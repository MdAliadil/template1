<?php

namespace App\Http\Controllers\Android;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\Aepsfundrequest;
use App\Model\Fundreport;
use App\Model\Fundbank;
use App\Model\Paymode;
use App\Model\PortalSetting;
use App\Model\Provider;
use App\Model\Aepsreport;
use App\Model\Report;
use App\Model\Api;
use App\Classes\PaytmChecksum;
use Carbon\Carbon;

class FundController extends Controller
{
    public $fundapi, $admin;

    public function __construct()
    {
        $this->fundapi = Api::where('code', 'fund')->first();
        $this->admin = User::whereHas('role', function ($q){
            $q->where('slug', 'admin');
        })->first();

    }
    
    public function transaction(Request $post)
    {
    	$rules = array(
            'apptoken' => 'required',
            'type' 	   => 'required',
            'user_id'  => 'required|numeric',
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

        switch ($post->type) {
            case 'bank':
                
                if ($this->pinCheck($post) == "fail") {
                    return response()->json(['statuscode' => "ERR", "message" => "Transaction Pin is incorrect"]);
                }
                
                $banksettlementtype = $this->banksettlementtype();
                $bankpayoutapi      = $this->bankpayoutapi();
                if($banksettlementtype == "down"){
                    return response()->json(['statuscode' => "ERR", 'message' => "Aeps Settlement Down For Sometime"],400);
                }

                $user = User::where('id', $post->user_id)->first();
                
                if($user->id != "2"){
                    //return response()->json(['statuscode' => "ERR", 'message' => "Aeps Settlement Down For Sometime"],400);
                }
                
                if(!\Myhelper::can('aeps_fund_request', $user->id)){
                    return response()->json(['statuscode' => "ERR", 'message' => "Permission not allowed"],400);
                }

                $rules = array(
                    'amount'    => 'required|numeric|min:10',
                    'account'   => 'sometimes|required',
                    'bank'      => 'sometimes|required',
                    'ifsc'      => 'sometimes|required'
                );
                
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }

                if($user->account == '' && $user->bank == '' && $user->ifsc == ''){
                    User::where('id', $user->id)->update(['account' => $post->account, 'bank' => $post->bank, 'ifsc'=>$post->ifsc]);
                }elseif($user->account2 == '' && $user->bank2 == '' && $user->ifsc2 == ''){
                    User::where('id',$user->id)->update(['account2' => $post->account, 'bank2' => $post->bank, 'ifsc2'=>$post->ifsc]);
                }elseif($user->account3 == '' && $user->bank3 == '' && $user->ifsc3 == ''){
                    User::where('id',$user->id)->update(['account3' => $post->account, 'bank3' => $post->bank, 'ifsc3'=>$post->ifsc]);
                }
                
                $settlerequest = Aepsfundrequest::where('user_id', $user->id)->where('status', 'pending')->count();
                if($settlerequest > 0){
                    return response()->json(['statuscode' => "ERR", 'message' => "One request is already submitted"], 400);
                }

                $post['charge'] = 0;
                if($post->amount <= 25000){
                    $post['charge'] = $this->impschargeupto25();
                }

                if($post->amount > 25000){
                    $post['charge'] = $this->impschargeabove25();
                }

                if($user->mainwallet < $post->amount + $post->charge){
                   return response()->json(['statuscode'=>"ERR", 'message' => "Low aeps balance to make this request"],200);
                }

                if($banksettlementtype == "auto"){

                    $previousrecharge = Aepsfundrequest::where('account', $post->account)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subSeconds(30)->format('Y-m-d H:i:s'), Carbon::now()->addSeconds(30)->format('Y-m-d H:i:s')])->count();
                    if($previousrecharge){
                        return response()->json(['statuscode'=>"ERR", 'message' => "Transaction Allowed After 1 Min."]);
                    } 

                    if($bankpayoutapi == "secure"){
                        $api = Api::where('code', 'psettlement')->first();
                    }else{
                        $api = Api::where('code', 'paytmsettlement')->first();
                    }

                    do {
                        $post['payoutid'] = $this->transcode().rand(111111111111, 999999999999);
                    } while (Aepsfundrequest::where("payoutid", "=", $post->payoutid)->first() instanceof Aepsfundrequest);

                    $post['status']   = "pending";
                    $post['pay_type'] = "payout";
                    $post['payoutid'] = $post->payoutid;
                    $post['payoutref']= $post->payoutid;
                    $post['create_time']= Carbon::now()->toDateTimeString();
                    try {
                        $aepsrequest = Aepsfundrequest::create($post->all());
                    } catch (\Exception $e) {
                        return response()->json(['statuscode' => "ERR", 'message' => "Duplicate Transaction Not Allowed, Please Check Transaction History"]);
                    }
                    
                    $aepsreports['api_id'] = $api->id;
                    $aepsreports['payid']  = $aepsrequest->id;
                    $aepsreports['mobile'] = $user->mobile;
                    $aepsreports['refno']  = "success";
                    $aepsreports['aadhar'] = $post->account;
                    $aepsreports['amount'] = $post->amount;
                    $aepsreports['charge'] = $post->charge;
                    $aepsreports['bank']   = $post->bank."(".$post->ifsc.")";
                    $aepsreports['txnid']  = $post->payoutid;
                    $aepsreports['user_id']= $user->id;
                    $aepsreports['credited_by'] = $this->admin->id;
                    $aepsreports['balance']     = $user->mainwallet;
                    $aepsreports['type']        = "debit";
                    $aepsreports['mode']        = $post->mode;
                    $aepsreports['transtype']   = 'fund';
                    $aepsreports['status'] = 'success';
                    $aepsreports['remark'] = "Bank Settlement";

                    User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                    $myaepsreport = Report::create($aepsreports);
                    

                    if($bankpayoutapi == "secure"){
                        $url = $api->url;
                        $parameter = [
                            "apitxnid" => $post->payoutid,
                            "amount"   => $post->amount, 
                            "account"  => $post->account,
                            "name"     => $user->name,
                            "bank"     => $post->bank,
                            "ifsc"     => $post->ifsc,
                            "ip"       => $post->ip(),
                            "token"    => $api->username,
                            'callback' => url('api/callback/update/payout')
                        ];
                        $header = array("Content-Type: application/json");
    
                        if(env('APP_ENV') != "local"){
                            $result = \Myhelper::curl($url, 'POST', json_encode($parameter), $header, 'yes', '\App\Model\Aepsfundrequest', $post->payoutid);
                        }else{
                            $result = [
                                'error'    => true,
                                'response' => ''
                            ];
                        }
    
                        if($result['response'] == ''){
                            return response()->json(['statuscode' => "TXN", "message" => "Aeps fund request submitted successfully", "txnid" => $aepsrequest->id]);
                        }
    
                        $response = json_decode($result['response']);
                        if(isset($response->status) && in_array($response->status, ['TXN', 'TUP'])){
                            Aepsfundrequest::where('id', $aepsrequest->id)->update(['status' => "approved", "payoutref" => $response->rrn]);
                            return response()->json(['statuscode' => "TXN", "message" => "Aeps fund request submitted successfully", "txnid" => $aepsrequest->id]);
                        }elseif(isset($response->status) && in_array($response->status, ['ERR', 'TXF'])){
                            User::where('id', $aepsreports['user_id'])->increment('mainwallet', $aepsreports['amount']+$aepsreports['charge']);
                            Report::where('id', $myaepsreport->id)->update(['status' => "failed", "refno" => isset($response->rrn) ? $response->rrn : $response->message]);
    
                            Aepsfundrequest::where('id', $aepsrequest->id)->update(['status' => "rejected"]);
                            return response()->json(['statuscode' => "TXF", "message" => $response->message]);
                        }else{
                            Aepsfundrequest::where('id', $aepsrequest->id)->update(['status' => "pending"]);
                            return response()->json(['statuscode' => "TUP", "message" => "Transaction Under Pending"]);
                        }
                    }else{
                        $paytmParams = array();
                        $paytmParams["subwalletGuid"]      = $api->password;
                        $paytmParams["orderId"]            = $post->payoutid;
                        $paytmParams["beneficiaryAccount"] = $post->account;
                        $paytmParams["beneficiaryIFSC"]    = $post->ifsc;
                        $paytmParams["amount"]             = $post->amount;
                        $paytmParams["transferMode"]       = $post->mode;
                        $paytmParams["purpose"]            = "OTHERS";
                        $paytmParams["callbackUrl"]        = url('api/callback/update/ppayout');
                        $paytmParams["date"]               = Carbon::now()->format('Y-m-d');
                        $paytmParams["MID"]                = $api->username;
                        
                        
                        $url = $api->url;
                        $paytmChecksum = PaytmChecksum::generateSignature($paytmParams, $api->optional1);
                        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
                        $isVerifySignature = PaytmChecksum::verifySignature($paytmParams, $api->optional1, $paytmChecksum);
                        $checksum = PaytmChecksum::generateSignature($post_data, $api->optional1);
                        $header   = array("Content-Type: application/json", "x-mid: " . $api->username, "x-checksum: " . $checksum);
                        
                        $response = \Myhelper::curl($url, 'POST', $post_data, $header, 'yes', 'PaytmPayout', $post->payoutid);
                        $result=json_decode($response['response']);
                        
                        if($response['response'] == ''){
                            return response()->json(['statuscode' => "TXN", "message" => "Aeps fund request submitted successfully", "txnid" => $aepsrequest->id]);
                        }
                        
                        if(isset($result->status) && $result->status=='ACCEPTED'){
                            Aepsfundrequest::updateOrCreate(['id'=> $aepsrequest->id], ['status' => "approved"]);
                            return response()->json(['statuscode' => "TXN", "message" => "Aeps fund request submitted successfully", "txnid" => $aepsrequest->id]);
                        }else{
                            User::where('id', $aepsreports['user_id'])->increment('mainwallet', $aepsreports['amount']+$aepsreports['charge']);
                            Report::where('id', $myaepsreport->id)->update(['status' => "failed"]);
    
                            Aepsfundrequest::where('id', $aepsrequest->id)->update(['status' => "rejected", 'payoutref' => $response->statusMessage]);
                            return response()->json(['statuscode'=>'TXF', 'message' => $response->statusMessage]);
                        }
                    }
                }else{
                    $post['pay_type'] = "manual";
                    $aepsrequest = Aepsfundrequest::create($post->all());
                }

