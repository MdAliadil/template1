<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Report;
use App\User;
use App\Model\Api;
use Carbon\Carbon;
use App\Model\Provider;
use App\Model\Cosmosmerchant;

class CronController extends Controller
{
    public function sessionClear()
  	{
	    $session = \DB::table('sessions')->where('last_activity' , '<', time()-3600)->delete();
  	}

  	public function appClear()
  	{
	    \DB::table('securedatas')->where('last_activity' , '<', time() - 3600)->delete();
  	}
  	public function passwordClear()
  	{
	    \DB::table('password_resets')->where('last_activity' , '<', time()-3600)->delete();
  	}

  	public function otpClear()
  	{
  		User::where('otpverify', '!=', 'yes')->update(['otpverify' => "yes", 'otpresend' => 0]);
  	}

  	public function recharge()
  	{
  		$reports = Report::where('product', 'recharge')->whereIn('status', ['pending', 'success'])->where('rtype', 'main')->whereIn('refno', ['', NULL])->take(50)->orderBy('id', 'DSEC')->get(['id', 'txnid', 'api_id']);

  		foreach ($reports as $report) {
  			switch ($report->api->code) {
					case 'recharge1':
						$url = $report->api->url.'/status?token='.$report->api->username.'&apitxnid='.$report->txnid;
						break;

					case 'recharge2':
						$url = $report->api->url.'rechargestatus.aspx?memberid='.$report->api->username."&pin=".$report->api->password.'&transid='.$report->txnid.'&format=json';
						break;
				}

	  		$result = \Myhelper::curl($url, "GET", "", []);

	  		if($result['response'] != ''){
				switch ($report->api->code) {
					case 'recharge1':
						$doc = json_decode($result['response']);
						if($doc->statuscode == "TXN" && ($doc->trans_status =="success" || $doc->trans_status =="pending")){
							$update['refno'] = $doc->refno;
							$update['status'] = "success";
						}elseif($doc->statuscode == "TXN" && $doc->trans_status =="reversed"){
							$update['status'] = "reversed";
							$update['refno'] = $doc->refno;
						}else{
							$update['status'] = "Unknown";
							$update['refno'] = $doc->message;
						}
						break;

					case 'recharge2':
						$doc = json_decode($result['response']);
						if(strtolower($doc->Status) == "success" || strtolower($doc->Status) == "pending"){
							$update['refno'] = $doc->OperatorRef;
							$update['status'] = "success";
						}elseif(strtolower($doc->Status) == "failed" || strtolower($doc->Status) == "failure" || strtolower($doc->Status) == "refund"){
							$update['status'] = "reversed";
							$update['refno'] = (isset($doc->ErrorMessage)) ? $doc->ErrorMessage : "failed";
						}else{
							$update['status'] = "Unknown";
							$update['refno'] = (isset($doc->ErrorMessage)) ? $doc->ErrorMessage : "Unknown";
						}
						break;
				}
			}
			if ($update['status'] != "Unknown") {
				$reportupdate = Report::where('id', $report->id)->update($update);
				if ($reportupdate && $update['status'] == "reversed") {
					\Myhelper::transactionRefund($report->id, "recharge");
				}
			}
  		}
  	}
  	
