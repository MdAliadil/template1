<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Provider;
use App\Model\Report;
use App\Model\Mahaagent;
use Carbon\Carbon;
use App\Model\Api;
use App\User;

class BillpayController extends Controller
{
    protected $billapi;
    public function __construct()
    {
        $this->billapi = Api::where('code', 'mhbill')->first();
    }

    public function index(Request $post, $type)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('billpayment_service')) {
            abort(403);
        }

        $data['type'] = $type;
        $data['providers'] = Provider::where('type', $type)->where('status', "1")->orderBy('name')->get();

        $agent = Mahaagent::where('user_id', \Auth::id())->first();

        if(!$agent){
            return redirect(route('aeps'));
        }
        $post['user_id'] = \Auth::id();
        $data['agent'] = $this->bbpsregistration($post, $agent);
        return view('service.billpayment')->with($data);
    }

    public function bbps(Request $post, $type)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('billpayment_service')) {
            abort(403);
        }

        $data['type'] = $type;
        $data['providers'] = Provider::where('type', $type)->where('status', "1")->orderBy('name')->get();

        $agent = Mahaagent::where('user_id', \Auth::id())->first();

        if(!$agent){
            return redirect(route('aeps'));
        }
        $post['user_id'] = \Auth::id();
        $data['agent'] = $this->bbpsregistration($post, $agent);
        return view('service.bbpsrecharge')->with($data);
    }

    public function payment(Request $post)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('billpayment_service')) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
        }

        $rules = array(
            'provider_id' => 'required|numeric'
        );

        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(['status' => $error]);
        }

        $user = \Auth::user();
        $post['user_id'] = $user->id;

        if($user->status != "active"){
            return response()->json(['status' => "Your account has been blocked."], 400);
        }

        $agent    = Mahaagent::where('user_id', \Auth::id())->first();

        if(!$agent->bbps_id){
            return response()->json(['status' => "Agent Approval Pending"], 400);
        }

        $provider = Provider::where('id', $post->provider_id)->first();

        if(!$provider){
            return response()->json(['status' => "Operator Not Found"], 400);
        }

        if($provider->status == 0){
            return response()->json(['status' => "Operator Currently Down."], 400);
        }

        if(!$provider->api || $provider->api->status == 0){
            return response()->json(['status' => "Bill Payment Service Currently Down."], 400);
        }
        $post['crno'] = "";
        for ($i=0; $i < $provider->paramcount; $i++) { 
            if($provider->ismandatory[$i] == "TRUE"){
                $rules['number'.$i] = "required";
                $post['crno'] .= $post['number'.$i]."|";
            }
        }

        switch ($post->type) {
            case 'getbilldetails':

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    return response()->json(['status' => $error]);
                }

                $url = $provider->api->url."GetAmount";

                $json_data = [
                    "requestby"    => $this->billapi->username,
                    "securityKey"  => $this->billapi->password,
                    "billerId"     => $provider->recharge2,
                    'mobileNumber' => $user->mobile,
                    "crno"         => rtrim($post->crno, "|"),
                    "ip"           => $post->ip(),
                    "mac"          => "BC-EE-7B-9C-F6-C0",
                    "agentId"      => $agent->bbps_id
                ];

                $header = array(
                    "authorization: Basic ".base64_encode($this->billapi->username.":".$this->billapi->optional1),
                    "cache-control: no-cache",
                    "content-type: application/json"
                );
                $result = \Myhelper::curl($url, "POST", json_encode($json_data), $header, "no");
               //dd([$url, $header, $json_data, $result]);
                if($result['response'] != ""){
                    $response = json_decode($result['response']);
                    if(isset($response->ResponseCode) && $response->ResponseCode == "000"){
                        if(isset($response->AdditionalDetails[4])){
                            $extradata = isset($response->AdditionalDetails[4]->Value)?$response->AdditionalDetails[4]->Value:0;
                        }else{
                            $extradata = 0;
                        }
                        
                        return response()->json([
                            'statuscode' => "TXN",
                            'data'       => [
                                "customername" => $response->CustomerName,
                                "duedate"      => $response->BillDueDate,
                                "dueamount"       => $response->BillAmount,
                                "TransactionId"=> $response->TransactionId,
                                'balance'      => $extradata
                            ]
                        ], 200);
                    }
                }
                return response()->json(['statuscode' => "ERR", "message" => isset($response->ResponseMessage) ? $response->ResponseMessage : "Something went wrong"], 400);
                break;
            
            case 'payment':
                $rules['amount'] = "required";
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    return response()->json(['status' => $error]);
                }
                
                if ($this->pinCheck($post) == "fail") {
                    return response()->json(['status' => "Transaction Pin is incorrect"], 400);
                }

                if($user->mainwallet - $this->mainlocked() < $post->amount){
                    return response()->json(['status'=> 'Low Balance, Kindly recharge your wallet.'], 400);
                }

                $previousrecharge = Report::where('number', $post->number0)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                if($previousrecharge > 0){
                    return response()->json(['status'=> 'Same Transaction allowed after 2 min.'], 400);
                }

                $post['profit'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $debit = User::where('id', $user->id)->decrement('mainwallet', $post->amount - $post->profit);
                if ($debit) {
                    do {
                        $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                    } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

                    $insert = [
                        'number'  => $post->number0,
                        'mobile'  => isset($post->number1)?$post->number1:$user->mobile,
                        'provider_id' => $provider->id,
                        'api_id'  => $provider->api->id,
                        'amount'  => $post->amount,
                        'profit'  => $post->profit,
                        'txnid'   => $post->txnid,
                        'payid'   => $post->TransactionId,
                        'option1' => $post->biller,
                        'option2' => $post->duedate,
                        'status'  => 'pending',
                        'user_id'    => $user->id,
                        'credit_by'  => $user->id,
                        'rtype'      => 'main',
                        'via'        => 'portal',
                        'balance'    => $user->mainwallet,
                        'trans_type' => 'debit',
                        'product'    => 'billpay'
                    ];

                    $report = Report::create($insert);

                    switch ($provider->api->code) {
                        case 'billpayment':
                            $url = $provider->api->url."/payment";
                            $parameter = [
                                "token"    => $provider->api->username,
                                "operator" => $provider->recharge1,
                                "number"   => $post->number,
                                "mobile"   => $post->mobile,
                                "amount"   => $post->amount,
                                "biller"   => $post->biller,
                                "duedate"  => $post->duedate,
                                "apitxnid" => $post->txnid,
                            ];

                            if (env('APP_ENV') == "server") {
                                $result = \Myhelper::curl($url, "POST", json_encode($parameter), ["Content-Type: application/json", "Accept: application/json"], "yes", "App\Model\Report", $post->txnid);
                            }else{
                                $result = [
                                    'error' => true,
                                    'response' => '' 
                                ];
                            }
                            break;
                        
                        default:
                            $url = $provider->api->url."SendBillPaymentRequest";

                            $parameter = [
                                "requestby"   => $provider->api->username,
                                "securityKey" => $provider->api->password,
                                "billAmount"  => $post->amount,
                                "transid"     => $post->TransactionId,
                                "billerId"    => $provider->recharge2,
                                'mobileNumber'=> $user->mobile,
                                "crno"        => rtrim($post->crno, "|"),
                                "ip"          => $post->ip(),
                                "mac"         => "BC-EE-7B-9C-F6-C0",
                                "agentId"     => $agent->bbps_id,
                                'BillerCategory' => "Electricity"
                            ];
                            
                            $header = array(
                                "authorization: Basic ".base64_encode($provider->api->username.":".$provider->api->optional1),
                                "cache-control: no-cache",
                                "content-type: application/json"
                            );

                            if (env('APP_ENV') == "server") {
                                $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", "App\Model\Report", $post->txnid);
                            }else{
                                $result = [
                                    'error' => true,
                                    'response' => '' 
                                ];
                            }

                            break;
                    }

                    if($result['error'] || $result['response'] == ''){
                        $update['status'] = "pending";
                        $update['payid'] = "pending";
                        $update['description'] = "billpayment pending";
                    }else{
                        $doc = json_decode($result['response']);

                        switch ($provider->api->code) {
                            case 'billpayment':
                                if(isset($doc->statuscode)){
                                    if($doc->statuscode == "TXN"){
                                        $update['status'] = "success";
                                        $update['payid'] = $doc->data->txnid;
                                        $update['description'] = "Billpayment Accepted";
                                    }elseif($doc->statuscode == "TXF"){
                                        $update['status'] = "failed";
                                        $update['payid'] = $doc->data->txnid;
                                        $update['description'] = (isset($doc->message)) ? $doc->message : "failed";
                                    }else{
                                        $update['status'] = "failed";
                                        if($doc->status == "Insufficient Wallet Balance"){
                                            $update['description'] = "Service down for sometime.";
                                        }else{
                                            $update['description'] = (isset($doc->message)) ? $doc->message : "failed";
                                        }
                                    }
                                }else{
                                    $update['status'] = "pending";
                                    $update['payid'] = "pending";
                                    $update['description'] = "billpayment pending";
                                }
                                break;
                            
                            default:
                                if(isset($doc->ResponseCode)){
                                    if($doc->ResponseCode == "000"){
                                        $update['status'] = "success";
                                        $update['refno']  = isset($doc->TransactionRefId) ? $doc->TransactionRefId : "Success";
                                        $update['payid']  = isset($doc->TransactionId) ? $doc->TransactionId : "Success";
                                        $update['option3']= isset($doc->PaymentRefId) ? $doc->PaymentRefId : "Success";
                                        $update['description'] = "Billpayment Accepted";
                                    }else{
                                        $update['status'] = "failed";
                                        if($doc->ResponseCode == "Insufficient Wallet Balance"){
                                            $update['description'] = "Service down for sometime.";
                                        }else{
                                            $update['description'] = (isset($doc->ResponseMessage)) ? $doc->ResponseMessage : "failed";
                                        }
                                    }
                                }else{
                                    $update['status'] = "pending";
                                    $update['payid'] = "pending";
                                    $update['description'] = "billpayment pending";
                                }
                                break;
                        }
                    }

                    if($update['status'] == "success" || $update['status'] == "pending"){
                        Report::where('id', $report->id)->update($update);
                        \Myhelper::commission($report);
                    }else{
                        User::where('id', $user->id)->increment('mainwallet', $post->amount - $post->profit);
                        Report::where('id', $report->id)->update($update);
                    }

                    return response()->json(['status' => $update['status'], 'data' => $report, 'description' => $update['description']], 200);
                }else{
                    return response()->json(['status'=> 'Transaction Failed, please try again.'], 400);
                }
                break;
        }
    }

    public function getprovider(Request $post)
    {
        return response()->json(Provider::where('id', $post->provider_id)->first());
    }
}