                if($aepsrequest){
                    return response()->json(['statuscode' => "TXN", "message" => "Aeps fund request submitted successfully", "txnid" => $aepsrequest->id]);
                }else{
                    return response()->json(['statuscode'=>"ERR", 'message' => "Something went wrong."]);
                }
                break;

            case 'wallet':
                
                if ($this->pinCheck($post) == "fail") {
                    return response()->json(['statuscode' => "ERR", "message" => "Transaction Pin is incorrect"]);
                }
                
                $settlementtype = $this->settlementtype();

                if($settlementtype == "down"){
                    return response()->json(['statuscode'=>"ERR", 'message' => "Aeps Settlement Down For Sometime"],400);
                }

                $rules = array(
                    'amount'    => 'required|numeric|min:1',
                );
        
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $user = User::where('id',$post->user_id)->first();

                if(!\Myhelper::can('aeps_fund_request', $user->id)){
                    return response()->json(['statuscode'=>"ERR", 'message' => "Permission not allowed"],400);
                }
                
                $myrequest = Aepsfundrequest::where('user_id', $user->id)->where('status', 'pending')->count();
                if($myrequest > 0){
                    return response()->json(['statuscode'=>"ERR", 'message' => "One request is already submitted"], 400);
                }

                if($user->mainwallet < $post->amount){
                    return response()->json(['statuscode'=>"ERR", 'message' => "Low aeps balance to make this request"], 200);
                }

