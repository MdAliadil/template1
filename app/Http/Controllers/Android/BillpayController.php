<?php

namespace App\Http\Controllers\Android;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Provider;
use App\User;
use App\Model\Report;
use App\Model\Mahaagent;
use Carbon\Carbon;
use App\Model\Api;

class BillpayController extends Controller
{
    protected $billapi;
    public function __construct()
    {
        $this->billapi = Api::where('code', 'mhbill')->first();
    }

    public function providersList(Request $post)
    {
        $providers = Provider::where('type', $post->type)->where('status', "1")->orderBy('name')->get();
        return response()->json(['statuscode' => "TXN", 'message' => "Provider Fetched Successfully", 'data' => $providers]);
    }

    public function getprovider(Request $post)
    {
        $provider = Provider::where('id', $post->provider_id)->first();
        
        for ($i=0; $i < $provider->paramcount; $i++) {
            if($provider->ismandatory[$i] == "TRUE"){
                $param['paramname'] = isset($provider->paramname[$i])?$provider->paramname[$i]:'';
                $param['maxlength'] = isset($provider->maxlength[$i])?$provider->maxlength[$i]:'';
                $param['minlength'] = isset($provider->minlength[$i])?$provider->minlength[$i]:'';
                $param['ismandatory'] = isset($provider->ismandatory[$i])?$provider->ismandatory[$i]:'';
                $param['fieldtype'] = isset($provider->fieldtype[$i])?$provider->fieldtype[$i]:'';
                $param['regex'] = isset($provider->regex[$i])?$provider->regex[$i]:'';

                $params[] = $param;
            }
        }
        $provider['parame'] = $params;
        return response()->json($provider);
    }

