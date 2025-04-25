<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\Mahastate;
use Illuminate\Validation\Rule;
use App\Model\Api;
use App\Model\Upireport;
use App\Model\Commission;
use App\Model\Provider;
use App\Model\Aepsreport;
use App\Model\Aepsfundreport;
use App\Model\Aepsfundrequest;
use App\Model\Initiateqr;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use App\Model\Apitoken;
use Illuminate\Support\Facades\Cache;

class UpiController extends Controller
{
    protected $xettleupi;
    public function __construct(){
       // $this->pinwallet  = Api::where('code', 'loadwallet')->first();
        //$this->xettleupi = Api::where('code', 'sfupi')->first();
    }
    
    public function QrIntent(Request $post)
    {
        return response()->json(['statuscode'=>'ERR', 'message'=> "Issue at bank side"]);
        
       //dd($post->ip());
       
       /*if($post->ip() !="103.225.205.98"){
           return response()->json(['statuscode'=>'ERR', 'message'=> "Issue at bank end"]); 
       }*/
        $rules = array(
            'token'   => 'required',
            'clientOrderId' => 'required|unique:upirorders,mytxnid',
            'amount' => 'required|numeric|min:1',
            'returnUrl' => 'required|url'
        );
        
        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'statuscode' => 'BPR',
                'status' => 'Bad Parameter Request.',  
                'message' => $error
            ));
        }
        
        $token = Apitoken::where('ip',$post->ip())->where('token', $post->token)->first();
        if(!$token){
         return response()->json(['statuscode'=>'ERR', 'message'=> "IP or Token mismatch, your current system IP is ".$post->ip()]);   
        }
        $post['user_id'] = $token->user_id;
        // if($post->user_id !="2"){
        //     return response()->json(['statuscode'=>'ERR', 'message'=> "Service down for some time"]); 
        // }
        $user = User::whereId($post->user_id)->first();
        $post['company_id'] = $user->company_id;
        //dd($this->bankupiapi());
        $apicheck = $this->bankupiapi();
        $post['transction_id'] = $this->transcode().date('YmdHis').rand(11111111,99999999);
        
        $provider = Provider::where('recharge1', 'upi')->first();
        $post['provider_id'] = $provider->id;
        
        if($apicheck =="safepay"){
            $api = Api::whereCode('sfupi')->first();
            $url="https://proapi.safepayindia.com/QRService.svc/CreateQRIntent";
         
            
            $req = [
                "order_id"=>$post->transction_id,
                "payment_remark"=>'salary',
                "amount"=>$post->amount
                
            ];
            
            //dd(json_encode($req));
              $header = array(
                            'Content-Type: application/json',
                            'Client-ID:SFP_1FC06ZYTQL481FC06ZYTQL',
                            'Client-Secret:1FC06ZYTQLME481FC06ZYTQLME',
                         ); 
             
                $result = \Myhelper::curl($url, "POST",json_encode($req), $header, "yes", 'CSUPI', $post->transction_id);
               //dd($result);
                    
                if($result['response'] != ''){
                $response = $result['response'];
                
                $doc = json_decode($response);
              
                if($doc->res_status =="CREATED" || $doc->res_status =='SUCCESS'){
                    $insert = [
                            "mobile"   => $user->mobileNumber,
                            "number"   => $user->upiId,
                            "payeeVPA" => $user->upiId,
                            'txnid'    => $post->transction_id,
                            "payid"    => $doc->safepay_txnid, //$doc->data->extTransactionId,
                            'mytxnid'  => $post->clientOrderId,
                            "amount"  => $post->amount,
                            "api_id"  => $api->id,
                            "user_id" => $post->user_id,
                            "credit_by" => $user->id,
                            "balance" => $user->upiwallet,
                            'aepstype'=> "UPI",
                            "trans_type"=>"credit",
                            "option1"=>$doc->qr_intent, //urldecode($doc->data->qrString),
                            'status'  => 'initiated',
                            'description'  => $post->returnUrl,
                            'credited_by' => $post->user_id,
                            'balance'     => $user->upiwallet,
                            'provider_id' => $post->provider_id,
                            'product'    => "upicollect"
                        ];
                       // dd($insert);
                        \DB::table('upirorders')->insert($insert);
                    $deatils = [
                          "extTransactionId"=>$doc->safepay_txnid, //$doc->data->extTransactionId,
                          "apiTxnId"=>$post->transction_id,
                          "qrString"=>$doc->qr_intent,//urldecode("upi://pay?ver=01&mode=15&am=".$post->amount.".00&cu=INR&pa=sbe.sbemid0001.sbeeazyw01@cnrb&pn=EAZYWAYBUSINESSSOLUTION&mc=6012&tr=".$doc->safepay_txnid."&tn=pay&mid=SBEMID0001&msid=SBEEAZYW01&mtid="),
                          "clientOrderId"=>$post->clientOrderId,
                          "url"=> "https://dashboard.tejaspee.com/order/".$post->transction_id //$doc->data->url
                        ];
                    return response()->json(['statuscode'=>'TXN', 'message'=> "QR generated Successfully","data" =>$deatils ]);
                }else{
                   return response()->json(['statuscode'=>'TXF', 'message'=> $doc->message??"Something Went Wrong"]); 
                }
            }else{
                 return response()->json(['statuscode'=>'TXF', 'message'=> "Verification failed at bank end"]);
            }
        }elseif($apicheck =="paycorn"){
          $api = Api::whereCode('paycorn')->first();
            
           
           
            $url="http://uat.paycoons.com/controller/api/v2/auth/PaycoonDynamicQR?payin_ref=".$post->transction_id."&amount=".$post->amount."&mNo=".$user->mobile;
           
           /* $req = [
                'token' => $api->username,
                'clientOrderId' => $post->transction_id,
                "amount"=>$post->amount,
                "returnUrl"=>$post->returnUrl,
                "sid"=>$user->sid
                
            ];*/
            
            $req = [
                'merchantId' => "2886255",
                "clientid"=>'PV8E7DLP-2EZ3-CX7S-I16M-AMK711E3T368',
                "clientSecretKey"=>'9TNKXZVQ7H9SL696L5UFTRM1LQKV815S'
                
            ];
            
            //dd(json_encode($req));
              $header = array(
                            'Content-Type: application/json'
                         ); 
             
                $result = \Myhelper::curl($url, "POST",json_encode($req), $header, "yes", 'PSUPI', $post->transction_id);
               
                    
                if($result['response'] != ''){
                $response = $result['response'];
                
                $doc = json_decode($response);
               // dd($doc);
               
               if(!isset($doc->response_code)){
                    return response()->json([
                        'status'    => 'ERR', 
                        'message'   => 'Something went wrong',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
                if($doc->response_code =="1"){
                    $insert = [
                            "mobile"   => $user->mobileNumber,
                            "number"   => $user->upiId,
                            "payeeVPA" => $user->upiId,
                            'txnid'    => $post->transction_id,
                            "payid"    => $doc->orderid, //$doc->data->extTransactionId,
                            'mytxnid'  => $post->clientOrderId,
                            "amount"  => $post->amount,
                            "api_id"  => $api->id,
                            "user_id" => $post->user_id,
                            "credit_by" => $user->id,
                            "balance" => $user->upiwallet,
                            'aepstype'=> "UPI",
                            "trans_type"=>"credit",
                            "option1"=>$doc->UpiLink,//$doc->data->redirect_url??'', //urldecode($doc->data->qrString),
                            'status'  => 'initiated',
                            'description'  => $post->returnUrl??'',
                            'credited_by' => $post->user_id,
                            'balance'     => $user->upiwallet,
                            'provider_id' => $post->provider_id,
                            'product'    => "upicollect"
                        ];
                       // dd($insert);
                        \DB::table('upirorders')->insert($insert);
                    $deatils = [
                          "extTransactionId"=>$doc->orderid, //$doc->data->extTransactionId,
                          "apiTxnId"=>$post->transction_id,
                          "qrString"=>$doc->UpiLink,//urldecode("upi://pay?ver=01&mode=15&am=".$post->amount.".00&cu=INR&pa=sbe.sbemid0001.sbeeazyw01@cnrb&pn=EAZYWAYBUSINESSSOLUTION&mc=6012&tr=".$doc->safepay_txnid."&tn=pay&mid=SBEMID0001&msid=SBEEAZYW01&mtid="),
                          "clientOrderId"=>$post->clientOrderId,
                          "url"=> "https://dashboard.tejaspee.com/order/".$post->transction_id //$doc->data->redirect_url??''//$doc->data->url
                        ];
                    return response()->json(['statuscode'=>'TXN', 'message'=> "QR generated Successfully","data" =>$deatils ]);
                }else{
                   return response()->json(['statuscode'=>'TXF', 'message'=> $doc->message??"Something Went Wrong"]); 
                }
            }else{
                 return response()->json(['statuscode'=>'TXF', 'message'=> "Verification failed at bank end"]);
            }  
        }else{
            $api = Api::whereCode('csupi')->first();
            
           
           
            $url="https://connect.safepayindia.com/v3/api/upi/generateQr";
           /* $req = [
                'token' => $api->username,
                'clientOrderId' => $post->transction_id,
                "amount"=>$post->amount,
                "returnUrl"=>$post->returnUrl,
                "sid"=>$user->sid
                
            ];*/
            
            $req = [
                'token' => "pO0ykSqPqa508jceLCBamZfJUMkMaG",
                "clientOrderId"=>$post->transction_id,
                "returnUrl"=>$post->returnUrl,
                "amount"=>$post->amount
                
            ];
            
            //dd(json_encode($req));
              $header = array(
                            'Content-Type: application/json'
                         ); 
             
                $result = \Myhelper::curl($url, "POST",json_encode($req), $header, "yes", 'CSUPI', $post->transction_id);
               
                    
                if($result['response'] != ''){
                $response = $result['response'];
                
                $doc = json_decode($response);
              
                if($doc->statuscode =="TXN"){
                    $insert = [
                            "mobile"   => $user->mobileNumber,
                            "number"   => $user->upiId,
                            "payeeVPA" => $user->upiId,
                            'txnid'    => $post->transction_id,
                            "payid"    => $doc->data->apiTxnId, //$doc->data->extTransactionId,
                            'mytxnid'  => $post->clientOrderId,
                            "amount"  => $post->amount,
                            "api_id"  => $api->id,
                            "user_id" => $post->user_id,
                            "credit_by" => $user->id,
                            "balance" => $user->upiwallet,
                            'aepstype'=> "UPI",
                            "trans_type"=>"credit",
                            "option1"=>$doc->data->qrString,//$doc->data->redirect_url??'', //urldecode($doc->data->qrString),
                            'status'  => 'initiated',
                            'description'  => $post->returnUrl,
                            'credited_by' => $post->user_id,
                            'balance'     => $user->upiwallet,
                            'provider_id' => $post->provider_id,
                            'product'    => "upicollect"
                        ];
                       // dd($insert);
                        \DB::table('upirorders')->insert($insert);
                    $deatils = [
                          "extTransactionId"=>$doc->data->apiTxnId, //$doc->data->extTransactionId,
                          "apiTxnId"=>$post->transction_id,
                          "qrString"=>$doc->data->qrString,//urldecode("upi://pay?ver=01&mode=15&am=".$post->amount.".00&cu=INR&pa=sbe.sbemid0001.sbeeazyw01@cnrb&pn=EAZYWAYBUSINESSSOLUTION&mc=6012&tr=".$doc->safepay_txnid."&tn=pay&mid=SBEMID0001&msid=SBEEAZYW01&mtid="),
                          "clientOrderId"=>$post->clientOrderId,
                          "url"=> "https://dashboard.tejaspee.com/order/".$post->transction_id //$doc->data->redirect_url??''//$doc->data->url
                        ];
                    return response()->json(['statuscode'=>'TXN', 'message'=> "QR generated Successfully","data" =>$deatils ]);
                }else{
                   return response()->json(['statuscode'=>'TXF', 'message'=> $doc->message??"Something Went Wrong"]); 
                }
            }else{
                 return response()->json(['statuscode'=>'TXF', 'message'=> "Verification failed at bank end"]);
            }
        }
        

        
    }
    
   public function statusCheck(Request $post)
   {
        $rules = array(
                'token'   => 'required',
                'clientOrderId' => 'required'
            );
            
        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'statuscode' => 'BPR',
                'status' => 'Bad Parameter Request.',  
                'message' => $error
            ));
        }
        $report = Upireport::whereMytxnid($post->clientOrderId)->first();
        //dd($report);
        if(!$report){
            $upiOrders = \DB::table('upirorders')->where('mytxnid',$post->clientOrderId)->first();
            if($upiOrders->status =="initiated"){
                $api = Api::whereCode('paycorn')->first();
            
                $url="http://app.paycoons.com/controller/api/v2/auth/PaycoonDynamicQRStatusCheck?payin_ref=".$upiOrders->txnid;
                $req = [
                'merchantId' => "2886255",
                "clientid"=>'PV8E7DLP-2EZ3-CX7S-I16M-AMK711E3T368',
                "clientSecretKey"=>'9TNKXZVQ7H9SL696L5UFTRM1LQKV815S'
                
            ];
            
            
            $header = array(
                    'Content-Type: application/json'
                 ); 
             
                $result = \Myhelper::curl($url, "POST",json_encode($req), $header, "yes", 'PSUPI', $upiOrders->txnid);
                 if(!$result['response'] != ''){
                    return response()->json(['statuscode'=>'ERR', 'message'=> "Something Went Wrong"]); 
                 }
                $response = $result['response'];
                
                $doc = json_decode($response);
               // dd($doc);
                $user  = User::where('id', $upiOrders->user_id)->first();
                $provider = Provider::where('recharge1', 'upi')->first();
                $post['provider_id'] = $provider->id;
                if($doc->response_code =="1"){
                    $usercommission = \Myhelper::getCommission($doc->Amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                    $usergstAmt =$usercommission*18/100;
              
                $update = [
                    "status"=>"success",
                    "refno"=>$doc->rrn,
                    "amount"=>$doc->Amount,//number_format($post->amount, 0, '.', ''),
                    "orderAmount"=>$upiOrders->amount,
                    "payid"=>$doc->payin_ref,
                    'option1'=>$doc->txnid??'',
                    "charge"  => $usercommission,
                    "gst"  => '0',
                    "payer_vpa"=>$doc->payerVpa??'',
                    "payerAccName"=>$doc->payerAccName??'',
                    "number"=>$doc->vpaadress??''
                ];
                //dd($update);
                $update = \DB::table('upirorders')->whereTxnid($doc->payin_ref)->where('status','initiated')->update($update);
                $reportOrderAmt = \DB::table('upirorders')->whereTxnid($doc->payin_ref)->first();
                $usercommission = \Myhelper::getCommission($doc->Amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                
                $insert = [
                    "mobile"   => $reportOrderAmt->mobile,
                    "payeeVPA" => $reportOrderAmt->payeeVPA,
                    'txnid'    => $reportOrderAmt->txnid,
                    "charge"=>$reportOrderAmt->charge,
                    "gst"=>$reportOrderAmt->gst,
                    "payid"    => $reportOrderAmt->payid,
                    'mytxnid'  => $reportOrderAmt->mytxnid,
                    "amount"  => $reportOrderAmt->amount,
                    "api_id"  => $reportOrderAmt->api_id,
                    "user_id" => $reportOrderAmt->user_id,
                    "balance" => $user->upiwallet,
                    'aepstype'=> "UPI",
                    "trans_type"=>"credit",
                    "option1"=>urldecode($reportOrderAmt->option1),
                    'status'  => 'success',
                    "refno"=>$reportOrderAmt->refno,
                    'description'  => $reportOrderAmt->description,
                    'credited_by' => $reportOrderAmt->credited_by,
                    //'balance'     => $user->mainwallet,
                    'provider_id' => $reportOrderAmt->provider_id,
                    'product'    => "upicollect"
                ];
                
                 \DB::table('upireports')->insert($insert);
                 
                 $totalCharge = $reportOrderAmt->charge+$reportOrderAmt->gst;
                User::where('id', $upiOrders->user_id)->increment('upiwallet', $doc->Amount-$totalCharge);
                $data = [
                    "clientOrderId"=>$reportOrderAmt->mytxnid,
                    "amount"=>$reportOrderAmt->amount,
                    "payer_vpa"=>$reportOrderAmt->payer_vpa,
                    "payerAccName"=>$reportOrderAmt->payerAccName,
                    "status"=>$reportOrderAmt->status,
                    "payId"=>$reportOrderAmt->txnid,
                    "txnId"=>$reportOrderAmt->option1,
                    "bankTxnId"=>$reportOrderAmt->payid,
                    "npciTxnId"=>$reportOrderAmt->refno,
                    
                    ];
                    
                    return response()->json(['statuscode'=>'TXN', 'message'=> "Transction Found","data"=>$data]);
                }
                
                return response()->json(['statuscode'=>'TUP', 'message'=> "Transction status ".$upiOrders->status]);  
            }
           return response()->json(['statuscode'=>'TXF', 'message'=> "Transction Not Found"]);
        }
        
        $data = [
            "clientOrderId"=>$report->mytxnid,
            "amount"=>$report->amount,
            "payer_vpa"=>$report->payer_vpa,
            "payerAccName"=>$report->payerAccName,
            "status"=>$report->status,
            "payId"=>$report->txnid,
            "txnId"=>$report->option1,
            "bankTxnId"=>$report->payid,
            "npciTxnId"=>$report->refno,
            
            ];
            
        return response()->json(['statuscode'=>'TXN', 'message'=> "Transction Found","data"=>$data]);
   }
   public function statusCheckWeb(Request $post)
   {
        $rules = array(
            'extTransactionId'   => 'required'
        );
        
        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'statuscode' => 'BPR',
                'status' => 'Bad Parameter Request.',  
                'message' => $error
            ));
        }
        
        $report = \DB::table('upirorders')->whereTxnid($post->extTransactionId)->first();
        $currentTimestamp = now();
        $createdTimestamp = \Carbon\Carbon::parse($report->created_at);
        $diffInMinutes = $currentTimestamp->diffInMinutes($createdTimestamp);
        //dd([$createdTimestamp,$diffInMinutes]);
        // If more than 5 minutes have passed, set status to failed
        //dd($diffInMinutes);
        if ($diffInMinutes >= 5) {
            $report->status = 'failed';
        }

        $data = [
            "status"=>$report->status,
            "amount"=>$report->amount,
            "utr"=>$report->refno,
            "returnUrl"=>$report->description
            
            ];
        return response()->json(['statuscode'=>'TXN', 'status'=>$report->status,'data'=> $data]);
   }
   public function generateToken()
   {
        $AES_ENCRYPTION_KEY = '39451d4102bf7d4ef7b3dea195a56d27';
        $AES_ENCRYPTION_IV = 'f1d56c25b38d57f7'; 
        $datapost = [
            'client_secret' => '67ecefbd9b960d8e2fb49a40491a88050644de39097152d408612e31cc56bcda',
            'requestid'     => uniqid(), 
            'timestamp'     => time() 
        ];
        
        $jsonData = json_encode($datapost, true);
        
        $cipher = openssl_encrypt($jsonData, 'AES-256-CBC', $AES_ENCRYPTION_KEY, $options = OPENSSL_RAW_DATA, $AES_ENCRYPTION_IV);
        $finaldata = base64_encode($cipher);
        return $finaldata;
   }
   
   public function orderIdInitiate(Request $post, $orderId)
    {
        $order = \DB::table('upirorders')->where('txnid',$orderId)->whereStatus('initiated')->first();
        
        if(!$order){
          return response()->json(['statuscode'=>'ERR', 'message'=> "Order Id Not Found"]);  
        }
        $data['option1'] =$order->option1;
        $data['payid'] =$order->payid;
        $data['mytxnid'] =$order->mytxnid;
        $data['amount'] =$order->amount;
        $data['orderId'] =$orderId;
        return view("service.qrPay")->with($data);
    }
    
}