                if($settlementtype == "auto"){
                    $previousrecharge = Aepsfundrequest::where('type', $post->type)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                    if($previousrecharge > 0){
                        return response()->json(['statuscode'=>"ERR", 'message' => "Transaction Allowed After 5 Min."]);
                    }

                    $post['status'] = "approved";
                    $load = Aepsfundrequest::create($post->all());
                    $payee = User::where('id', $user->id)->first();
                    User::where('id', $payee->id)->decrement('mainwallet', $post->amount);
                    $inserts = [
                        "mobile"  => $payee->mobile,
                        "amount"  => $post->amount,
                        "bank"    => $payee->bank,
                        'txnid'   => date('ymdhis'),
                        'refno'   => $post->refno,
                        "user_id" => $payee->id,
                        "credited_by" => $user->id,
                        "balance"     => $payee->mainwallet,
                        'type'        => "debit",
                        'transtype'   => 'fund',
                        'status'      => 'success',
                        'remark'      => "Move To Wallet Request",
                        'payid'       => "Wallet Transfer Request",
                        'aadhar'      => $payee->account
                    ];

                    Report::create($inserts);

                    if($post->type == "wallet"){
                        $provide = Provider::where('recharge1', 'aepsfund')->first();
                        User::where('id', $payee->id)->increment('mainwallet', $post->amount);
                        $insert = [
                            'number' => $payee->mobile,
                            'mobile' => $payee->mobile,
                            'provider_id' => $provide->id,
                            'api_id' => $this->fundapi->id,
                            'amount' => $post->amount,
                            'charge' => '0.00',
                            'profit' => '0.00',
                            'gst' => '0.00',
                            'tds' => '0.00',
                            'txnid' => $load->id,
                            'payid' => $load->id,
                            'refno' => $post->refno,
                            'description' =>  "Aeps Fund Recieved",
                            'remark' => $post->remark,
                            'option1' => $payee->name,
                            'status' => 'success',
                            'user_id' => $payee->id,
                            'credit_by' => $payee->id,
                            'rtype' => 'main',
                            'via' => 'portal',
                            'balance' => $payee->mainwallet,
                            'trans_type' => 'credit',
                            'product' => "fund request"
                        ];

                        Report::create($insert);
                    }
                }else{
                    $load = Aepsfundrequest::create($post->all());
                }