  	public function upiUpdate(Request $post){
  	   
        //$reports = Report::where('product', 'upicollect')->where('status', 'initiated')->whereIn('txnid',['ASPAY2023053100382766029389'])->where('rtype', 'main')->get(['id', 'txnid', 'api_id','user_id']);
        $reports = Report::where('product', 'upicollect')->where('status', 'initiated')->where('created_at', '<', now()->subMinutes(5))->where('rtype', 'main')->get(['id', 'txnid', 'api_id','user_id']);
        //dd($reports);
        foreach ($reports as $report) {
            $user = User::where('id',$report->user_id)->first();
            
            $api = Api::where('company_id',$user->company_id)->whereCode('cosmosupi')->first();
            $cosmosAgent = Cosmosmerchant::where('user_id',$report->user_id)->first();
            if(!$cosmosAgent){
               return response()->json(['statuscode'=>'ERR', 'message'=>"Merchant Not Registed with SID"]);    
            }
           $req = [
                'source' => $api->optional3,
                'channel' => 'api',
                "terminalId"=>$cosmosAgent->sid,
                'extTransactionId' => $report->txnid
            ];
        
            $checksum='';
            foreach ($req as $val){
                $checksum.=$val;
            }
            $checksum_string=$checksum.$api->optional1;
            $req['checksum']=hash('sha256',$checksum_string);
        
            $key= $api->username;
            $key=substr((hash('sha256',$key,true)),0,16);
        
            $cipher='AES-128-ECB';
            $encrypted_string=openssl_encrypt(
                json_encode($req),
                $cipher,
                $key
            );
        
        $url = 'https://merchantprod.timepayonline.com/evok/qr/v1/qrStatus';
            $header = array(
                        "Content-Type: text/plain",
                        "cid: ".$api->password
                     );
            $result = \Myhelper::curl($url, "POST",$encrypted_string, $header, "yes", 'cosmosStatusCheck', $report->txnid);
            if($result['response'] != ''){
                $response = $result['response'];
                $decrypted_string = openssl_decrypt($response,$cipher,$key);
                $json_data = json_decode($decrypted_string);
                //dd($json_data);
     
                if($json_data->status=="SUCCESS" && $json_data->data[0]->respMessge=="SUCCESS")
                {
                    $report = Report::where('txnid',$json_data->data[0]->extTransactionId)->where('status','initiated')->first();
                    // dd($report);  
                    if($report){
                        //dd($report);
                        $user  = User::where('id', $report->user_id)->first();
                        $provider = Provider::where('recharge1', 'upi')->first();
                        $post['provider_id'] = $provider->id;
                        $post['parent_id'] = $user->parent_id;
                        $parentUser = User::where('id',$post->parent_id)->first();
                        $mastermerchentCh = \Myhelper::getCommission($report->amount, $user->scheme_id, $post->provider_id, $parentUser->role->slug);
                        $mastermerchentGstCharge = $this->gstCharge($mastermerchentCh);
                        //$mastermerchentCh = \Myhelper::getCommission($report->amount, $user->scheme_id, $post->provider_id, $parentUser->role->slug);
                        $debitMaasterCharge = User::where('id',$parentUser->id)->decrement('mainwallet',$mastermerchentCh+$mastermerchentGstCharge);
                        $reselerUser = User::whereId($parentUser->parent_id)->first();
                        $resellerCommision = \Myhelper::getCommission($report->amount, $reselerUser->scheme_id, $post->provider_id, $reselerUser->role->slug);
                        User::where('id', $reselerUser->id)->increment('mainwallet', $resellerCommision );
                        $userCharge = $this->getUpiCharge($json_data->data[0]->amount,$user->updatemerchantcharge);
                       // \DB::table('paytmlogs')->insert(['response' => $userCharge, 'txnid' =>'cosmoscharge']);
                        $update = [
                            "status"=>"success",
                            "refno"=>$json_data->data[0]->custRefNo,
                            "payid"=>$json_data->data[0]->upiTxnId,
                            "charge"=>$mastermerchentCh,
                            "gst"=>$mastermerchentGstCharge,
                            "amount"=>$json_data->data[0]->amount,
                            "orderAmount"=>$report->amount,
                            "payer_vpa"=>$json_data->data[0]->upiId,
                            "payerAccName"=>$json_data->data[0]->customerName,
                            "authcode"=>$json_data->data[0]->txnTime,
                            "number"=>$json_data->data[0]->upiId
                        ];
                        
                        $update = \DB::table('reports')->whereTxnid($json_data->data[0]->extTransactionId)->where('status','initiated')->update($update);
                        
                        User::where('id', $report->user_id)->increment('mainwallet', $json_data->data[0]->amount-$userCharge );
                        $reportOrderAmt = \DB::table('reports')->whereTxnid($json_data->extTransactionId)->first();
                    if($user->role->slug == "apiuser"){
                        $output['status']      = "success";
                        $output['clientid']    = $report->mytxnid;
                        $output['txnid']       = $json_data->data[0]->extTransactionId;
                        $output['vpaadress']   = $json_data->data[0]->upiId;
                        $output['npciTxnId']   = $json_data->data[0]->custRefNo;
                        $output['amount']      = $json_data->data[0]->amount;
                        $output['bankTxnId']   = $json_data->data[0]->upiTxnId;
                        $output['payerVpa']    = $json_data->data[0]->upiId;
                        $output['payerAccName']= $json_data->data[0]->customerName;
                        $output['orderAmount']= $reportOrderAmt->orderAmount;
                        \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                    }
                }else{
                    $report = Report::where('txnid',$json_data->extTransactionId)->first();
                     $reportOrderAmt = \DB::table('reports')->whereTxnid($json_data->extTransactionId)->first();
                    if($user->role->slug == "apiuser"){
                        $output['status']      = "success";
                        $output['clientid']    = $report->mytxnid;
                        $output['txnid']       = $json_data->data[0]->extTransactionId;
                        $output['vpaadress']   = $json_data->data[0]->upiId;
                        $output['npciTxnId']   = $json_data->data[0]->custRefNo;
                        $output['amount']      = $json_data->data[0]->amount;
                        $output['bankTxnId']   = $json_data->data[0]->upiTxnId;
                        $output['payerVpa']    = $json_data->data[0]->upiId;
                        $output['payerAccName']= $json_data->data[0]->customerName;
                        $output['orderAmount']= $reportOrderAmt->orderAmount;
                        \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                    }
                }
                }
                else{
                    $report = Report::where('txnid',$json_data->extTransactionId)->where('status','initiated')->first();
                    // dd($report);  
                    if($report){
                        //dd($report);
                        $user  = User::where('id', $report->user_id)->first();
                        $provider = Provider::where('recharge1', 'upi')->first();
                        $post['provider_id'] = $provider->id;
                        $post['parent_id'] = $user->parent_id;
                        $parentUser = User::where('id',$post->parent_id)->first();
                        $reselerUser = User::whereId($parentUser->parent_id)->first();
                       // \DB::table('paytmlogs')->insert(['response' => $userCharge, 'txnid' =>'cosmoscharge']);
                        $update = [
                            "status"=>"failed",
                            "refno"=>$json_data->data[0]->custRefNo??$json_data->extTransactionId,
                            "payid"=>$json_data->data[0]->upiTxnId??$json_data->data[0]->respMessge,
                            "charge"=>'0',
                            "gst"=>"0",
                            "amount"=>$json_data->data[0]->amount??$report->amount,
                            "orderAmount"=>$report->amount,
                            "payer_vpa"=>$json_data->data[0]->upiId??'',
                            "payerAccName"=>$json_data->data[0]->customerName??'',
                            "authcode"=>$json_data->data[0]->txnTime??'',
                            "number"=>$json_data->data[0]->upiId??''
                        ];
                        
                        $update = \DB::table('reports')->whereTxnid($json_data->extTransactionId)->where('status','initiated')->update($update);
                        $reportOrderAmt = \DB::table('reports')->whereTxnid($json_data->extTransactionId)->first();
                        //User::where('id', $report->user_id)->increment('mainwallet', $json_data->amount-$userCharge );
                        
                    if($user->role->slug == "apiuser"){
                        $output['status'] = "failed";
                        $output['clientid']  = $report->mytxnid;
                        $output['txnid']     = $json_data->extTransactionId;
                        $output['vpaadress']   = $json_data->data[0]->upiId??'';
                        $output['npciTxnId']   = $json_data->data[0]->custRefNo??'';
                        $output['amount']   = $json_data->data[0]->amount??'0';
                        $output['bankTxnId']   = $json_data->extTransactionId;
                        $output['payerVpa']  = $json_data->data[0]->upiId??'';
                        $output['payerAccName']= $json_data->data[0]->customerName??'';
                        $output['orderAmount']= $reportOrderAmt->orderAmount;
                        
                        \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                    }
                }else{
                    $report = Report::where('txnid',$json_data->extTransactionId)->first();
                    $reportOrderAmt = \DB::table('reports')->whereTxnid($json_data->extTransactionId)->first();
                        //User::where('id', $report->user_id)->increment('mainwallet', $json_data->amount-$userCharge );
                        
                    if($user->role->slug == "apiuser"){
                        $output['status'] = "failed";
                        $output['clientid']  = $report->mytxnid;
                        $output['txnid']     = $json_data->extTransactionId;
                        $output['vpaadress']   = $json_data->data[0]->upiId??'';
                        $output['npciTxnId']   = $json_data->data[0]->custRefNo??'';
                        $output['amount']   = $json_data->data[0]->amount??'0';
                        $output['bankTxnId']   = $json_data->extTransactionId;
                        $output['payerVpa']  = $json_data->data[0]->upiId??'';
                        $output['payerAccName']= $json_data->data[0]->customerName??'';
                        $output['orderAmount']= $reportOrderAmt->orderAmount;
                        
                        \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                    }
                }
                }
            }    
           
        }
       
    }
    