    public function transaction(Request $post)
    {
        if(!in_array($post->type, ['validate', 'payment'])){
            return response()->json(['statuscode' => "ERR", "message" => "Type parameter request in invalid"]);
        }

        if($post->type == "validate"){
            $rules = array(
                'apptoken' => 'required',
                'user_id'  =>'required|numeric',
                'provider_id' => 'required|numeric'
            );
        }else{
            $rules = array(
                'apptoken' => 'required',
                'user_id'  =>'required|numeric',
                'provider_id' => 'required|numeric',
                'amount'      => 'required|numeric|min:10'
            );
        }
        
        $agent = Mahaagent::where('user_id', $post->user_id)->first();

        if(!$agent){
            $output['statuscode'] = "ERR";
            $output['message']    = "BBPS Onboarding Pending";
            return response()->json($output);
        }

        $agent    = $this->bbpsregistration($post, $agent);

        if(!$agent->bbps_id){
            $output['statuscode'] = "ERR";
            $output['message']    = "BBPS Onboarding Approval Pending";
            return response()->json($output);
        }

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = User::where('id', $post->user_id)->first();
        if(!$user){
            $output['statuscode'] = "ERR";
            $output['message']    = "User details not matched";
            return response()->json($output);
        }

        if (!\Myhelper::can('billpayment_service', $user->id)) {
            return response()->json(['statuscode' => "ERR", "message" => "Service Not Allowed"]);
        }

        if($user->status != "active"){
            return response()->json(['statuscode' => "ERR", "message" => "Your account has been blocked."]);
        }

        $provider = Provider::where('id', $post->provider_id)->first();

        if(!$provider){
            return response()->json(['statuscode' => "ERR", "message" => "Operator Not Found"]);
        }

        if($provider->status == 0){
            return response()->json(['statuscode' => "ERR", "message" => "Operator Currently Down."]);
        }

        if(!$provider->api || $provider->api->status == 0){
            return response()->json(['statuscode' => "ERR", "message" => "Recharge Service Currently Down."]);
        }

        $post['crno'] = "";
        for ($i=0; $i < $provider->paramcount; $i++) { 
            if($provider->ismandatory[$i] == "TRUE"){
                $rules['number'.$i] = "required";
                $post['crno'] .= $post['number'.$i]."|";
            }
        }

        switch ($post->type) {
            case 'validate':
                $validate = \Myhelper::FormValidator($rules, $post);
                if($validate != "no"){
                    return $validate;
                }

                $url = $provider->api->url."GetAmount";

                $json_data = [
                    "requestby"    => $provider->api->username,
                    "securityKey"  => $provider->api->password,
                    "billerId"     => $provider->recharge2,
                    'mobileNumber' => $user->mobile,
                    "crno"         => rtrim($post->crno, "|"),
                    "ip"           => $post->ip(),
                    "mac"          => "BC-EE-7B-9C-F6-C0",
                    "agentId"      => $agent->bbps_id
                ];

                $header = array(
                    "authorization: Basic ".base64_encode($provider->api->username.":".$provider->api->optional1),
                    "cache-control: no-cache",
                    "content-type: application/json"
                );
                $result = \Myhelper::curl($url, "POST", json_encode($json_data), $header, "no");

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
                
                if ($this->pinCheck($post) == "fail") {
                    return response()->json(['statuscode' => "ERR", "message" => "Transaction Pin is incorrect"]);
                }
                
                if($user->mainwallet - $this->mainlocked() < $post->amount){
                    return response()->json(['statuscode' => "ERR", "message"=> 'Low Balance, Kindly recharge your wallet.']);
                }

                $previousrecharge = Report::where('number', $post->number0)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                if($previousrecharge > 0){
                    return response()->json(['statuscode' => "ERR", "message"=> 'Same Transaction allowed after 2 min.'], 400);
                }

                $post['profit'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $debit = User::where('id', $user->id)->decrement('mainwallet', $post->amount - $post->profit);
                if ($debit) {
                    do {
                        $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                    } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

                    $insert = [
                        'number' => $post->number0,
                        'mobile'  => isset($post->number1)?$post->number1:$user->mobile,
                        'provider_id' => $provider->id,
                        'api_id' => $provider->api->id,
                        'amount' => $post->amount,
                        'profit' => $post->profit,
                        'txnid' => $post->txnid,
                        'option1' => $post->biller,
                        'option2' => $post->duedate,
                        'status' => 'pending',
                        'user_id'    => $user->id,
                        'credit_by'  => $user->id,
                        'rtype'      => 'main',
                        'via'        => 'app',
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
                                    'error'    => false,
                                    'response' => ''
                                ];
                            }
                            break;
                        
                        default:
                            $agent = Mahaagent::where('user_id', $post->user_id)->first();
                            if(!$agent){
                                return response()->json(['statuscode' => 'ERR', 'message' => "Agent Onboard Pending"]);
                            }

                            if(!$agent->bbps_id){
                                return response()->json(['statuscode' => 'ERR', 'message' => "Agent Approval Pending"]);
                            }

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
                        $output['statuscode'] = "TXN";
                        $output['message'] = "Billpayment Request Submitted";
                    }else{
                        User::where('id', $user->id)->increment('mainwallet', $post->amount - $post->profit);
                        Report::where('id', $report->id)->update($update);
                        $output['statuscode'] = "TXF";
                        $output['message'] = $update['description'];    
                    }
                    $output['txnid']   = $post->txnid;
                    $output['rrn']     = $post->txnid;
                    return response()->json($output);
                }else{
                    return response()->json(['statuscode'=> "ERR" , "message" => 'Transaction Failed, please try again.']);
                }
                break;
        }
    }

    public function status(Request $post)
    {
        $rules = array(
            'apptoken' => 'required',
            'user_id'  => 'required|numeric',
            'txnid'    => 'required|numeric'
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

        if (!\Myhelper::can('billpayment_status', $user->id)) {
            return response()->json(['statuscode' => "ERR", "message" => "Service Not Allowed"]);
        }

        $report = Report::where('id', $post->txnid)->first();

        if(!$report || !in_array($report->status , ['pending', 'success'])){
            return response()->json(['status' => "Recharge Status Not Allowed1"], 400);
        }
       // dd($report->api->code);
        switch ($report->api->code) {
            case 'billpayment':
                $url = $report->api->url.'/status?token='.$report->api->username.'&apitxnid='.$report->txnid;
                break;
            
            default:
                return response()->json(['statuscode' => "ERR", "message" => "Recharge Status Not Allowed2"]);
                break;
        }

        $method = "GET";
        $parameter = "";
        $header = [];

        if (env('APP_ENV') != "local") {
                $result = \Myhelper::curl($url, $method, $parameter, $header);
            }else{
                $result = [
                    'error' => false,
                    'response' => json_encode([
                        'statuscode' => 'TXN',
                        'message'=> 'local',
                        'data' => ['status'=> 'success', 'ref_no' => 'local']
                    ]) 
                ];
            }
        if($result['response'] != ''){
            switch ($report->api->code) {
                case 'billpayment':
                $doc = json_decode($result['response']);
                dd($doc);
                if($doc->statuscode == "TXN" && ($doc->data->status =="success" || $doc->data->status =="pending")){
                    $update['refno'] = $doc->data->ref_no;
                    $update['status'] = "success";

                    $output['statuscode'] = "TXN";
                    $output['txn_status'] = "success";
                    $output['message'] = $doc->message;
                    $output['refno'] = $doc->data->ref_no;

                }elseif($doc->statuscode == "TXN" && $doc->data->status =="reversed"){
                    $update['status'] = "reversed";
                    $update['refno'] = $doc->data->refno;

                    $output['statuscode'] = "TXR";
                    $output['txn_status'] = "reversed";
                    $output['message'] = $doc->message;
                    $output['refno'] = $doc->data->refno;
                }else{
                    $update['status'] = "Unknown";
                    $update['refno'] = $doc->message;

                    $output['statuscode'] = "TNF";
                    $output['txn_status'] = "unknown";
                    $output['message'] = $doc->message;
                    $output['refno'] = $doc->message;
                }
                break;
            }
            $product = "billpay";

            if ($update['status'] != "Unknown") {
                $reportupdate = Report::where('id', $report->id)->update($update);
                if ($reportupdate && $update['status'] == "reversed") {
                    \Myhelper::transactionRefund($post->id);
                }

                if($report->user->role->slug == "apiuser" && $report->status == "pending" && $post->status != "pending"){
                    \Myhelper::callback($report, $product);
                }
            }
            return response()->json($output);
        }else{
            return response()->json(['statuscode' => "ERR", "message" => "Something went wrong, contact your service provider"]);
        }
    }
}