                if($load){
                    return response()->json(['statuscode' => "TXN", "message" => "Aeps fund request submitted successfully", "txnid" => $load->id],200);
                }else{
                    return response()->json(['statuscode' => "ERR", 'message' => "Transaction Failed"]);
                }
                break;
                
             case 'matmbank':
                $banksettlementtype = $this->banksettlementtype();

                if($banksettlementtype == "down"){
                    return response()->json(['statuscode' => "ERR", 'message' => "Aeps Settlement Down For Sometime"],400);
                }

                $user = User::where('id', $post->user_id)->first();
                
                if($user->id != "2"){
                    //return response()->json(['statuscode' => "ERR", 'message' => "Aeps Settlement Down For Sometime"],400);
                }
                
                if(!\Myhelper::can('aeps_fund_request', $user->id)){
                    return response()->json(['statuscode' => "ERR", 'message' => "Permission not allowed"],400);
                }

                if($user->account == '' && $user->bank == '' && $user->ifsc == ''){
                    $rules = array(
                        'amount'    => 'required|numeric|min:10',
                        'account'   => 'sometimes|required',
                        'bank'      => 'sometimes|required',
                        'ifsc'      => 'sometimes|required'
                    );
                }else{
                    $rules = array(
                        'amount'    => 'required|numeric|min:10'
                    );

                    $post['account'] = $user->account;
                    $post['bank']    = $user->bank;
                    $post['ifsc']    = $user->ifsc;
                }

                $validate = \Myhelper::FormValidator($rules,$post);
                if($validate != "no"){
                    return $validate;
                }

                if($user->account == '' && $user->bank == '' && $user->ifsc == ''){
                    User::where('id',$user->id)->update(['account' => $post->account, 'bank' => $post->bank, 'ifsc'=>$post->ifsc]);
                }

                $settlerequest = Microatmfundrequest::where('user_id', $user->id)->where('status', 'pending')->count();
                if($settlerequest > 0){
                    return response()->json(['statuscode' => "ERR", 'message' => "One request is already submitted"], 400);
                }

                $post['charge'] = 0;
                if($post->mode == "IMPS" && $post->amount <= 25000){
                    $post['charge'] = $this->impschargeupto25();
                }

                if($post->mode == "IMPS" && $post->amount > 25000){
                    $post['charge'] = $this->impschargeabove25();
                }

                if($post->mode == "NEFT"){
                    $post['charge'] = $this->neftcharge();
                }

                if($user->mainwallet -$this->aepslocked() < $post->amount + $post->charge){
                    return response()->json(['statuscode'=>"ERR", 'message' => "Low aeps balance to make this request"],200);
                }