    public function safepayUpiUpdate(Request $post){
        $startDate = '2024-04-06';
        $endDate = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $checkOrders = \DB::table('upirorders') ->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->where('status','initiated')->take(500)->get();
        //dd($checkOrders);
        $provider = Provider::where('recharge1', 'upi')->first();
        $post['provider_id'] = $provider->id;
        foreach($checkOrders as $checkOrder){
            $user = User::where('id',$checkOrder->credited_by)->first();
            $usercommission = \Myhelper::getCommission($checkOrder->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
            $usergstAmt =$usercommission*18/100;
            
            $url = "https://proapi.safepayindia.com/QRService.svc/StatusCheck";
            $params = [
                "order_id"=> $checkOrder->txnid
                
                ];
            
            $header = array(
                        "Content-Type: application/json",
                        /*"Client-ID:SFP_1UDDP668AV391UDDP668AV",
                        "Client-Secret:1UDDP668AV4W391UDDP668AV4W",*/
                        "Client-ID:SFP_FFZF96UNKP27FFZF96UNKP",
                        "Client-Secret:FFZF96UNKPDC27FFZF96UNKPDC",
                     );
                     
            $result = \Myhelper::curl($url, "POST",json_encode($params), $header, "yes", 'cosmosStatusCheck', $checkOrder->txnid);    
            //dd($result);
            if($result['response'] ==''){
                return response()->json(['statuscode'=>"ERR","message"=>"Handshake is not estabalish"]);
            }
            $doc = json_decode($result['response']);
            //dd($doc);
            if($doc->res_code =="1" && $doc->res_status =="SUCCESS")
            {
                $update = [
                    "status"=>"success",
                    "refno"=>$doc->utr,
                    "amount"=>$doc->amount,//number_format($post->amount, 0, '.', ''),
                    "orderAmount"=>$checkOrder->amount,
                    "payid"=>$doc->utr,
                    'option1'=>$doc->utr,
                    "charge"  => $usercommission,
                    "gst"  => '0',
                    "payer_vpa"=>$doc->payerVpa??'',
                    "payerAccName"=>$doc->payerAccName??'',
                    "number"=>$doc->vpaadress??'',
                    "statusCheck"=>'1',
                ];
                
                $update = \DB::table('upirorders')->whereTxnid($doc->orderid)->where('status','initiated')->update($update);
                $reportOrderAmt = \DB::table('upirorders')->whereTxnid($doc->orderid)->first();
                //dd($reportOrderAmt->txnid);
                $usercommission = \Myhelper::getCommission($doc->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                
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
                 $report =  \DB::table('upireports')->where('txnid',$reportOrderAmt->txnid)->first();
                 //dd($report);
                $totalCharge = $reportOrderAmt->charge+$reportOrderAmt->gst;
                User::where('id', $report->user_id)->increment('upiwallet', $doc->amount-$totalCharge);
               
                if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        //dd("3232323");
                            $output['status'] = "success";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $report->mytxnid;
                            $output['txnid']     = $report->txnid;
                            $output['vpaadress']   = $report->payeeVPA??'';
                            $output['npciTxnId']   = $doc->utr;
                            $output['payId']   = $reportOrderAmt->mytxnid;
                            $output['amount']   = $reportOrderAmt->amount;
                            $output['bankTxnId']   = $doc->utr;
                            $output['payerVpa']  = $post->payerVpa??'';
                            $output['payerAccName']= $post->payerAccName??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                            
                }
            }elseif(($doc->res_code =="2" && $doc->res_status =="CREATED")||($doc->res_code =="0" && $doc->res_status =="FAILED")){
                $update = [
                    "status"=>"failed",
                    "refno"=>$doc->utr,
                    "amount"=>$doc->amount,//number_format($post->amount, 0, '.', ''),
                    "orderAmount"=>$checkOrder->amount,
                    "payid"=>$doc->utr,
                    'option1'=>$doc->utr,
                    "charge"  => $usercommission,
                    "gst"  => '0',
                    "payer_vpa"=>$doc->payerVpa??'',
                    "payerAccName"=>$doc->payerAccName??'',
                    "number"=>$doc->vpaadress??'',
                    "statusCheck"=>'1',
                ];
                
                $update = \DB::table('upirorders')->whereTxnid($doc->orderid)->where('status','initiated')->update($update);
                $reportOrderAmt = \DB::table('upirorders')->whereTxnid($doc->orderid)->first();
                if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                    //dd("34344");
                   // $report =  \DB::table('upireports')->where('txnid',$reportOrderAmt->txnid)->first();
                        //dd("3232323");
                            $output['status'] = "failed";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $reportOrderAmt->mytxnid;
                            $output['txnid']     = $reportOrderAmt->txnid;
                            $output['vpaadress']   = $reportOrderAmt->payeeVPA??'';
                            $output['npciTxnId']   = $doc->utr;
                            $output['payId']   = $reportOrderAmt->mytxnid;
                            $output['amount']   = $reportOrderAmt->amount;
                            $output['bankTxnId']   = $doc->utr;
                            $output['payerVpa']  = $post->payerVpa??'';
                            $output['payerAccName']= $post->payerAccName??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $reportOrderAmt->mytxnid);
                          //  dd($user->callbackurl."?".http_build_query($output));
                            
                }
            }
            
        }
        
    }
    
    public function safepayPayoutUpdate(Request $post){
        \Log::info('Sechuduler Run ......');

        $startDate = '2024-09-28';
        $checkOrders = \DB::table('reports')->where('created_at', '>=', $startDate)->where('status','pending')->take(500)->get();
       // dd($checkOrders);
        foreach($checkOrders as $checkOrder){
            $user = User::where('id',$checkOrder->user_id)->first();
            
            
            $url = "https://proapi.safepayindia.com/PayoutService.svc/StatusCheck";
            $params = [
                "order_id"=> $checkOrder->txnid
                
                ];
            
            $header = array(
                        "Content-Type: application/json",
                        "Client-ID:SFP_LFJ1YYAX8A48LFJ1YYAX8A",
                        "Client-Secret:LFJ1YYAX8ASA48LFJ1YYAX8ASA",
                     );
                     
            $result = \Myhelper::curl($url, "POST",json_encode($params), $header, "yes", 'cosmosStatusCheck', $checkOrder->txnid);    
            //dd($result);
            if($result['response'] ==''){
                return response()->json(['statuscode'=>"ERR","message"=>"Handshake is not estabalish"]);
            }
            $doc = json_decode($result['response']);
            //dd($doc);
            $user = User::where('id', $checkOrder->user_id)->first();
            
            if($doc->res_code =="1" && $doc->res_status =="SUCCESS")
            {
                $update = [
                    "status"=>"success",
                    "refno"=>$doc->utr_no,
                    "payid"=>$doc->safepay_txnid
                ];
                
                $update = \DB::table('reports')->whereTxnid($checkOrder->txnid)->where('status','pending')->update($update);
                
               
                if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        
                          $output = array();
                          $output['status'] = 'TXN';
                          $output['statuscode'] = 'TXN';
                          $output['utr'] = $doc->utr_no??'';
                          $output['message'] = $data->res_message??'';
                          $output['amount'] = $doc->amount;
                          $output['clientTxnid'] = $checkOrder->apitxnid;
                          $output['apitxnid'] = $checkOrder->txnid;
                          $output['product'] = 'spayout';
                          //dd($post->user_id);
                            //$jsonResponse = response()->json($response);
                            
                          //dd($user);
                          \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $checkOrder->apitxnid); 
                            
                            
                }
            }elseif($doc->res_code =="0" || $doc->res_code =="-1" ||$doc->res_code =="4"){
                //dd("jhhjhj");
                \Myhelper::transactionRefund($checkOrder->id);
                        $update = [];
                         $update['status'] = "reversed";
                         $update['payid'] = $doc->safepay_txnid??"";
                    
                        Report::where(['id'=> $checkOrder->id])->update($update);
                        $output = array();
                          $output['status'] = 'TXF';
                          $output['statuscode'] = 'TXF';
                          $output['utr'] = $doc->utr_no??'';
                          $output['message'] = $data->res_message??'';
                          $output['amount'] = $doc->amount;
                          $output['clientTxnid'] = $checkOrder->apitxnid??'';
                          $output['apitxnid'] = $checkOrder->txnid??'';
                          $output['product'] = 'spayout';
                          //dd($post->user_id);
                           // $jsonResponse = response()->json($response);
                            $user = User::where('id', $checkOrder->user_id)->first();
                          //dd($user);
                          \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $checkOrder->apitxnid); 
                          
            }
            
        }
        
    }
    
    public function sprintUpiUpdate(Request $post){
        \Log::info("sprintUpiUpdate callback tigred");
        $startDate = '2024-05-22';
        $endDate = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $columns = ['id', 'txnid','amount','credited_by', 'status', 'created_at'];  // Add other necessary columns
        $checkOrders = \DB::table('upirorders')
                          ->select($columns)
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->where('status','initiated')
                          ->take(500)
                          ->get();
                          
        //dd($checkOrders);
        $provider = Provider::where('recharge1', 'upi')->first();
        $post['provider_id'] = $provider->id;
        foreach($checkOrders as $checkOrder){
            $user = User::where('id',$checkOrder->credited_by)->first();
            $usercommission = \Myhelper::getCommission($checkOrder->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
            $usergstAmt =$usercommission*18/100;
            
            $url = "https://api.sprintnxt.in/api/v2/UPIService/UPI";
            
            $req = [
                'apiId' => 20247,
                "bankId"=>3,
                "txnId"=>$checkOrder->txnid
                
            ];
            
            //dd(json_encode($req));
              $header = array(
                            'Content-Type: application/json',
                            'Client-id:U1BSX05YVF9wcm9kX2EzN2NjZDY5MmM2MWIzYWI=',
                            'token:'.$this->generateToken(),
                         ); 
             
            $result = \Myhelper::curl($url, "POST",json_encode($req), $header, "yes", 'SPRINT', $post->transction_id);   
            //dd($result);
            if($result['response'] ==''){
                return response()->json(['statuscode'=>"ERR","message"=>"Handshake is not estabalish"]);
            }
            $doc = json_decode($result['response']);
            //dd($doc);
            if($doc->status_code =="200" && $doc->status =="true" && $doc->data->status =="Success")
            {
                $update = [
                    "status"=>"success",
                    "refno"=>$doc->data->rrn_number,
                    "amount"=>$doc->data->payer_amount,//number_format($post->amount, 0, '.', ''),
                    "orderAmount"=>$checkOrder->amount,
                    "payid"=>$doc->data->txn_id,
                    //'option1'=>$doc->utr,
                    "charge"  => $usercommission,
                    "gst"  => '0',
                    "payer_vpa"=>$doc->data->payer_vpa??'',
                    "payerAccName"=>$doc->data->payer_name??'',
                    "number"=>$doc->data->payer_vpa??'',
                    "statusCheck"=>'1',
                ];
                
                $update = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->where('status','initiated')->update($update);
                $reportOrderAmt = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->first();
                //dd($reportOrderAmt->txnid);
                $usercommission = \Myhelper::getCommission($doc->data->payer_amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                
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
                 $report =  \DB::table('upireports')->where('txnid',$reportOrderAmt->txnid)->first();
                 //dd($report);
                $totalCharge = $reportOrderAmt->charge+$reportOrderAmt->gst;
                User::where('id', $report->user_id)->increment('upiwallet', $doc->data->payer_amount-$totalCharge);
               
                if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        //dd("3232323");
                            $output['status'] = "success";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $report->mytxnid;
                            $output['txnid']     = $report->txnid;
                            $output['vpaadress']   = $report->payeeVPA??'';
                            $output['npciTxnId']   = $doc->data->rrn_number;
                            $output['payId']   = $reportOrderAmt->mytxnid;
                            $output['amount']   = $reportOrderAmt->amount;
                            $output['bankTxnId']   = $doc->data->rrn_number;
                            $output['payerVpa']  = $doc->data->payer_vpa??'';
                            $output['payerAccName']= $doc->data->payer_name??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $reportOrderAmt->mytxnid);
                            
                }
            }elseif(($doc->status_code =="200" && $doc->status =="true" && ($doc->data->status !="Pending" || $doc->data->status !="Initiated"))){
                
               //dd($doc); 
                $update = [
                    "status"=>"failed",
                    "refno"=>$doc->data->rrn_number??'',
                    "amount"=>number_format( (float) $checkOrder->amount??'0', 2, '.', ''),//number_format($post->amount, 0, '.', ''),
                    "orderAmount"=>$checkOrder->amount??'',
                    "payid"=>$doc->data->txn_id??'',
                    //'option1'=>$doc->utr,
                    "charge"  => $usercommission,
                    "gst"  => '0',
                    "payer_vpa"=>$doc->data->payer_vpa??'',
                    "payerAccName"=>$doc->data->payer_name??'',
                    "number"=>$doc->data->payer_vpa??'',
                    "statusCheck"=>'1',
                ];
                
                
                $update = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->where('status','initiated')->update($update);
                
                $reportOrderAmt = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->first();
                
                $usercommission = \Myhelper::getCommission($checkOrder->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                
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
                    'status'  => 'failed',
                    "refno"=>$reportOrderAmt->refno,
                    'description'  => $reportOrderAmt->description,
                    'credited_by' => $reportOrderAmt->credited_by,
                    //'balance'     => $user->mainwallet,
                    'provider_id' => $reportOrderAmt->provider_id,
                    'product'    => "upicollect"
                ];
                
                 \DB::table('upireports')->insert($insert);
                if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                    //dd("34344");
                   // $report =  \DB::table('upireports')->where('txnid',$reportOrderAmt->txnid)->first();
                        //dd("3232323");
                            $output['status'] = "failed";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $reportOrderAmt->mytxnid;
                            $output['txnid']     = $reportOrderAmt->txnid;
                            $output['vpaadress']   = $reportOrderAmt->payeeVPA??'';
                            $output['npciTxnId']   = $doc->data->rrn_number;
                            $output['payId']   = $reportOrderAmt->mytxnid;
                            $output['amount']   = $reportOrderAmt->amount;
                            $output['bankTxnId']   = $doc->data->rrn_number;
                            $output['payerVpa']  = $doc->data->payer_vpa??'';
                            $output['payerAccName']= $doc->data->payer_name??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $reportOrderAmt->mytxnid);
                          //  dd($user->callbackurl."?".http_build_query($output));
                            
                }
            }
            
        }
        
    }
    
    public function sprintUpiUpdatelattest(Request $post){
        //\Log::info("sprintUpiUpdatelattest callback tigred");
         
        $startDate = '2024-05-22';
        $endDate = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $columns = ['id', 'txnid','amount','credited_by', 'status', 'created_at'];  // Add other necessary columns
        $checkOrders = \DB::table('upirorders')
                          ->select($columns)
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->where('status','initiated')
                          ->orderBy('created_at', 'desc')
                          ->take(500)
                          ->get();
                          
        //dd($checkOrders);
        $provider = Provider::where('recharge1', 'upi')->first();
        $post['provider_id'] = $provider->id;
        foreach($checkOrders as $checkOrder){
            $user = User::where('id',$checkOrder->credited_by)->first();
            $usercommission = \Myhelper::getCommission($checkOrder->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
            $usergstAmt =$usercommission*18/100;
            
            $url = "https://api.sprintnxt.in/api/v2/UPIService/UPI";
            
            $req = [
                'apiId' => 20247,
                "bankId"=>3,
                "txnId"=>$checkOrder->txnid
                
            ];
            
            //dd(json_encode($req));
              $header = array(
                            'Content-Type: application/json',
                            'Client-id:U1BSX05YVF9wcm9kX2EzN2NjZDY5MmM2MWIzYWI=',
                            'token:'.$this->generateToken(),
                         ); 
             
            $result = \Myhelper::curl($url, "POST",json_encode($req), $header, "yes", 'SPRINT', $post->transction_id);   
            //dd($result);
            if($result['response'] ==''){
                return response()->json(['statuscode'=>"ERR","message"=>"Handshake is not estabalish"]);
            }
            $doc = json_decode($result['response']);
            //dd($doc);
            if($doc->status_code =="200" && $doc->status =="true" && $doc->data->status =="Success")
            {
                $update = [
                    "status"=>"success",
                    "refno"=>$doc->data->rrn_number,
                    "amount"=>$doc->data->payer_amount,
                    "orderAmount"=>$checkOrder->amount,
                    "payid"=>$doc->data->txn_id,
                    "charge"  => $usercommission,
                    "gst"  => '0',
                    "payer_vpa"=>$doc->data->payer_vpa??'',
                    "payerAccName"=>$doc->data->payer_name??'',
                    "number"=>$doc->data->payer_vpa??'',
                    "statusCheck"=>'1',
                ];
                
                $update = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->where('status','initiated')->update($update);
                $reportOrderAmt = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->first();
                
                $usercommission = \Myhelper::getCommission($doc->data->payer_amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                
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
                 $report =  \DB::table('upireports')->where('txnid',$reportOrderAmt->txnid)->first();
                 //dd($report);
                $totalCharge = $reportOrderAmt->charge+$reportOrderAmt->gst;
                User::where('id', $report->user_id)->increment('upiwallet', $doc->data->payer_amount-$totalCharge);
               
                if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        //dd("3232323");
                            $output['status'] = "success";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $report->mytxnid;
                            $output['txnid']     = $report->txnid;
                            $output['vpaadress']   = $report->payeeVPA??'';
                            $output['npciTxnId']   = $doc->data->rrn_number;
                            $output['payId']   = $reportOrderAmt->mytxnid;
                            $output['amount']   = $reportOrderAmt->amount;
                            $output['bankTxnId']   = $doc->data->rrn_number;
                            $output['payerVpa']  = $doc->data->payer_vpa??'';
                            $output['payerAccName']= $doc->data->payer_name??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $reportOrderAmt->mytxnid);
                            
                }
            }elseif(($doc->status_code =="200" && $doc->status =="true" && ($doc->data->status !="Pending" || $doc->data->status !="Initiated"))){
                
               //dd($doc); 
                $update = [
                    "status"=>"failed",
                    "refno"=>$doc->data->rrn_number??'',
                    "amount"=>number_format( (float) $checkOrder->amount??'0', 2, '.', ''),//number_format($post->amount, 0, '.', ''),
                    "orderAmount"=>$checkOrder->amount??'',
                    "payid"=>$doc->data->txn_id??'',
                    //'option1'=>$doc->utr,
                    "charge"  => $usercommission,
                    "gst"  => '0',
                    "payer_vpa"=>$doc->data->payer_vpa??'',
                    "payerAccName"=>$doc->data->payer_name??'',
                    "number"=>$doc->data->payer_vpa??'',
                    "statusCheck"=>'1',
                ];
                
                
                $update = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->where('status','initiated')->update($update);
                
                $reportOrderAmt = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->first();
                
                $usercommission = \Myhelper::getCommission($checkOrder->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                
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
                    'status'  => 'failed',
                    "refno"=>$reportOrderAmt->refno,
                    'description'  => $reportOrderAmt->description,
                    'credited_by' => $reportOrderAmt->credited_by,
                    //'balance'     => $user->mainwallet,
                    'provider_id' => $reportOrderAmt->provider_id,
                    'product'    => "upicollect"
                ];
                
                 \DB::table('upireports')->insert($insert);
                if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                    //dd("34344");
                   // $report =  \DB::table('upireports')->where('txnid',$reportOrderAmt->txnid)->first();
                        //dd("3232323");
                            $output['status'] = "failed";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $reportOrderAmt->mytxnid;
                            $output['txnid']     = $reportOrderAmt->txnid;
                            $output['vpaadress']   = $reportOrderAmt->payeeVPA??'';
                            $output['npciTxnId']   = $doc->data->rrn_number;
                            $output['payId']   = $reportOrderAmt->mytxnid;
                            $output['amount']   = $reportOrderAmt->amount;
                            $output['bankTxnId']   = $doc->data->rrn_number;
                            $output['payerVpa']  = $doc->data->payer_vpa??'';
                            $output['payerAccName']= $doc->data->payer_name??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $reportOrderAmt->mytxnid);
                          //  dd($user->callbackurl."?".http_build_query($output));
                            
                }
            }
            
        }
        
    }
    
    
    public function sprintUpioneByone(Request $post, $txnid){
        //\Log::info("sprintUpiUpdatelattest callback tigred");
         
       
        $columns = ['id', 'txnid','amount','credited_by', 'status', 'created_at'];  // Add other necessary columns
        $checkOrder = \DB::table('upirorders')
                          ->select($columns)
                          ->where('txnid',$txnid)
                          ->orderBy('created_at', 'desc')
                          ->take(100)
                          ->first();
                          
        //dd($checkOrders);
        $provider = Provider::where('recharge1', 'upi')->first();
        $post['provider_id'] = $provider->id;
            $user = User::where('id',$checkOrder->credited_by)->first();
            $usercommission = \Myhelper::getCommission($checkOrder->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
            $usergstAmt =$usercommission*18/100;
            
            $url = "https://api.sprintnxt.in/api/v2/UPIService/UPI";
            
            $req = [
                'apiId' => 20247,
                "bankId"=>3,
                "txnId"=>$checkOrder->txnid
                
            ];
            
            //dd(json_encode($req));
              $header = array(
                            'Content-Type: application/json',
                            'Client-id:U1BSX05YVF9wcm9kX2EzN2NjZDY5MmM2MWIzYWI=',
                            'token:'.$this->generateToken(),
                         ); 
             
            $result = \Myhelper::curl($url, "POST",json_encode($req), $header, "yes", 'SPRINT', $post->transction_id);   
            //dd($result);
            if($result['response'] ==''){
                return response()->json(['statuscode'=>"ERR","message"=>"Handshake is not estabalish"]);
            }
            $doc = json_decode($result['response']);
            //dd($doc);
            if($doc->status_code =="200" && $doc->status =="true" && $doc->data->status =="Success")
            {
                $update = [
                    "status"=>"success",
                    "refno"=>$doc->data->rrn_number,
                    "amount"=>$doc->data->payer_amount,
                    "orderAmount"=>$checkOrder->amount,
                    "payid"=>$doc->data->txn_id,
                    "charge"  => $usercommission,
                    "gst"  => '0',
                    "payer_vpa"=>$doc->data->payer_vpa??'',
                    "payerAccName"=>$doc->data->payer_name??'',
                    "number"=>$doc->data->payer_vpa??'',
                    "statusCheck"=>'1',
                ];
                
                $update = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->where('status','initiated')->update($update);
                $reportOrderAmt = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->first();
                
                $usercommission = \Myhelper::getCommission($doc->data->payer_amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                
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
                 $report =  \DB::table('upireports')->where('txnid',$reportOrderAmt->txnid)->first();
                 //dd($report);
                $totalCharge = $reportOrderAmt->charge+$reportOrderAmt->gst;
                User::where('id', $report->user_id)->increment('upiwallet', $doc->data->payer_amount-$totalCharge);
               
                if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        //dd("3232323");
                            $output['status'] = "success";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $report->mytxnid;
                            $output['txnid']     = $report->txnid;
                            $output['vpaadress']   = $report->payeeVPA??'';
                            $output['npciTxnId']   = $doc->data->rrn_number;
                            $output['payId']   = $reportOrderAmt->mytxnid;
                            $output['amount']   = $reportOrderAmt->amount;
                            $output['bankTxnId']   = $doc->data->rrn_number;
                            $output['payerVpa']  = $doc->data->payer_vpa??'';
                            $output['payerAccName']= $doc->data->payer_name??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $reportOrderAmt->mytxnid);
                            
                }
            }elseif(($doc->status_code =="200" && $doc->status =="true" && ($doc->data->status !="Pending" || $doc->data->status !="Initiated"))){
                
               //dd($doc); 
                $update = [
                    "status"=>"failed",
                    "refno"=>$doc->data->rrn_number??'',
                    "amount"=>number_format( (float) $checkOrder->amount??'0', 2, '.', ''),//number_format($post->amount, 0, '.', ''),
                    "orderAmount"=>$checkOrder->amount??'',
                    "payid"=>$doc->data->txn_id??'',
                    //'option1'=>$doc->utr,
                    "charge"  => $usercommission,
                    "gst"  => '0',
                    "payer_vpa"=>$doc->data->payer_vpa??'',
                    "payerAccName"=>$doc->data->payer_name??'',
                    "number"=>$doc->data->payer_vpa??'',
                    "statusCheck"=>'1',
                ];
                
                
                $update = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->where('status','initiated')->update($update);
                
                $reportOrderAmt = \DB::table('upirorders')->whereTxnid($doc->data->qr_refid)->first();
                
                $usercommission = \Myhelper::getCommission($checkOrder->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                
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
                    'status'  => 'failed',
                    "refno"=>$reportOrderAmt->refno,
                    'description'  => $reportOrderAmt->description,
                    'credited_by' => $reportOrderAmt->credited_by,
                    //'balance'     => $user->mainwallet,
                    'provider_id' => $reportOrderAmt->provider_id,
                    'product'    => "upicollect"
                ];
                
                 \DB::table('upireports')->insert($insert);
                if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                    //dd("34344");
                   // $report =  \DB::table('upireports')->where('txnid',$reportOrderAmt->txnid)->first();
                        //dd("3232323");
                            $output['status'] = "failed";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $reportOrderAmt->mytxnid;
                            $output['txnid']     = $reportOrderAmt->txnid;
                            $output['vpaadress']   = $reportOrderAmt->payeeVPA??'';
                            $output['npciTxnId']   = $doc->data->rrn_number;
                            $output['payId']   = $reportOrderAmt->mytxnid;
                            $output['amount']   = $reportOrderAmt->amount;
                            $output['bankTxnId']   = $doc->data->rrn_number;
                            $output['payerVpa']  = $doc->data->payer_vpa??'';
                            $output['payerAccName']= $doc->data->payer_name??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $reportOrderAmt->mytxnid);
                          //  dd($user->callbackurl."?".http_build_query($output));
                            
                }
            }
            
        
        
    }
    
    public function failedPayout(Request $post){
        $reports = \DB::table('reports')->where('status','pending')->where('product','payout')
                    ->whereBetween('id', ['195459', '196963'])
                    ->get();
        //dd($reports);
        foreach($reports as $report){
            \Myhelper::transactionRefund($report->id);
                         $update['status'] = "reversed";
                         $update['payid'] ='';
                    
                        /* Report::where('txnid', $report->payoutid)->update([
                            'status' => 'reversed',
                            'refno' => $post->result['rrn']??$post->data['reason']
                        ]);*/
                        Report::where(['id'=> $report->id])->update($update);
                    
                    
                        
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXF';
                       $output['statuscode'] = 'TXF';
                       $output['utr'] = '';
                       $output['message'] = 'Transction failed';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "B-PayoutCallback", $report->apitxnid); 
        }
    }
    
    public function checkSafepayOrder(Request $post){
        $now = Carbon::now();
        $tenMinutesAgo = $now->subMinutes(60);
        $data = \DB::table('upirorders')->where('created_at', '>=', $tenMinutesAgo)->get();
                
        //dd($data);        
    }
    
    public function getUpiCharge($amount,$usersetCommision)
    {
       
            $charge = $amount*$usersetCommision/100;
            return $charge;
        
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
    
    public function gstCharge($amount)
    {
       
            $charge = $amount*18/100;
            return $charge;
        
    }
}