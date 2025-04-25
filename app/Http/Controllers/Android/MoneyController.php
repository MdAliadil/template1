<?php

namespace App\Http\Controllers\Android;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Api;
use App\Model\Provider;
use App\Model\Mahabank;
use App\Model\Report;
use App\Model\Commission;
use App\Model\Packagecommission;
use App\User;
use Carbon\Carbon;

class MoneyController extends Controller
{
    protected $api;
    public function __construct()
    {
        $this->api = Api::where('code', 'dmt1')->first();
    }

    public function transaction(Request $post)
    {
        if(!$this->api || $this->api->status == 0){
            return response()->json(['statuscode' => "ERR", "message" => "Money Transfer Service Currently Down"]);
        }

        $rules = array(
            'apptoken' => 'required',
            'user_id'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = User::where('id', $post->user_id)->first();
        if(!$user){
            $output['statuscode'] = "ERR";
            $output['message'] = "User details not matched";
            return response()->json($output);
        }

        if (!\Myhelper::can('dmt1_service', $user->id)) {
            return response()->json(['statuscode' => "ERR", "message" => "Service Not Allowed"]);
        }

        switch ($post->type) {
            case 'getbank':
                $rules = array(
                    'type' => 'required'
                );
                break;

            case 'verification':
            case 'otp':
                $rules = array(
                    'type' => 'required',
                    'mobile' => 'required|numeric|digits:10'
                );
                break;

            case 'registration':
                $rules = array(
                    'type'   => 'required',
                    'mobile' => 'required|numeric|digits:10',
                    'fname'  => 'required|regex:/^[\pL\s\-]+$/u',
                    'otp'  => 'required',
                );
                break;

            case 'addbeneficiary':
                $rules = array(
                    'mobile'      => 'required|numeric|digits:10', 
                    'benebank'    => 'required', 
                    'beneifsc'    => "required", 
                    'beneaccount' => "required|numeric|digits_between:6,20", 
                    'benemobile'  => 'required|numeric|digits:10', 
                    'benename'    => "required|regex:/^[\pL\s\-]+$/u");
            break;

            case 'beneverify':
                $rules = array(
                    'mobile'      => 'required|numeric|digits:10',
                    'beneaccount' => 'required|numeric|digits_between:6,20', 
                    'benemobile'  => 'required|numeric|digits:10', 
                    'otp'         => 'required|numeric');
            break;

            case 'accountverification':
                $rules = array(
                    'mobile'      => 'required|numeric|digits:10', 
                    'benebank'    => 'required', 
                    'beneifsc'    => "required", 
                    'beneaccount' => "required|numeric|digits_between:6,20", 
                    'benemobile'  => 'required|numeric|digits:10', 
                    'benename'    => "required|regex:/^[\pL\s\-]+$/u",
                    'name'        => "required"
                );
            break;

            case 'transfer':
                $rules = array(
                    'name' => 'required',
                    'mobile' => 'required|numeric|digits:10',
                    'benebank' => 'required', 
                    'beneifsc' => "required", 
                    'beneaccount' => "required|numeric|digits_between:6,20", 
                    'benemobile' => 'required|numeric|digits:10', 
                    'benename' => "required",
                    'amount' => 'required|numeric|min:100|max:25000');
            break;
            
            default:
                return response()->json(['statuscode' => "ERR", "message" => "Bad Parameter Request"]);
                break;
        }

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $bcid   = \App\Model\PortalSetting::where('code', 'bcid')->first();
        $cpid   = \App\Model\PortalSetting::where('code', 'cpid')->first();

        if(isset($cpid->value)){
            $post['cpid'] = $cpid->value;
        }else{
            return response()->json(['statuscode' => 'ERR', 'message' => "CP id not mapped"], 400);
        }

        if(isset($bcid->value)){
            $post['bc_id'] = $bcid->value;
        }else{
            return response()->json(['statuscode' => 'ERR', 'message' => "Bc id not mapped"], 400);
        }

        if($post->type == "transfer"){
            $codes = ['dmt1', 'dmt2', 'dmt3', 'dmt4', 'dmt5'];
            $providerids = [];
            foreach ($codes as $value) {
                $providerids[] = Provider::where('recharge1', $value)->first(['id'])->id;
            }
            if($this->schememanager() == "admin"){
                $commission = Commission::where('scheme_id', $user->scheme_id)->whereIn('slab', $providerids)->get();
            }else{
                $commission = Packagecommission::where('scheme_id', $user->scheme_id)->whereIn('slab', $providerids)->get();
            }
            if(!$commission || sizeof($commission) < 5){
                return response()->json(['statuscode' => 'ERR', 'message' => "Money Transfer charges not set, contact administrator."], 400);
            }
        }

        $header = array("Content-Type: application/json");

        switch ($post->type) {
            case 'getbank':
                $banks = Mahabank::get();
                return response()->json(['statuscode' => "TXN", "message" => "Bank details fetched", 'data' => $banks]);
                break;

            case 'verification':
                $url = $this->api->url."AIRTEL/getairtelbenedetails";
                $parameter["bc_id"]  = $post->bc_id;
                $parameter["custno"] = $post->mobile;
                break;

            case 'otp':
                $url = $this->api->url."AIRTEL/airtelOTP";
                $parameter["bc_id"]  = $post->bc_id;
                $parameter["custno"] = $post->mobile;
                break;

            case 'registration':
                $circle = \DB::table('circles')->where('state', 'like', '%'.$user->state.'%')->first();
                
                if(!$circle || $user->pincode == '' || $user->address == ''){
                    return response()->json(['statuscode' => 'ERR', 'message' => "Please update your profile or contact administrator"]);
                }
                
                $url = $this->api->url."AIRTEL/apiCustRegistration";
                $name = explode(" ", $post->fname);
                $parameter["bc_id"]       = $post->bc_id;
                $parameter["custno"]      = $post->mobile;
                $parameter["cust_f_name"] = $name[0];
                $parameter["cust_l_name"] = isset($name[1]) ? $name[1] : 'kumar';
                $parameter["Dob"] = date("d-m")."-".rand(1980, 2000);
                $parameter["otp"] = $post->otp;
                $parameter["Address"] = $user->address;
                $parameter["pincode"] = $user->pincode;
                $parameter["StateCode"] = $circle->statecode;
                $parameter["usercode"]    = $post->cpid;
                $parameter["saltkey"] = $this->api->username;
                $parameter["secretkey"] = $this->api->password;
                break;

            case 'addbeneficiary':
                $url = $this->api->url."AIRTEL/airtelbeneadd";
                $parameter["custno"]    = $post->mobile;
                $parameter["bankname"]  = $post->benebank;
                $parameter["beneaccno"] = $post->beneaccount;
                $parameter["benemobile"]= $post->benemobile;
                $parameter["benename"]  = $post->benename;
                $parameter["ifsc"]      = $post->beneifsc;
                break;

            case 'beneverify':
                $url = $this->api->url."AIRTEL/verifybeneotp";
                $parameter["custno"] = $post->mobile;
                $parameter["otp"] = $post->otp;
                $parameter["beneaccno"] = $post->beneaccount;
                $parameter["benemobile"] = $post->benemobile;
                break;

            case 'accountverification':
                $url = $this->api->url."AIRTEL/VerifybeneApi";
                $post['amount'] = 1;
                $provider = Provider::where('recharge1', 'dmt1accverify')->first();
                $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $provider->id, $user->role->slug);
                $post['provider_id'] = $provider->id;
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode" => "IWB", 'message'=>'Low balance, kindly recharge your wallet.']);
                }

                $parameter["custno"]    = $post->mobile;
                $parameter["bankname"]  = $post->benebank;
                $parameter["beneaccno"] = $post->beneaccount;
                $parameter["benemobile"]= $post->benemobile;
                $parameter["benename"]  = $post->benename;
                $parameter["ifsc"]      = $post->beneifsc;
                $parameter['bc_id']     = $post->bc_id;
                $parameter["saltkey"]   = $this->api->username;
                $parameter["secretkey"] = $this->api->password;
                do {
                    $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
                $parameter["clientrefno"] = $post->txnid;
                break;

            case 'transfer':
                
                if ($this->pinCheck($post) == "fail") {
                    return response()->json(['statuscode' => "ERR", "message" => "Transaction Pin is incorrect"]);
                }
                return $this->transfer($post);
                break;
        }

        $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", 'App\Model\Report', '0');
        if($user->id == "489"){
            //dd([$url, $parameter , $result]);
        }
        if ($result['error'] || $result['response'] == "") {
            return response()->json(['statuscode' => "ERR", "message" => "Technical Error, contact service provider"]);
        }

        $response = json_decode($result['response']);
        switch ($post->type) {
            case 'verification':
                if(
                    isset($response->statuscode) && 
                    $response->statuscode == 001 || 
                    $response->statuscode == 003 || 
                    $response->statuscode == 111)
                {
                    $output['statuscode'] = "TXN";
                    $output['message']= "Transaction Successfull";
                    $output['name'] = $response->custfirstname." ".$response->custlastname;
                    $output['mobile']= $response->custmobile;
                    $output['totallimit']= $response->total_limit;
                    $output['usedlimit']= $response->used_limit;
                    $benedatas = [];
                    if(sizeof($response->Data) > 0){
                        foreach ($response->Data as $value) {
                            $benedata['beneid']   = $value->id;
                            $benedata['benename'] = $value->benename;
                            $benedata['beneaccount'] = $value->beneaccno;
                            $benedata['benemobile']  = $value->benemobile;
                            $benedata['benebank'] = $value->bankname;
                            $benedata['beneifsc'] = $value->ifsc;
                            $benedata['benebankid'] = $value->bankid;
                            $benedata['benestatus'] = $value->status;
                            $benedatas[] = $benedata;
                        }
                    }
                    $output['beneficiary'] = $benedatas;
                    
                }elseif(
                    $response->statuscode == 002 && 
                    $response->message == "No Customer found")
                {
                    $parameter["bc_id"] = $post->bc_id;
                    $parameter["custno"] = $post->mobile;
                    $url = $this->api->url."AIRTEL/airtelOTP";
                    $header = array("Content-Type: application/json");
                    \Myhelper::curl($url, "POST", json_encode($parameter), $header, "no");

                    $output['statuscode'] = "RNF";
                    $output['message']= "Customer Not Found";
                }else{
                    $output['statuscode'] = "ERR";
                    $output['message']= isset($response->message) ? $response->message : 'Transaction Error';
                }
                break;

            case 'otp':
                if(isset($response[0]->StatusCode) && $response[0]->StatusCode == 001){
                    $output['statuscode'] = 'TXN';
                    $output['message']= $response[0]->Message;
                }else{
                    $output['statuscode'] = 'ERR';
                    $output['message']= isset($response[0]->Message) ? $response[0]->Message : 'Transaction Error';
                }
                break;

            case 'registration':
                if(isset($response->StatusCode) && $response->StatusCode == 001){
                    $output['statuscode'] = 'TXN';
                    $output['message']= 'Transaction Successfull';
                }elseif(is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 001){
                    $output['statuscode'] = 'TXN';
                    $output['message']= 'Transaction Successfull';
                }elseif(is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 000){
                    $output['statuscode'] = 'ERR';
                    $output['message']= $response[0]->Message;
                }else{
                    $output['statuscode'] = 'ERR';
                    $output['message']= $response[0]->Message;
                }
                break;

            case 'addbeneficiary':
                if(isset($response->statuscode) && $response->statuscode == 001){
                    $output['statuscode'] = 'TXN';
                    $output['message']= 'Transaction Successfull';
                }elseif(is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 000){
                    $output['statuscode'] = 'ERR';
                    $output['message']= $response[0]->Message;
                }else{
                    $output['statuscode'] = 'ERR';
                    $output['message']= $response->message;
                }
                break;

            case 'beneverify':
                if(!is_array($response) && isset($response->StatusCode) && $response->StatusCode == 001){
                    $output['statuscode'] = 'TXN';
                    $output['message']= 'Transaction Successfull';
                }elseif(is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 001){
                    $output['statuscode'] = 'TXN';
                    $output['message']= 'Transaction Successfull';
                }elseif(is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 000){
                    $output['statuscode'] = 'ERR';
                    $output['message']= $response[0]->Message;
                }elseif(is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 003){
                    $output['statuscode'] = 'ERR';
                    $output['message']= $response[0]->Message;
                }else{
                    $output['statuscode'] = 'ERR';
                    $output['message']= $response->Message;
                }
                break;

            case 'accountverification':
                if(isset($response->statuscode) && $response->statuscode == 001 && isset($response->Data[0]->benename) && $response->Data[0]->benename != ""){
                    $insert = [
                        'api_id'      => $this->api->id,
                        'provider_id' => $post->provider_id,
                        'option1'     => $post->name,
                        'mobile'      => $post->mobile,
                        'number'      => $post->beneaccount,
                        'option2'     => isset($response->Data[0]->benename) ? $response->Data[0]->benename : $post->benename,
                        'option3'     => $post->benebank,
                        'option4'     => $post->beneifsc,
                        'txnid'       => $post->txnid,
                        'refno'       => isset($response->Data[0]->rrn) ? $response->Data[0]->rrn : "none",
                        'amount'      => $post->amount,
                        'charge'      => $post->charge,
                        'remark'      => "Money Transfer",
                        'status'      => 'success',
                        'user_id'     => $user->id,
                        'credit_by'   => $user->id,
                        'product'     => 'dmt',
                        'balance'     => $user->mainwallet,
                        'description' => $post->benemobile,
                        'via'         => 'app',
                        'trans_type' => 'debit'
                    ];

                    User::where('id', $post->user_id)->decrement('mainwallet', $post->charge + $post->amount);
                    $report = Report::create($insert);
                    $output['statuscode']   = 'TXN';
                    $output['message']  = 'Transaction Successfull';
                    $output['benename'] = $response->Data[0]->benename;
                }elseif(is_array($response) && isset($response[0]->statuscode) && $response[0]->statuscode == 000){
                    $output['statuscode'] = 'ERR';
                    $output['message']= $response[0]->Message;
                }else{
                    $output['statuscode'] = 'ERR';
                    $output['message']= $response->message;
                }
                break;
        }
        return response()->json($output);
    }