                if($post->mode == "IMPS" && $banksettlementtype == "auto"){

                    $previousrecharge = Microatmfundrequest::where('account', $post->account)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subSeconds(30)->format('Y-m-d H:i:s'), Carbon::now()->addSeconds(30)->format('Y-m-d H:i:s')])->count();
                    if($previousrecharge){
                        return response()->json(['statuscode'=>"ERR", 'message' => "Transaction Allowed After 1 Min."]);
                    } 

                    $api = Api::where('code', 'psettlement')->first();

                    do {
                        $post['payoutid'] = $this->transcode().rand(111111111111, 999999999999);
                    } while (Microatmfundrequest::where("payoutid", "=", $post->payoutid)->first() instanceof Microatmfundrequest);

                    $post['status']   = "pending";
                    $post['pay_type'] = "payout";
                    $post['payoutid'] = $post->payoutid;
                    $post['payoutref']= $post->payoutid;
                    $post['create_time']= Carbon::now()->toDateTimeString();
                    try {
                        $aepsrequest = Microatmfundrequest::create($post->all());
                    } catch (\Exception $e) {
                        return response()->json(['statuscode' => "ERR", 'message' => "Duplicate Transaction Not Allowed, Please Check Transaction History"]);
                    }
                    
                    $aepsreports['api_id'] = $api->id;
                    $aepsreports['payid']  = $aepsrequest->id;
                    $aepsreports['mobile'] = $user->mobile;
                    $aepsreports['refno']  = "success";
                    $aepsreports['aadhar'] = $post->account;
                    $aepsreports['amount'] = $post->amount;
                    $aepsreports['charge'] = $post->charge;
                    $aepsreports['bank']   = $post->bank."(".$post->ifsc.")";
                    $aepsreports['txnid']  = $post->payoutid;
                    $aepsreports['user_id']= $user->id;
                    $aepsreports['credited_by'] = $this->admin->id;
                    $aepsreports['balance']     = $user->mainwallet;
                    $aepsreports['type']        = "debit";
                    $aepsreports['transtype']   = 'fund';
                    $aepsreports['status'] = 'success';
                    $aepsreports['remark'] = "Bank Settlement";

                    User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                    $myaepsreport = Microatmreport::create($aepsreports);
                    $url = $api->url;

                    $parameter = [
                        "apitxnid" => $post->payoutid,
                        "amount"   => $post->amount, 
                        "account"  => $post->account,
                        "name"     => $user->name,
                        "bank"     => $post->bank,
                        "ifsc"     => $post->ifsc,
                        "ip"       => $post->ip(),
                        "token"    => $api->username,
                        'callback' => url('api/callback/update/payout')
                    ];
                    $header = array("Content-Type: application/json");

                    if(env('APP_ENV') != "local"){
                        $result = \Myhelper::curl($url, 'POST', json_encode($parameter), $header, 'yes', '\App\Model\Aepsfundrequest', $post->payoutid);
                    }else{
                        $result = [
                            'error'    => true,
                            'response' => ''
                        ];
                    }

                    if($result['response'] == ''){
                        return response()->json(['status'=> "success"]);
                    }

                    $response = json_decode($result['response']);
                    if(isset($response->status) && in_array($response->status, ['TXN', 'TUP'])){
                        Microatmfundrequest::where('id', $aepsrequest->id)->update(['status' => "approved", "payoutref" => $response->rrn]);
                        return response()->json(['statuscode' => "TXN", "message" => "Aeps fund request submitted successfully", "txnid" => $aepsrequest->id],200);
                    }elseif(isset($response->status) && in_array($response->status, ['ERR', 'TXF'])){
                        User::where('id', $aepsreports['user_id'])->increment('mainwallet', $aepsreports['amount']+$aepsreports['charge']);
                        Microatmreport::where('id', $myaepsreport->id)->update(['status' => "failed", "refno" => isset($response->rrn) ? $response->rrn : $response->message]);

                        Microatmfundrequest::where('id', $aepsrequest->id)->update(['status' => "rejected"]);
                        return response()->json(['statuscode' => "TXF", "message" => $response->message], 400);
                    }else{
                        Microatmfundrequest::where('id', $aepsrequest->id)->update(['status' => "pending"]);
                        return response()->json(['statuscode' => "TUP", "message" => "Transaction Under Pending"]);
                    }
                }else{
                    $post['pay_type'] = "manual";
                    $aepsrequest = Microatmfundrequest::create($post->all());
                }

                if($aepsrequest){
                    return response()->json(['statuscode' => "TXN", "message" => "Aeps fund request submitted successfully", "txnid" => $aepsrequest->id],200);
                }else{
                    return response()->json(['statuscode'=>"ERR", 'message' => "Something went wrong."]);
                }
                break;

            case 'matmwallet':
                $settlementtype = $this->settlementtype();

                if($settlementtype == "down"){
                    return response()->json(['statuscode'=>"ERR", 'message' => "Aeps Settlement Down For Sometime"],400);
                }

                $rules = array(
                    'amount'    => 'required|numeric|min:1',
                );
        
                $validate = \Myhelper::FormValidator($rules, $post);
                if($validate != "no"){
                    return $validate;
                }

                $user = User::where('id',$post->user_id)->first();

                if(!\Myhelper::can('aeps_fund_request', $user->id)){
                    return response()->json(['statuscode'=>"ERR", 'message' => "Permission not allowed"]);
                }
                
                $myrequest = Microatmfundrequest::where('user_id', $user->id)->where('status', 'pending')->count();
                if($myrequest > 0){
                    return response()->json(['statuscode'=>"ERR", 'message' => "One request is already submitted"]);
                }

                if($user->mainwallet - $this->aepslocked() < $post->amount){
                    return response()->json(['statuscode'=>"ERR", 'message' => "Low aeps balance to make this request"],200);
                }

                if($settlementtype == "auto"){
                    $previousrecharge = Microatmfundrequest::where('type', $post->type)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                    if($previousrecharge > 0){
                        return response()->json(['statuscode'=>"ERR", 'message' => "Transaction Allowed After 5 Min."]);
                    }

                    $post['status'] = "approved";
                    $load = Microatmfundrequest::create($post->all());
                    $payee = User::where('id', $user->id)->first();
                    User::where('id', $payee->id)->decrement('mainwallet', $post->amount);
                    $inserts = [
                        "mobile"  => $payee->mobile,
                        "amount"  => $post->amount,
                        "bank"    => $payee->bank,
                        'txnid'   => date('ymdhis'),
                        'refno'   => $post->refno,
                        "user_id" => $payee->id,
                        "credited_by" => $user->id,
                        "balance"     => $payee->mainwallet,
                        'type'        => "debit",
                        'transtype'   => 'fund',
                        'status'      => 'success',
                        'remark'      => "Move To Wallet Request",
                        'payid'       => "Wallet Transfer Request",
                        'aadhar'      => $payee->account
                    ];

                    Microatmreport::create($inserts);

                    if($post->type == "wallet"){
                        $provide = Provider::where('recharge1', 'aepsfund')->first();
                        User::where('id', $payee->id)->increment('mainwallet', $post->amount);
                        $insert = [
                            'number' => $payee->mobile,
                            'mobile' => $payee->mobile,
                            'provider_id' => $provide->id,
                            'api_id' => $this->fundapi->id,
                            'amount' => $post->amount,
                            'charge' => '0.00',
                            'profit' => '0.00',
                            'gst' => '0.00',
                            'tds' => '0.00',
                            'txnid' => $load->id,
                            'payid' => $load->id,
                            'refno' => $post->refno,
                            'description' =>  "Aeps Fund Recieved",
                            'remark' => $post->remark,
                            'option1' => $payee->name,
                            'status' => 'success',
                            'user_id' => $payee->id,
                            'credit_by' => $payee->id,
                            'rtype' => 'main',
                            'via' => 'portal',
                            'balance' => $payee->mainwallet,
                            'trans_type' => 'credit',
                            'product' => "fund request"
                        ];

                        Report::create($insert);
                    }
                }else{
                    $load = Microatmfundrequest::create($post->all());
                }

                if($load){
                    return response()->json(['statuscode' => "TXN", "message" => "Aeps fund request submitted successfully", "txnid" => $load->id],200);
                }else{
                    return response()->json(['statuscode' => "ERR", 'message' => "Transaction Failed"]);
                }
                break;
    

            case 'request':
                if(!\Myhelper::can('fund_request', $post->user_id)){
                    return response()->json(['statuscode' => "ERR", "message" => "Permission not allowed"]);
                }

                $rules = array(
                    'fundbank_id'    => 'required|numeric',
                    'paymode'    => 'required',
                    'amount'    => 'required|numeric|min:100',
                    'ref_no'    => 'required|unique:fundreports,ref_no',
                    'paydate'    => 'required',
                    'apptoken'    => 'required'
                );
        
                $validate = \Myhelper::FormValidator($rules, $post);
                if($validate != "no"){
                    return $validate;
                }
                $user = User::where('id', $post->user_id)->first();

                $post['user_id'] = $user->id;
                $post['credited_by'] = $user->parent_id;
                if(!\Myhelper::can('setup_bank', $user->parent_id)){
                    $admin = User::whereHas('role', function ($q){
                        $q->where('slug', 'whitelable');
                    })->where('company_id', $user->company_id)->first(['id']);

                    if($admin && \Myhelper::can('setup_bank', $admin->id)){
                        $post['credited_by'] = $admin->id;
                    }else{
                        $admin = User::whereHas('role', function ($q){
                            $q->where('slug', 'admin');
                        })->first(['id']);
                        $post['credited_by'] = $admin->id;
                    }
                }

                $post['status'] = "pending";
                $action = Fundreport::create($post->all());
                if($action){
                    return response()->json(['statuscode' => "TXN", "message" => "Fund request send successfully", "txnid" => $action->id]);
                }else{
                    return response()->json(['statuscode' => "ERR", "message" => "Something went wrong, please try again."]);
                }
                break;

            case 'getfundbank':
                $rules = array(
                    'apptoken' => 'required',
                    'user_id'  => 'required|numeric'
                );
        
                $validate = \Myhelper::FormValidator($rules, $post);
                if($validate != "no"){
                    return $validate;
                }
                $user = User::where('id', $post->user_id)->first();
                $data['banks'] = Fundbank::where('user_id', $user->parent_id)->where('status', '1')->get();
                if(!\Myhelper::can('setup_bank', $user->parent_id)){
                    $admin = User::whereHas('role', function ($q){
                        $q->where('slug', 'whitelable');
                    })->where('company_id', $user->company_id)->first(['id']);

                    if($admin && \Myhelper::can('setup_bank', $admin->id)){
                        $data['banks'] = Fundbank::where('user_id', $admin->id)->where('status', '1')->get();
                    }else{
                        $admin = User::whereHas('role', function ($q){
                            $q->where('slug', 'admin');
                        })->first(['id']);
                        $data['banks'] = Fundbank::where('user_id', $admin->id)->where('status', '1')->get();
                    }
                }
                $data['paymodes'] = Paymode::where('status', '1')->get();
                return response()->json(['statuscode' => "TXN", "message" => "Get successfully", "data" => $data]);
                break;
            
            case 'transfer' :
            case 'return'   :
                if($post->type == "transfer" && !\Myhelper::can('fund_transfer', $post->user_id)){
                    return response()->json(['statuscode' => "ERR", "message" => "Permission not allowed"]);
                }

                if($post->type == "return" && !\Myhelper::can('fund_return', $post->user_id)){
                    return response()->json(['statuscode' => "ERR", "message" => "Permission not allowed1"]);
                }
                
                $provide = Provider::where('recharge1', 'fund')->first();
                $post['provider_id'] = $provide->id;
        
                $rules = array(
                    'amount' => 'required|numeric|min:1',
                    'id'     => 'required' 
                );
        
                $validate = \Myhelper::FormValidator($rules, $post);
		        if($validate != "no"){
		        	return $validate;
		        }
                
                $user  = User::where('id', $post->user_id)->first();
                $payee = User::where('id', $post->id)->first();
                
                if($post->type == "transfer"){
                    if($user->mainwallet < $post->amount){
                        return response()->json(['statuscode' => "ERR", "message" => "Insufficient wallet balance."]);
                    }
                }else{
                    if($payee->mainwallet - $payee->lockedamount < $post->amount){
                        return response()->json(['statuscode' => "ERR", "message" => "Insufficient balance in user wallet."]);
                    }
                }
                $post['txnid']   = 0;
                $post['option1'] = 0;
                $post['option2'] = 0;
                $post['option3'] = 0;
                $post['refno']   = date('ymdhis');
                return $this->paymentAction($post);
                break;
                
            default :
                return response()->json(['statuscode' => "ERR", 'message' => "Bad Parameter Request"]);
            break;
        }
    }
        public function paymentAction($post)
    {
        $user = User::where('id', $post->id)->first();

        if($post->type == "transfer" || $post->type == "request"){
            $action = User::where('id', $post->id)->increment('mainwallet', $post->amount);
        }else{
            $action = User::where('id', $post->id)->decrement('mainwallet', $post->amount);
        }

        if($action){
            if($post->type == "transfer" || $post->type == "request"){
                $post['trans_type'] = "credit";
            }else{
                $post['trans_type'] = "debit";
            }

            $insert = [
                'number' => $user->mobile,
                'mobile' => $user->mobile,
                'provider_id' => $post->provider_id,
                'api_id' => $this->fundapi->id,
                'amount' => $post->amount,
                'charge' => '0.00',
                'profit' => '0.00',
                'gst' => '0.00',
                'tds' => '0.00',
                'apitxnid' => NULL,
                'txnid' => $post->txnid,
                'payid' => NULL,
                'refno' => $post->refno,
                'description' => NULL,
                'remark' => $post->remark,
                'option1' => $post->option1,
                'option2' => $post->option2,
                'option3' => $post->option3,
                'option4' => NULL,
                'status' => 'success',
                'user_id' => $user->id,
                'credit_by' => $post->user_id,
                'rtype' => 'main',
                'via' => 'portal',
                'adminprofit' => '0.00',
                'balance' => $user->mainwallet,
                'trans_type' => $post->trans_type,
                'product' => "fund ".$post->type
            ];
            $action = Report::create($insert);
            if($action){
                return $this->paymentActionCreditor($post);
            }else{
                return response()->json(['statuscode' => "ERR", "message" => "Technical error, please contact your service provider before doing transaction."]);
            }
        }else{
            return response()->json(['statuscode' => "ERR", "message" => "Fund transfer failed, please try again."]);
        }
    }

    public function paymentActionCreditor($post)
    {
        $payee = $post->id;
        $user = User::where('id', $post->user_id)->first();
        if($post->type == "transfer" || $post->type == "request"){
            $action = User::where('id', $user->id)->decrement('mainwallet', $post->amount);
        }else{
            $action = User::where('id', $user->id)->increment('mainwallet', $post->amount);
        }

        if($action){
            if($post->type == "transfer" || $post->type == "request"){
                $post['trans_type'] = "debit";
            }else{
                $post['trans_type'] = "credit";
            }

            $insert = [
                'number' => $user->mobile,
                'mobile' => $user->mobile,
                'provider_id' => $post->provider_id,
                'api_id' => $this->fundapi->id,
                'amount' => $post->amount,
                'charge' => '0.00',
                'profit' => '0.00',
                'gst' => '0.00',
                'tds' => '0.00',
                'apitxnid' => NULL,
                'txnid' => $post->txnid,
                'payid' => NULL,
                'refno' => $post->refno,
                'description' => NULL,
                'remark' => $post->remark,
                'option1' => $post->option1,
                'option2' => $post->option2,
                'option3' => $post->option3,
                'option4' => NULL,
                'status' => 'success',
                'user_id' => $user->id,
                'credit_by' => $payee,
                'rtype' => 'main',
                'via' => 'portal',
                'adminprofit' => '0.00',
                'balance' => $user->mainwallet,
                'trans_type' => $post->trans_type,
                'product' => "fund ".$post->type
            ];

            $action = Report::create($insert);
            if($action){
                return response()->json(['statuscode' => "TXN", "message" =>  "Transaction Successfull"]);
            }else{
                return response()->json(['statuscode' => "ERR", "message" => "Technical error, please contact your service provider before doing transaction."]);
            }
        }else{
            return response()->json(['statuscode' => "ERR", "message" => "Technical error, please contact your service provider before doing transaction."]);
        }
    }
}