    public function transfer($post)
    {
        $totalamount = $post->amount;
        $url = $this->api->url."AIRTEL/Apipaymode";
        $parameter['bc_id'] = $post->bc_id;
        $parameter["saltkey"] = $this->api->username;
        $parameter["secretkey"] = $this->api->password;

        $amount = $post->amount;
        for ($i=1; $i < 6; $i++) { 
            if(5000*($i-1) <= $amount  && $amount <= 5000*$i){
                if($amount == 5000*$i){
                    $n = $i;
                }else{
                    $n = $i-1;
                    $x = $amount - $n*5000;
                }
                break;
            }
        }

        $amounts = array_fill(0,$n,5000);
        if(isset($x)){
            array_push($amounts , $x);
        }

        foreach ($amounts as $amount) {
            if ($totalamount < $amount) {
                continue;
            }

            $outputs['statuscode'] = "TXN";
            $post['amount'] = $amount;
            $user = User::where('id', $post->user_id)->first();
            $post['charge'] = $this->getCharge($post->amount);
            if($user->mainwallet < $post->amount + $post->charge){
                $outputs['data'][] = array(
                    'amount'  => $amount,
                    'statuscode'  => 'IWB',
                    'status'  => 'Insufficient Wallet Balance',
                    'message' => 'Insufficient Wallet Balance',
                );
            }else{
                
                do {
                    $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

                if($post->amount >= 100 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'dmt1')->first();
                }elseif($amount>1000 && $amount<=2000){
                    $provider = Provider::where('recharge1', 'dmt2')->first();
                }elseif($amount>2000 && $amount<=3000){
                    $provider = Provider::where('recharge1', 'dmt3')->first();
                }elseif($amount>3000 && $amount<=4000){
                    $provider = Provider::where('recharge1', 'dmt4')->first();
                }else{
                    $provider = Provider::where('recharge1', 'dmt5')->first();
                }
                
                $post['provider_id'] = $provider->id;
                $bank = Mahabank::where('bankid', $post->benebank)->first();
                $insert = [
                    'api_id'      => $this->api->id,
                    'provider_id' => $post->provider_id,
                    'option1'     => $post->name,
                    'mobile'      => $post->mobile,
                    'number'      => $post->beneaccount,
                    'option2'     => $post->benename,
                    'option3'     => $bank->bankname,
                    'option4'     => $post->beneifsc,
                    'txnid'       => $post->txnid,
                    'amount'      => $post->amount,
                    'charge'      => $post->charge,
                    'remark'      => "Money Transfer",
                    'status'      => 'pending',
                    'user_id'     => $user->id,
                    'credit_by'   => $user->id,
                    'product'     => 'dmt',
                    'via'         => 'app',
                    'balance'     => $user->mainwallet,
                    'description' => $post->benemobile,
                    'trans_type'  => 'debit'
                ];

                $previousrecharge = Report::where('number', $post->beneaccount)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subSeconds(1)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                if($previousrecharge == 0){
                    $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);
                    if(!$transaction){
                        $outputs['data'][] = array(
                            'amount' => $amount,
                            'statuscode' => 'TXF',
                            'status' => 'Transaction Failed',
                            'message' => 'Transaction Failed'
                        );
                    }else{
                        $totalamount = $totalamount - $amount;
                        $report = Report::create($insert);
                        $post['service'] = $provider->type;
                        $post['reportid'] = $report->id;
                        $post['amount'] = $amount;
                        $parameter["custno"]    = $post->mobile;
                        $parameter["bankname"]  = $post->benebank;
                        $parameter["beneaccno"] = $post->beneaccount;
                        $parameter["benemobile"]= $post->benemobile;
                        $parameter["benename"]  = $post->benename;
                        $parameter["ifsc"]      = $post->beneifsc;
                        $parameter['amount']    = $amount;
                        $parameter["clientrefno"] = $post->txnid;
                        $header = array("Content-Type: application/json");

                        if(env('APP_ENV') == "server"){
                            $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", 'App\Model\Report', $post->txnid);
                        }else{
                            $result['error']    = true;
                            $result['response'] = '';
                        }

                        if($result['error'] || $result['response'] == ''){
                            $result['response'] = json_encode([
                                "message"       =>  "Pending",
                                "statuscode"    =>  "001",
                                "availlimit"    =>  "0",
                                "total_limit"   =>  "0",
                                "used_limit"    =>  "0",
                                "Data"          =>  [
                                    [   "fesessionid"   =>  "CP1801861S131436",
                                        "tranid"        =>  "pending",
                                        "rrn"           =>  "pending",
                                        "externalrefno" =>  "MH357381218131436",
                                        "amount"        =>  "0",
                                        "responsetimestamp" =>  "0",
                                        "benename"          =>  "",
                                        "messagetext"       =>  "Success",
                                        "code"              =>  "0",
                                        "errorcode"         =>  "1114",
                                        "mahatxnfee"        =>  "10.00"
                                    ]
                                ]
                            ]);
                        }

                        $response = json_decode($result['response']);
                        $report = Report::where('id', $post->reportid)->first();

                        if(isset($response->Data[0]) && $response->Data[0]->errorcode == 0){
                            $charge = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            $post['gst'] = 0;
                            User::where('id', $post->user_id)->increment('mainwallet', $report->charge - $post->gst - $charge);
                            Report::where('id', $post->reportid)->update([
                                'status'=> "success",
                                'payid' => (isset($response->Data[0]->externalrefno))?$response->Data[0]->externalrefno : "Pending" ,
                                'refno' => (isset($response->Data[0]->rrn))?$response->Data[0]->rrn : "Pending",
                                'remark'=> (isset($response->Data[0]->fesessionid))?$response->Data[0]->fesessionid : "Pending",
                                'gst'   => $post->gst,
                                'profit'=> $report->charge - $post->gst - $charge
                            ]);
                            \Myhelper::commission($report);
                            $output = ['amount' => $amount, 'status'=> 'success', 'message'=> 'success', "rrn" => (isset($response->Data[0]->rrn))? $response->Data[0]->rrn : "Success"];
                        }elseif(isset($response->Data[0]->errorcode) && in_array($response->Data[0]->errorcode, ['8','1','10','86','3','52','101','M5','M3','20','1076','100','608','BL101','96','9001','M2','12','3403','PM0405','999001','PM0640','333998','M0','1206','1077','M4','1075','9001','93097','911','PM0684','54','3','51','13','14','92','94','4','1515','302','M7','1616','99','801','22','M1','AS_111','9001','999001','333998','934210','9006','801','911','302','9001','912','010'])){
                            User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                            if(isset($response->Data[0]) && isset($response->Data[0]->messagetext)){
                                $refno = $response->Data[0]->messagetext;
                            }elseif (isset($response->message)) {
                                $refno = $response->message;
                            }else{
                                $refno = 'Failed';
                            }
        
                            Report::where('id', $post->reportid)->update([
                                'status'=> 'failed',
                                'refno' => $refno,
                            ]);
                            try {
                                if(isset($response->message) && $response->message == "You have Insufficent balance"){
                                    $refno = "Service Down for some time";
                                }
                            } catch (\Exception $th) {}
                            $output = ['amount' => $amount, 'status'=> 'failed', 'message'=> 'failed', 'rrn'=> $refno];
                        }elseif(
                            isset($response->message) && (
                                $response->message == "Unexpected character encountered while parsing value: <. Path " ||
                                $response->message == "You have Insufficent balance" ||
                                $response->message == "Invalid IFSC code" ||
                                $response->message == "Invalid Beneficiary details" ||
                                $response->message == "Beneficiary is not verified. Please verify"
                            )
                        ){
                            User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                            if(isset($response->Data[0]) && isset($response->Data[0]->messagetext)){
                                $refno = $response->Data[0]->messagetext;
                            }elseif (isset($response->message)) {
                                $refno = $response->message;
                            }else{
                                $refno = 'Failed';
                            }

                            Report::where('id', $post->reportid)->update([
                                'status'=> 'failed',
                                'refno' => $refno,
                            ]);
                            try {
                                if(isset($response->message) && $response->message == "You have Insufficent balance"){
                                    $refno = "Service Down for some time";
                                }
                            } catch (\Exception $th) {}
                            $output = ['amount' => $amount, 'status'=> 'failed', 'message'=> 'failed', 'rrn'=> $refno];
                        }else{
                            $charge = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            $post['gst'] = 0;
                            User::where('id', $post->user_id)->increment('mainwallet', $report->charge - $post->gst - $charge);
                            Report::where('id', $post->reportid)->update([
                                'status'=> "pending",
                                'payid' => (isset($response->Data[0]->externalrefno))?$response->Data[0]->externalrefno : "Pending" ,
                                'refno' => (isset($response->Data[0]->rrn))?$response->Data[0]->rrn : "Pending",
                                'remark'=> (isset($response->Data[0]->fesessionid))?$response->Data[0]->fesessionid : "Pending",
                                'gst'   => $post->gst,
                                'profit'=> $report->charge - $post->gst - $charge
                            ]);
                            \Myhelper::commission($report);
                            $output = ['amount' => $amount, 'status'=> 'pending', 'message'=> 'pending', "rrn" => (isset($response->Data[0]->rrn))?$response->Data[0]->rrn : "Pending"];
                        }

                        $outputs['data'][] = $output;
                    }
                }else{
                    $outputs['data'][] = array(
                            'amount' => $amount,
                            'statuscode' => 'TXF',
                            'status' => 'Transaction Failed, Same Transaction Repeat',
                            'message' => 'Transaction Failed, Same Transaction Repeat'
                        );
                }
            }
            sleep(1);
        }
        return response()->json($outputs);
    }

    public function getCommission($scheme, $slab, $amount)
    {
        if($amount < 1000){
            $amount = 1000;
        }
        $userslab = Commission::where('scheme_id', $scheme)->where('product', 'money')->where('slab', $slab)->first();
        if($userslab){
            if ($userslab->type == "percent") {
                $usercharge = $amount * $userslab->value / 100;
            }else{
                $usercharge = $userslab->value;
            }
        }else{
            $usercharge = 7;
        }

        return $usercharge;
    }

    public function getCharge($amount)
    {
        if($amount < 1000){
            return 10;
        }else{
            return $amount*1/100;
        }
    }

    public function getGst($amount)
    {
        return $amount*100/118;
    }

    public function getTds($amount)
    {
        return $amount*5/100;
    }
}
