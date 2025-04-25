<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Report;
use App\Model\Utiid;
use App\Model\Aepsfundrequest;
use App\Model\Aepsreport;
use App\Model\Upireport;
use App\Model\Company;
use App\Model\Provider;
use App\Model\Api;
use App\User;
use Carbon\Carbon;


class CallbackController extends Controller
{
    public function callback(Request $post, $api)
    {
        switch ($api) {
            case 'blinkpe':
                \DB::table('microlog')->insert(['response' => json_encode($post->all()),'product'=>'blinkpe']);
                
                break;
            case 'payconpayin':
               \DB::table('paytmlogs')->insert(['response' => json_encode($post->all())]);
                    $report = \DB::table('upirorders')->where('txnid',$post->payin_ref)->first();
                    if($report->status =='initiated' && $post->response_code=="1"){
                          
                            //dd($report);
                            $user  = User::where('id', $report->user_id)->first();
                            $provider = Provider::where('recharge1', 'upi')->first();
                            $post['provider_id'] = $provider->id;
                            //$post['parent_id'] = $user->parent_id;
                           // $parentUser = User::where('id',$user->id)->first();
                            //$mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            
                            //User::where('id', $user->id)->increment('upiwallet', $post->amount );
                            
                            $usercommission = \Myhelper::getCommission($post->Amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            $usergstAmt =$usercommission*18/100;
                          
                            $update = [
                                "status"=>"success",
                                "refno"=>$post->rrn,
                                "amount"=>$post->Amount,//number_format($post->amount, 0, '.', ''),
                                "orderAmount"=>$report->amount,
                                "payid"=>$post->payin_ref,
                                'option1'=>$post->txnid,
                                "charge"  => $usercommission,
                                "gst"  => '0',
                                "payer_vpa"=>$post->payerVpa??'',
                                "payerAccName"=>$post->payerAccName??'',
                                "number"=>$post->vpaadress??''
                            ];
                            //dd($update);
                            $update = \DB::table('upirorders')->whereTxnid($post->payin_ref)->where('status','initiated')->update($update);
                            $reportOrderAmt = \DB::table('upirorders')->whereTxnid($post->payin_ref)->first();
                            $usercommission = \Myhelper::getCommission($post->Amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            
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
                            User::where('id', $report->user_id)->increment('upiwallet', $post->Amount-$totalCharge);
                           // dd($user->role->slug);
                        if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                            //dd("3232323");
                                $output['status'] = "success";
                                $output['clientid']  = $report->mytxnid;
                                $output['txnid']     = $post->payin_ref;
                                $output['vpaadress']   = $post->vpaadress??'';
                                $output['npciTxnId']   = $post->rrn;
                                $output['payId']   = $post->payin_ref;
                                $output['amount']   = $post->Amount;
                                $output['bankTxnId']   = $post->bankTxnId??'';
                                $output['payerVpa']  = $post->payerVpa??'';
                                $output['payerAccName']= $post->payerAccName??'';
                                $output['orderAmount']= $reportOrderAmt->orderAmount??'';
                                /*if($post->ip() =="45.248.27.202"){
                                  dd(http_build_query($output));
                                }*/
                                \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                                
                            }
                         
                    }
                
                break;
            case 'payconpayout':
                \DB::table('microlog')->insert(['response' => json_encode($post->all()),'product'=>'payconpayout']);
               
                $report = Report::where('status','pending')->where('txnid', $post->payout_ref)->first();
                //dd($report);
                if($report){
                    if($post->response_code == "1"){
                        //dd("asasa");
                        Report::where('txnid', $post->payout_ref)->update([
                             'status' => 'success',
                             'refno'  => $post->rrn,
                             'payid'=>$post->rrn
                        ]);
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXN';
                       $output['statuscode'] = 'TXN';
                       $output['utr'] = $post->rrn??'';
                       $output['message'] = $post->message??'';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->payoutcallbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $report->apitxnid); 
                    }elseif (($post->response_code) == "2") {
                        \Myhelper::transactionRefund($report->id);
                         $update['status'] = "reversed";
                         $update['payid'] = $post->rrn??"";
                    
                        Report::where(['id'=> $report->id])->update($update);
                    
                    
                        
                        
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXF';
                       $output['statuscode'] = 'TXF';
                       $output['utr'] = $post->rrn??'';
                       $output['message'] = $post->message??'';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apiTxnId;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->payoutcallbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $report->apitxnid); 
                    }
                }
                break;
            
            case 'cspayout':
                \DB::table('microlog')->insert(['response' => json_encode($post->all()),'product'=>'cspayout']);
                $report = Report::where('status','pending')->where('txnid', $post->clientTxnid)->first();
                //dd($report);
                if($report){
                    if($post->statuscode == "TXN"){
                        //dd("asasa");
                        Report::where('txnid', $post->clientTxnid)->update([
                             'status' => 'success',
                             'refno'  => $post->utr,
                             'payid'=>$post->apitxnid
                        ]);
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXN';
                       $output['statuscode'] = 'TXN';
                       $output['utr'] = $post->utr??'';
                       $output['message'] = $post->message??'';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->payoutcallbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $report->apitxnid); 
                    }elseif (($post->statuscode) == "TXF") {
                        \Myhelper::transactionRefund($report->id);
                         $update['status'] = "reversed";
                         $update['payid'] = $post->apitxnid??"";
                    
                        Report::where(['id'=> $report->id])->update($update);
                    
                    
                        
                        
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXF';
                       $output['statuscode'] = 'TXF';
                       $output['utr'] = $post->utr??'';
                       $output['message'] = $post->message??'';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apiTxnId;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->payoutcallbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $report->apitxnid); 
                    }
                }
                break; 
            
            case 'csupi';
                    \DB::table('paytmlogs')->insert(['response' => json_encode($post->all())]);
                    $report = \DB::table('upirorders')->where('txnid',$post->clientid)->first();
                    if($report->status =='initiated' && $post->status=="success"){
                          
                            //dd($report);
                            $user  = User::where('id', $report->user_id)->first();
                            $provider = Provider::where('recharge1', 'upi')->first();
                            $post['provider_id'] = $provider->id;
                            //$post['parent_id'] = $user->parent_id;
                           // $parentUser = User::where('id',$user->id)->first();
                            //$mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            
                            //User::where('id', $user->id)->increment('upiwallet', $post->amount );
                            
                            $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            $usergstAmt =$usercommission*18/100;
                          
                            $update = [
                                "status"=>"success",
                                "refno"=>$post->npciTxnId,
                                "amount"=>$post->amount,//number_format($post->amount, 0, '.', ''),
                                "orderAmount"=>$report->amount,
                                "payid"=>$post->bankTxnId,
                                'option1'=>$post->txnid,
                                "charge"  => $usercommission,
                                "gst"  => '0',
                                "payer_vpa"=>$post->payerVpa,
                                "payerAccName"=>$post->payerAccName,
                                "number"=>$post->vpaadress
                            ];
                            //dd($update);
                            $update = \DB::table('upirorders')->whereTxnid($post->clientid)->where('status','initiated')->update($update);
                            $reportOrderAmt = \DB::table('upirorders')->whereTxnid($post->clientid)->first();
                            $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            
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
                            User::where('id', $report->user_id)->increment('upiwallet', $post->amount-$totalCharge);
                           // dd($user->role->slug);
                        if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                            //dd("3232323");
                                $output['status'] = "success";
                                $output['clientid']  = $report->mytxnid;
                                $output['txnid']     = $post->txnid;
                                $output['vpaadress']   = $post->vpaadress;
                                $output['npciTxnId']   = $post->npciTxnId;
                                $output['payId']   = $post->clientid;
                                $output['amount']   = $post->amount;
                                $output['bankTxnId']   = $post->bankTxnId;
                                $output['payerVpa']  = $post->payerVpa;
                                $output['payerAccName']= $post->payerAccName;
                                $output['orderAmount']= $reportOrderAmt->orderAmount;
                                /*if($post->ip() =="45.248.27.202"){
                                  dd(http_build_query($output));
                                }*/
                                \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                                
                            }
                         
                    }elseif($report->status =='initiated' && $post->status=="failed"){
                       //dd($report);
                            $user  = User::where('id', $report->user_id)->first();
                            
                            
                          
                            $update = [
                                "status"=>"failed",
                                "refno"=>$post->npciTxnId??"",
                                "amount"=>$post->amount,//number_format($post->amount, 0, '.', ''),
                                "orderAmount"=>$report->amount,
                                "payid"=>$post->bankTxnId??"",
                                'option1'=>$post->txnid??"",
                                "charge"  => $usercommission??"0",
                                "gst"  => '0',
                                "payer_vpa"=>$post->payerVpa??"",
                                "payerAccName"=>$post->payerAccName??"",
                                "number"=>$post->vpaadress??""
                            ];
                            //dd($update);
                            $update = \DB::table('upirorders')->whereTxnid($post->clientid)->where('status','initiated')->update($update);
                            
                        if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                            //dd("3232323");
                                $output['status'] = "failed";
                                $output['clientid']  = $report->mytxnid;
                                $output['txnid']     = $post->txnid??"";
                                $output['vpaadress']   = $post->vpaadress??"";
                                $output['npciTxnId']   = $post->npciTxnId??"";
                                $output['payId']   = $post->clientid??"";
                                $output['amount']   = $post->amount??"";
                                $output['bankTxnId']   = $post->bankTxnId??"";
                                $output['payerVpa']  = $post->payerVpa??"";
                                $output['payerAccName']= $post->payerAccName??"";
                                $output['orderAmount']= $reportOrderAmt->orderAmount??"";
                                /*if($post->ip() =="45.248.27.202"){
                                  dd(http_build_query($output));
                                }*/
                                \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                                
                            } 
                    }
                break;    
            case 'payout':
                \DB::table('paytmlogs')->insert(['response' => json_encode($post->all()), 'txnid' => $post->result['orderId']]);
                $fundreport = Aepsfundrequest::where('payoutid', $post->txnid)->first();
                if($fundreport && in_array($fundreport->status , ['pending', 'approved'])){
                    if(strtolower($post->status) == "success"){
                        $update['status'] = "approved";
                        $update['payoutref'] = $post->refno;
                    }elseif (strtolower($post->status) == "reversed") {
                        $update['status'] = "rejected";
                        $update['payoutref'] = $post->refno;
                    }else{
                        $update['status'] = "pending";
                    }
                    
                    if($update['status'] != "pending"){
                        $action = Aepsfundrequest::where('id', $fundreport->id)->update($update);
                        if ($action) {
                            if($update['status'] == "rejected"){
                                $report = Report::where('txnid', $fundreport->payoutid)->update(['status' => "reversed"]);
                                $report = Report::where('txnid', $fundreport->payoutid)->first();
                                $aepsreports['api_id'] = $report->api_id;
                                $aepsreports['payid']  = $report->payid;
                                $aepsreports['mobile'] = $report->mobile;
                                $aepsreports['refno']  = $report->refno;
                                $aepsreports['aadhar'] = $report->aadhar;
                                $aepsreports['amount'] = $report->amount;
                                $aepsreports['charge'] = $report->charge;
                                $aepsreports['bank']   = $report->bank;
                                $aepsreports['txnid']  = $report->id;
                                $aepsreports['user_id']= $report->user_id;
                                $aepsreports['credited_by'] = $report->credited_by;
                                $aepsreports['balance']     = $report->user->mainwallet;
                                $aepsreports['type']        = "credit";
                                $aepsreports['transtype']   = 'fund';
                                $aepsreports['status'] = 'refunded';
                                $aepsreports['remark'] = "Bank Settlement";
                                Report::create($aepsreports);
                                User::where('id', $aepsreports['user_id'])->increment('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                            }
                        }
                    }
                }
                break;
            case 'ppayout':
                \DB::table('paytmlogs')->insert(['response' => json_encode($post->all()), 'txnid' => $post->result['orderId']]);
                $report = Aepsfundrequest::where('payoutid', $post->result['orderId'])->first();
                if($report && in_array($report->status , ['success','pending'])){
                if($report){
                    if(strtolower($post->status) == "success"){
                        Report::where('txnid', $report->payoutid)->update([
                             'status' => 'success',
                             'refno'  => $post->result['rrn']
                        ]);
        
                        Aepsfundrequest::where('payoutid', $post->result['orderId'])->update([
                            'payoutref' => $post->result['rrn'],
                            'status'    => 'approved'
                        ]);
                    }elseif (strtolower($post->status) == "failure") {
                        Report::where('txnid', $report->payoutid)->update([
                            'status' => 'reversed',
                            'refno' => $post->result['rrn']
                        ]);
                        
                        Aepsfundrequest::where('id', $report->id)->update(['status' => "rejected", 'payoutref' => $post->result['rrn']]);
                        $aepsreport = Report::where('txnid', $report->payoutid)->first();
                        $aepsreports['api_id'] = $aepsreport->api_id;
                        $aepsreports['payid']  = $aepsreport->payid;
                        $aepsreports['mobile'] = $aepsreport->mobile;
                        $aepsreports['refno']  = $aepsreport->refno;
                        $aepsreports['aadhar'] = $aepsreport->aadhar;
                        $aepsreports['amount'] = $aepsreport->amount;
                        $aepsreports['charge'] = $aepsreport->charge;
                        $aepsreports['bank']   = $aepsreport->bank;
                        $aepsreports['txnid']  = $aepsreport->id;
                        $aepsreports['user_id']= $aepsreport->user_id;
                        $aepsreports['credited_by'] = $aepsreport->credited_by;
                        $aepsreports['balance']     = $aepsreport->user->mainwallet;
                        $aepsreports['type']        = "credit";
                        $aepsreports['transtype']   = 'fund';
                        $aepsreports['status'] = 'refunded';
                        $aepsreports['remark'] = "Bank Settlement Refunded";
        
                        User::where('id', $aepsreports['user_id'])->increment('mainwallet', $aepsreports['amount'] + $aepsreports['charge']);
                        Report::create($aepsreports);
                    }
                }
                }
                break;
                
            case 'mmoney':
                \DB::table('paytmlogs')->insert(['response' => 'mmoney'.json_encode($post->all()), 'txnid' =>'7676']);
                $report = Report::where('status','pending')->where('txnid', $post->clientid)->first();
                if($report){
                    if(strtolower($post->status) == "success"){
                        Report::where('txnid', $post->clientid)->update([
                             'status' => 'success',
                             'refno'  => $post->utr,
                             'payid'=>$post->txnid
                        ]);
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXN';
                       $output['statuscode'] = 'TXN';
                       $output['utr'] = $post->utr??'';
                       $output['message'] = $post->status??'';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $report->apitxnid); 
                    }elseif (($post->status) == "failed" ||($post->status) == "failure" ) {
                        \Myhelper::transactionRefund($report->id);
                         $update['status'] = "reversed";
                         $update['payid'] = $post->result['rrn']??$post->data['reason'];
                    
                        Report::where(['id'=> $report->id])->update($update);
                    
                    
                        
                        
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXF';
                       $output['statuscode'] = 'TXF';
                       $output['utr'] = $post->utr??'';
                       $output['message'] = $post->status??'';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $report->apitxnid); 
                    }
                }
                
                break;
                
            case 'spayout':
                //dd($post->data);
                \DB::table('microlog')->insert(['response' => json_encode($post->all()),'product'=>'Payout']);
                $report = Report::where('status','pending')->where('txnid', $post->data['clientRefId'])->first();
                //dd($report);
                if($report){
                    if(strtolower($post->code) == "0x0200" && $post->data['status']=="processed"){
                        Report::where('txnid', $post->data['clientRefId'])->update([
                             'status' => 'success',
                             'refno'  => $post->data['utr']
                        ]);
                        $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXN';
                       $output['statuscode'] = 'TXN';
                       $output['utr'] = $post->data['utr']??'';
                       $output['message'] = $post->message??'';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $report->apitxnid); 
                    }elseif (($post->code) != "0x0200") {
                       // dd("sdsdsd");
                        
                        
                         \Myhelper::transactionRefund($report->id);
                         $update['status'] = "reversed";
                         $update['payid'] = $post->result['rrn']??$post->data['reason'];
                    
                        /* Report::where('txnid', $report->payoutid)->update([
                            'status' => 'reversed',
                            'refno' => $post->result['rrn']??$post->data['reason']
                        ]);*/
                        Report::where(['id'=> $report->id])->update($update);
                    
                    
                        /*//Aepsfundrequest::where('id', $report->id)->update(['status' => "rejected", 'payoutref' => $post->result['rrn']]);
                        $aepsreport = Report::where('txnid', $report->payoutid)->first();
                        $aepsreports['api_id'] = $aepsreport->api_id;
                        $aepsreports['payid']  = $aepsreport->payid;
                        $aepsreports['mobile'] = $aepsreport->mobile;
                        $aepsreports['refno']  = $aepsreport->refno;
                        $aepsreports['aadhar'] = $aepsreport->aadhar;
                        $aepsreports['amount'] = $aepsreport->amount;
                        $aepsreports['charge'] = $aepsreport->charge;
                        $aepsreports['bank']   = $aepsreport->bank;
                        $aepsreports['txnid']  = $aepsreport->id;
                        $aepsreports['user_id']= $aepsreport->user_id;
                        $aepsreports['credited_by'] = $aepsreport->credited_by;
                        $aepsreports['balance']     = $aepsreport->user->mainwallet;
                        $aepsreports['type']        = "credit";
                        $aepsreports['transtype']   = 'fund';
                        $aepsreports['status'] = 'refunded';
                        $aepsreports['remark'] = "Bank Settlement Refunded";
        
                        User::where('id', $aepsreports['user_id'])->increment('mainwallet', $aepsreports['amount'] + $aepsreports['charge']);
                        Report::create($aepsreports);*/
                        
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXF';
                       $output['statuscode'] = 'TXF';
                       $output['utr'] = $post->data['utr']??'';
                       $output['message'] = $post->message??'';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $report->apitxnid); 
                    }
                }
                
                break;    
             case 'vyperblinkpe':
                \DB::table('paytmlogs')->insert(['response' => json_encode($post->all()), 'txnid' => $post->result['orderId']??'23232323']);
                $findreport = \DB::table('idfcreports')->where('txnid',$post->order_id)->where('status','initiated')->first();
                if(!$findreport){
                    return response()->json(['status'=>'ERR','message'=>'Order Id not found']);
                }
                
                //status=SUCCESS&order_id=SM2024000111&remark1=&utr=404592403750&amount=1.00&txnmessage=Transaction+success&payerVpa=6296421747-2%40ybl&paymentApp=Phonepe
                if($post->status=="SUCCESS"){
                $user = User::where('id',$findreport->user_id)->first();
                $provider = Provider::where('recharge1', 'upi')->first();
                $post['provider_id'] = $provider->id;
                //$post['parent_id'] = $user->parent_id;
               // $parentUser = User::where('id',$post->parent_id)->first();
                $mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $mastermerchentGstCharge = $this->gstCharge($mastermerchentCh);
                $debitMaasterCharge = User::where('id',$user->id)->decrement('exupiwallet',$mastermerchentCh+$mastermerchentGstCharge);
               // $reselerUser = User::whereId($parentUser->parent_id)->first();
                //$resellerCommision = \Myhelper::getCommission($post->amount, $reselerUser->scheme_id, $post->provider_id, $reselerUser->role->slug);
                $userCharge = $this->getUpiCharge($post->amount,$user->updatemerchantcharge);
               
                $update = [
                    "status"=>"success",
                    "refno"=>$post->utr,
                    "amount"=>$post->amount,
                    "orderAmount"=>$post->order_id,
                    "payid"=>$post->order_id, //$data->data[0]->amount,
                    "balance" => $user->exupiwallet,
                    "charge"=>$mastermerchentCh,
                    "gst"=>$mastermerchentGstCharge,
                    "payer_vpa"=>$post->payerVpa,
                    "payerAccName"=>$post->remitterName??'',
                    "authcode"=>$post->remitterAccountNumber??'',
                    "number"=>$post->remitterIFSC??''
                ];
                
                $update = \DB::table('idfcreports')->where('txnid',$post->order_id)->where('status','initiated')->update($update);
                if($user->role->slug == "apiuser"){
                        $output['status'] = "success";
                        $output['clientid']  = $findreport->mytxnid;
                        $output['txnid']     = $findreport->mytxnid;
                        $output['vpaadress']   = $post->payerVpa;
                        $output['npciTxnId']   = $post->utr;
                        $output['amount']   = $post->amount;
                        $output['bankTxnId']   = $post->order_id;
                        $output['payerVpa']  = $json_data->customer_vpa??'';
                        $output['payerAccName']= $json_data->customerName??'';
                        $output['orderAmount']= $findreport->orderAmount??'';
                        /*if($post->ip() =="45.248.27.202"){
                          dd(http_build_query($output));
                        }*/
                        \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                        
                    }
                }    
            break;    
            case 'supi';
                \DB::table('paytmlogs')->insert(['response' => json_encode($post->all())]);
                $report = \DB::table('upirorders')->where('txnid',$post->clientid)->first();
                if($report->status =='initiated' && $post->status=="success"){
                      
                        //dd($report);
                        $user  = User::where('id', $report->user_id)->first();
                        $provider = Provider::where('recharge1', 'upi')->first();
                        $post['provider_id'] = $provider->id;
                        //$post['parent_id'] = $user->parent_id;
                       // $parentUser = User::where('id',$user->id)->first();
                        //$mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
                        //User::where('id', $user->id)->increment('upiwallet', $post->amount );
                        
                        $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        $usergstAmt =$usercommission*18/100;
                      
                        $update = [
                            "status"=>"success",
                            "refno"=>$post->npciTxnId,
                            "amount"=>$post->amount,//number_format($post->amount, 0, '.', ''),
                            "orderAmount"=>$report->amount,
                            "payid"=>$post->bankTxnId,
                            'option1'=>$post->txnid,
                            "charge"  => $usercommission,
                            "gst"  => '0',
                            "payer_vpa"=>$post->payerVpa,
                            "payerAccName"=>$post->payerAccName,
                            "number"=>$post->vpaadress
                        ];
                        //dd($update);
                        $update = \DB::table('upirorders')->whereTxnid($post->clientid)->where('status','initiated')->update($update);
                        $reportOrderAmt = \DB::table('upirorders')->whereTxnid($post->clientid)->first();
                        $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
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
                        User::where('id', $report->user_id)->increment('upiwallet', $post->amount-$totalCharge);
                       // dd($user->role->slug);
                    if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        //dd("3232323");
                            $output['status'] = "success";
                            $output['clientid']  = $report->mytxnid;
                            $output['txnid']     = $post->txnid;
                            $output['vpaadress']   = $post->vpaadress;
                            $output['npciTxnId']   = $post->npciTxnId;
                            $output['payId']   = $post->clientid;
                            $output['amount']   = $post->amount;
                            $output['bankTxnId']   = $post->bankTxnId;
                            $output['payerVpa']  = $post->payerVpa;
                            $output['payerAccName']= $post->payerAccName;
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                            
                        }
                     
                }
                break;
            
            case 'sprint';
                \DB::table('paytmlogs')->insert(['response' => 'sprint'.json_encode($post->all())]);
                $AES_ENCRYPTION_KEY = '39451d4102bf7d4ef7b3dea195a56d27';
                $AES_ENCRYPTION_IV = 'f1d56c25b38d57f7'; 
                
                
                $base64Data = base64_decode($post->encdata);
                
                $cipher = openssl_decrypt($base64Data, 'AES-256-CBC', $AES_ENCRYPTION_KEY, $options = OPENSSL_RAW_DATA, $AES_ENCRYPTION_IV);
                $jsonData = json_decode($cipher);
                //dd($jsonData);
                $report = \DB::table('upirorders')->where('txnid',$jsonData->param->txnReferance)->first();
                
                if($report->status =='initiated' && $jsonData->param->TxnStatus=="0" && $jsonData->param->responsecode=="0"){
                      
                        //dd($report);
                        $user  = User::where('id', $report->user_id)->first();
                        $provider = Provider::where('recharge1', 'upi')->first();
                        $post['provider_id'] = $provider->id;
                        //$post['parent_id'] = $user->parent_id;
                       // $parentUser = User::where('id',$user->id)->first();
                        //$mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
                        //User::where('id', $user->id)->increment('upiwallet', $post->amount );
                        
                        $usercommission = \Myhelper::getCommission($jsonData->param->TxnAmt, $user->scheme_id, $post->provider_id, $user->role->slug);
                        $usergstAmt =$usercommission*18/100;
                      
                        $update = [
                            "status"=>"success",
                            "refno"=>$jsonData->param->RRN,
                            "amount"=>$jsonData->param->TxnAmt,//number_format($post->amount, 0, '.', ''),
                            "orderAmount"=>$report->amount,
                            "payid"=>$jsonData->param->TXN_ID,
                            'option1'=>$jsonData->param->CBS_Ref_Num,
                            "charge"  => $usercommission,
                            "gst"  => '0',
                            "payer_vpa"=>$jsonData->param->PayerVPA,
                            "payerAccName"=>$jsonData->param->PayerName,
                            "number"=>$jsonData->param->PayerMobileNumber
                        ];
                        //dd($update);
                        $update = \DB::table('upirorders')->whereTxnid($jsonData->param->txnReferance)->where('status','initiated')->update($update);
                        $reportOrderAmt = \DB::table('upirorders')->whereTxnid($jsonData->param->txnReferance)->first();
                        $usercommission = \Myhelper::getCommission($jsonData->param->TxnAmt, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
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
                            'option2'=> $reportOrderAmt->payer_vpa,
                            'option3'=> $reportOrderAmt->payerAccName,
                            'option4'=> $reportOrderAmt->number,
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
                        User::where('id', $report->user_id)->increment('upiwallet', $jsonData->param->TxnAmt-$totalCharge);
                       // dd($user->role->slug);
                    if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        //dd("3232323");
                            $output['status'] = "success";
                            $output['clientid']  = $report->mytxnid;
                            $output['txnid']     = $jsonData->param->txnReferance;
                            $output['vpaadress']   = $jsonData->param->PayerVPA;
                            $output['npciTxnId']   = $jsonData->param->RRN;
                            $output['payId']   = $jsonData->param->TXN_ID;
                            $output['amount']   = $jsonData->param->TxnAmt;
                            $output['bankTxnId']   = $jsonData->param->CBS_Ref_Num;
                            $output['payerVpa']  = $jsonData->param->PayerVPA;
                            $output['payerAccName']= $jsonData->param->PayerName;
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                            
                        }
                     
                }
                return response()->json(['responseMessage'=>'Successful','returnCode'=>"0"]);
                break;    
                
            
            case 'sfupi';
                \DB::table('paytmlogs')->insert(['response' => json_encode($post->all())]);
                $report = \DB::table('upirorders')->where('txnid',$post->order_id)->first();
                if(!$report){
                     return response()->json([
                        'status'    => 'TXF', 
                        'statuscode' => 'TXF',
                        'message'   => 'Transaction Not Found',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
                if($report->status =='initiated' && $post->status=="SUCCESS"){
                      
                        //dd($report);
                        $user  = User::where('id', $report->user_id)->first();
                        $provider = Provider::where('recharge1', 'upi')->first();
                        $post['provider_id'] = $provider->id;
                        //$post['parent_id'] = $user->parent_id;
                       // $parentUser = User::where('id',$user->id)->first();
                        //$mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
                        //User::where('id', $user->id)->increment('upiwallet', $post->amount );
                        
                        $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        $usergstAmt =$usercommission*18/100;
                      
                        $update = [
                            "status"=>"success",
                            "refno"=>$post->utr_no,
                            "amount"=>$post->amount,//number_format($post->amount, 0, '.', ''),
                            "orderAmount"=>$report->amount,
                            "payid"=>$post->utr_no,
                            'option1'=>$post->utr_no,
                            "charge"  => $usercommission,
                            "gst"  => '0',
                            "payer_vpa"=>$post->payerVpa??'',
                            "payerAccName"=>$post->payerAccName??'',
                            "number"=>$post->vpaadress??''
                        ];
                        //dd($update);
                        $update = \DB::table('upirorders')->whereTxnid($post->order_id)->where('status','initiated')->update($update);
                        $reportOrderAmt = \DB::table('upirorders')->whereTxnid($post->order_id)->first();
                        $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
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
                        User::where('id', $report->user_id)->increment('upiwallet', $post->amount-$totalCharge);
                       // dd($user->role->slug);
                    if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        //dd("3232323");
                            $output['status'] = "success";
                            $output['clientid']  = $report->mytxnid;
                            $output['txnid']     = $post->order_id;
                            $output['vpaadress']   = $post->vpaadress??'';
                            $output['npciTxnId']   = $post->utr_no;
                            $output['payId']   = $post->order_id;
                            $output['amount']   = $post->amount;
                            $output['bankTxnId']   = $post->utr_no;
                            $output['payerVpa']  = $post->payerVpa??'';
                            $output['payerAccName']= $post->payerAccName??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                            
                        }
                     
                }
                break;    
            
            case 'payscope':
                
                 \DB::table('paytmlogs')->insert(['response' => 'payscope'.json_encode($post->all())]);
                break;
            
            case 'easebuz':
               // dd($post->data);
                //$doc = json_encode($post->all());
                
                //Hash Sequence: key|beneficiary_account_number|ifsc|beneficiary_upi_handle|unique_request_number|amount|unique_transaction_reference|status|salt


                 \DB::table('paytmlogs')->insert(['response' => 'easebuz'.json_encode($post->all())]);
                 /*$inputString = '970EA1BF79'.'|'.$post->data['beneficiary_account_name'].'|'.$post->data['beneficiary_account_ifsc'].'|'.' '.'|'.$post->data['unique_request_number'].'|'.$post->data['amount'].'|'.$post->data['unique_transaction_reference'].'|'.$post->data['status'].'|'.'FEE20D5CC1';
                    //dd($inputString);
                $hash = hash('sha512', $inputString);
                dd($inputString,$hash);*/
                $report = Report::where('status','pending')->where('txnid', $post->data['unique_request_number'])->first();
                //dd($report);
                if($report){
                   //dd($data->event);
                    if(($post->event=="TRANSFER_INITIATED"||$post->event=="TRANSFER_STATUS_UPDATE" )&& $post->data['status'] =='success'){
                        Report::where('txnid', $post->data['unique_request_number'])->update([
                             'status' => 'success',
                             'refno'  => $post->data['unique_transaction_reference']
                        ]);
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXN';
                       $output['statuscode'] = 'TXN';
                       $output['utr'] = $post->data['unique_transaction_reference']??'';
                       $output['message'] = 'Transction Successfull';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "ES-PayoutCallback", $report->apitxnid); 
                    }elseif ($post->event=="TRANSFER_INITIATED" && $post->data['status'] =='failure') {
                       // dd("sdsdsd");
                        
                        
                         \Myhelper::transactionRefund($report->id);
                         $update['status'] = "reversed";
                         $update['payid'] = $post->data['unique_transaction_reference']??'';
                    
                        /* Report::where('txnid', $report->payoutid)->update([
                            'status' => 'reversed',
                            'refno' => $post->result['rrn']??$post->data['reason']
                        ]);*/
                        Report::where(['id'=> $report->id])->update($update);
                    
                    
                        
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXF';
                       $output['statuscode'] = 'TXF';
                       $output['utr'] = $post->data['unique_transaction_reference']??'';
                       $output['message'] = $post->message??'';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $report->apitxnid); 
                    }
                }
                
                break;
            
            case 'bankopen':
                
                \DB::table('paytmlogs')->insert(['response' => 'bankopen'.json_encode($post->all()),'encRes'=>$post->header('x-zwitch-signature')]);
                if($post->name !="transfers.updated"){
                    return response()->json(["statuscode"=>'ERR','message'=> "Event type is not handle,pls pass transfers.updated"]); 
                }
                $headersignature = $post->header('x-zwitch-signature');
                $signing_secret = 'whs_live_4uynm46NX7ZbwYnbmTkDu4NE6qUPP7iAIEwu'; 
                $response_body = $post->all();//raw webhook request body
                
                $request_signature =  hash_hmac('sha256', json_encode($response_body), $signing_secret);
                
               /* if($headersignature != $request_signature) 
                {
                    return response()->json(["statuscode"=>'ERR','message'=> "Signature Invalid"]);
                }*/
                $report = Report::where('status','pending')->where('txnid', $post->data['object']['merchant_reference_id'])->first();
                //dd($report);
                if(!$report){
                     return response()->json(["statuscode"=>'ERR','message'=> "Transction Not Found"]);
                }
                if($report){
                  // dd($post->data['object']['status']);
                    if($post->name =="transfers.updated" && $post->is_sandbox == false && $post->data['object']['status'] =='success'){
                        //dd('sdsdsd');
                        Report::where('txnid', $post->data['object']['merchant_reference_id'])->update([
                             'status' => 'success',
                             'refno'  => $post->data['object']['bank_reference_number']
                        ]);
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXN';
                       $output['statuscode'] = 'TXN';
                       $output['utr'] = $post->data['object']['bank_reference_number']??'';
                       $output['message'] = 'Transction Successfull';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "B-PayoutCallback", $report->apitxnid); 
                    }elseif ($post->name=="transfers.updated" && $post->is_sandbox =='false' && $post->data['object']['status'] =='failed') {
                       \Myhelper::transactionRefund($report->id);
                         $update['status'] = "reversed";
                         $update['payid'] =$post->data['object']['bank_reference_number']??'';
                    
                        /* Report::where('txnid', $report->payoutid)->update([
                            'status' => 'reversed',
                            'refno' => $post->result['rrn']??$post->data['reason']
                        ]);*/
                        Report::where(['id'=> $report->id])->update($update);
                    
                    
                        
                       $user  = User::where('id',$report->user_id)->first();
                       $output = array();
                       $output['status'] = 'TXF';
                       $output['statuscode'] = 'TXF';
                       $output['utr'] = $post->data['object']['bank_reference_number']??'';
                       $output['message'] = $post->message??'Transction failed';
                       $output['amount'] = $report->amount;
                       $output['clientTxnid'] = $report->apitxnid;
                       $output['apitxnid'] = $report->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "B-PayoutCallback", $report->apitxnid); 
                    }
                }
                
                break;
            
            default:
                return response('');
                break;
        }
    }
    
    
    public function upicallback(Request $post){
       \DB::table('paytmlogs')->insert(['response' =>json_encode($post->all()),'encRes'=>$post->message,'callbackurl'=>$_SERVER['HTTP_HOST']]);
        
        $resLog = \DB::table('paytmlogs')->where('encRes','like','%'.$post->message.'%')->first();
        $company = Company::whereWebsite($resLog->callbackurl)->first();
        $api = Api::where('company_id',$company->id)->first();
        $cipher = "AES-256-CBC";
        $encrypted_data =$resLog->encRes;
        $encryption_key =$api->username;
        //Decrypt data
        $decrypted_data = openssl_decrypt($encrypted_data, $cipher, $encryption_key);
        //dd($decrypted_data);
        //echo $decrypted_data;
        /*if($post->ip()=="160.202.37.73"){
          dd($decrypted_data);  
        }*/
         $json_start_pos = strpos($decrypted_data, '{');
        if ($json_start_pos !== false) {
            $json_string = substr($decrypted_data, $json_start_pos);
            $json_data = json_decode($json_string);
            if(!isset($json_data->status)){
                 return response()->json(['status'=>false]);
            }
            //dd($json_data);
            // $json_data now contains the decoded JSON object
            if($json_data->status=="SUCCESS"){
            $report = Report::where('txnid',$json_data->extTransactionId)->where('status','initiated')->first();
                
                if($report){
                    //dd($report);
                    $user  = User::where('id', $report->user_id)->first();
                    $provider = Provider::where('recharge1', 'upi')->first();
                    $post['provider_id'] = $provider->id;
                    $post['parent_id'] = $user->parent_id;
                    $parentUser = User::where('id',$post->parent_id)->first();
                    $mastermerchentCh = \Myhelper::getCommission($json_data->amount, $user->scheme_id, $post->provider_id, $parentUser->role->slug);
                    $mastermerchentGstCharge = $this->gstCharge($mastermerchentCh);
                    //$mastermerchentCh = \Myhelper::getCommission($report->amount, $user->scheme_id, $post->provider_id, $parentUser->role->slug);
                    $debitMaasterCharge = User::where('id',$parentUser->id)->decrement('mainwallet',$mastermerchentCh+$mastermerchentGstCharge);
                    $reselerUser = User::whereId($parentUser->parent_id)->first();
                    $resellerCommision = \Myhelper::getCommission($json_data->amount, $reselerUser->scheme_id, $post->provider_id, $reselerUser->role->slug);
                    User::where('id', $reselerUser->id)->increment('mainwallet', $resellerCommision );
                    $userCharge = $this->getUpiCharge($json_data->amount,$user->updatemerchantcharge);
                   // \DB::table('paytmlogs')->insert(['response' => $userCharge, 'txnid' =>'cosmoscharge']);
                    $update = [
                        "status"=>"success",
                        "refno"=>$json_data->rrn,
                        "amount"=>$json_data->amount,
                        "orderAmount"=>$report->amount,
                        "payid"=>$json_data->txnId,
                        "charge"=>$mastermerchentCh,
                        "gst"=>$mastermerchentGstCharge,
                        "payer_vpa"=>$json_data->customer_vpa,
                        "payerAccName"=>$json_data->customerName,
                        "authcode"=>$json_data->responseTime,
                        "number"=>$json_data->merchant_vpa
                    ];
                    
                    $update = \DB::table('reports')->whereTxnid($json_data->extTransactionId)->where('status','initiated')->update($update);
                    $reportOrderAmt = \DB::table('reports')->whereTxnid($json_data->extTransactionId)->first();
                    User::where('id', $report->user_id)->increment('mainwallet', $json_data->amount-$userCharge );
                    
                if($user->role->slug == "apiuser" ||$user->role->slug == "whitelable"){
                        $output['status'] = "success";
                        $output['clientid']  = $report->mytxnid;
                        $output['txnid']     = $json_data->extTransactionId;
                        $output['vpaadress']   = $json_data->merchant_vpa;
                        $output['npciTxnId']   = $json_data->rrn;
                        $output['amount']   = $json_data->amount;
                        $output['bankTxnId']   = $json_data->txnId;
                        $output['payerVpa']  = $json_data->customer_vpa;
                        $output['payerAccName']= $json_data->customerName;
                        $output['orderAmount']= $reportOrderAmt->orderAmount;
                        /*if($post->ip() =="45.248.27.202"){
                          dd(http_build_query($output));
                        }*/
                        \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                        
                    }
                }
            }
        }
        
    }
    
    public function bvyaper(Request $post){
       \DB::table('paytmlogs')->insert(['response' => 'bvyaper'.json_encode($post->all())]);
        $findreport = \DB::table('idfcreports')->where('txnid',$post->order_id)->where('status','initiated')->first();
                if(!$findreport){
                    return response()->json(['status'=>'ERR','message'=>'Order Id not found']);
                }
                
                //status=SUCCESS&order_id=SM2024000111&remark1=&utr=404592403750&amount=1.00&txnmessage=Transaction+success&payerVpa=6296421747-2%40ybl&paymentApp=Phonepe
                if($post->status=="SUCCESS"){
                $user = User::where('id',$findreport->user_id)->first();
                $provider = Provider::where('recharge1', 'upi')->first();
                $post['provider_id'] = $provider->id;
                $post['parent_id'] = $user->parent_id;
                $parentUser = User::where('id',$post->parent_id)->first();
                $mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $parentUser->role->slug);
                $mastermerchentGstCharge = $this->gstCharge($mastermerchentCh);
                $debitMaasterCharge = User::where('id',$user->id)->decrement('exupiwallet',$mastermerchentCh+$mastermerchentGstCharge);
                $reselerUser = User::whereId($parentUser->parent_id)->first();
                //$resellerCommision = \Myhelper::getCommission($post->amount, $reselerUser->scheme_id, $post->provider_id, $reselerUser->role->slug);
                $userCharge = $this->getUpiCharge($post->amount,$user->updatemerchantcharge);
               
                $update = [
                    "status"=>"success",
                    "refno"=>$post->utr,
                    "amount"=>$post->amount,
                    "orderAmount"=>$post->order_id,
                    "payid"=>$post->order_id, //$data->data[0]->amount,
                    "balance" => $user->exupiwallet,
                    "charge"=>$mastermerchentCh,
                    "gst"=>$mastermerchentGstCharge,
                    "payer_vpa"=>$post->payerVpa,
                    "payerAccName"=>$post->remitterName??'',
                    "authcode"=>$post->remitterAccountNumber??'',
                    "number"=>$post->remitterIFSC??''
                ];
                
                $update = \DB::table('idfcreports')->where('txnid',$post->order_id)->update($update);
                if($user->role->slug == "apiuser" || $user->role->slug == "whitelable"){
                        $output['status'] = "success";
                        $output['clientid']  = $findreport->mytxnid;
                        $output['txnid']     = $findreport->mytxnid;
                        $output['vpaadress']   = $post->payerVpa;
                        $output['npciTxnId']   = $post->utr;
                        $output['amount']   = $post->amount;
                        $output['bankTxnId']   = $post->order_id;
                        $output['payerVpa']  = $json_data->customer_vpa??'';
                        $output['payerAccName']= $json_data->customerName??'';
                        $output['orderAmount']= $findreport->orderAmount??'';
                        /*if($post->ip() =="45.248.27.202"){
                          dd(http_build_query($output));
                        }*/
                        \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $post->order_id);
                        
                    }
                }elseif($post->status=="FAILURE"){
                    $user = User::where('id',$findreport->user_id)->first();
                    $post['amount']=$findreport->amount;
                    $provider = Provider::where('recharge1', 'upi')->first();
                    $post['provider_id'] = $provider->id;
                    $post['parent_id'] = $user->parent_id;
                    $parentUser = User::where('id',$post->parent_id)->first();
                    $mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $parentUser->role->slug);
                    $mastermerchentGstCharge = $this->gstCharge($mastermerchentCh);
                    $debitMaasterCharge = User::where('id',$user->id)->decrement('exupiwallet',$mastermerchentCh+$mastermerchentGstCharge);
                    $reselerUser = User::whereId($parentUser->parent_id)->first();
                    //$resellerCommision = \Myhelper::getCommission($post->amount, $reselerUser->scheme_id, $post->provider_id, $reselerUser->role->slug);
                    $userCharge = $this->getUpiCharge($post->amount,$user->updatemerchantcharge);
                   
                    $update = [
                        "status"=>"failed",
                        "refno"=>$post->utr??'failed',
                        "amount"=>$post->amount,
                        "orderAmount"=>$post->order_id,
                        "payid"=>$post->order_id, //$data->data[0]->amount,
                        "balance" => $user->exupiwallet,
                        "charge"=>$mastermerchentCh,
                        "gst"=>$mastermerchentGstCharge,
                        "payer_vpa"=>$post->payerVpa??'',
                        "payerAccName"=>$post->remitterName??'',
                        "authcode"=>$post->remitterAccountNumber??'',
                        "number"=>$post->remitterIFSC??''
                    ];
                    
                    $update = \DB::table('idfcreports')->where('txnid',$post->order_id)->update($update);
                    if($user->role->slug == "apiuser" || $user->role->slug == "whitelable"){
                            $output['status'] = "failed";
                            $output['clientid']  = $findreport->mytxnid;
                            $output['txnid']     = $findreport->mytxnid;
                            $output['vpaadress']   = $post->payerVpa??'';
                            $output['npciTxnId']   = $post->utr??'';
                            $output['amount']   = $post->amount;
                            $output['bankTxnId']   = $post->order_id;
                            $output['payerVpa']  = $json_data->customer_vpa??'';
                            $output['payerAccName']= $json_data->customerName??'';
                            $output['orderAmount']= $findreport->orderAmount??'';
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $post->order_id);
                            
                        }
                }         
    }
    
    
    public function getUpiCharge($amount,$usersetCommision)
    {
       
            $charge = $amount*$usersetCommision/100;
            return $charge;
        
    }
    
    public function gstCharge($amount)
    {
       
            $charge = $amount*18/100;
            return $charge;
        
    }
    private function generateAesKey($salt)
    {
        $salt = hex2bin($salt);
        $passphrase = 'CipherPay API Payout';
        $iterationCount = 10000;
        $keySize = 128;
        $hashAlgorithm = 'sha1';
        $key = openssl_pbkdf2($passphrase, $salt, $keySize / 8, $iterationCount, $hashAlgorithm);
        $this->aesKey = $key;
        $this->aesIv = bin2hex($salt);
        return [$key, bin2hex($salt)];
    }
    public function safepayCallback(Request $post){
        \Log::info('Received callback request:', $post->all());
        \DB::table('paytmlogs')->insert(['response' => 'sfUpinew'.($post)]);
        $doc = json_encode($post->all());
       // dd($post->order_id);
        $report = \DB::table('upirorders')->where('txnid',$post->order_id)->first();
                if(!$report){
                     return response()->json([
                        'status'    => 'TXF', 
                        'statuscode' => 'TXF',
                        'message'   => 'Transaction Not Found',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
                if($report->status =='initiated' && $post->status=="SUCCESS"){
                      
                        //dd($report);
                        $user  = User::where('id', $report->user_id)->first();
                        $provider = Provider::where('recharge1', 'upi')->first();
                        $post['provider_id'] = $provider->id;
                        //$post['parent_id'] = $user->parent_id;
                       // $parentUser = User::where('id',$user->id)->first();
                        //$mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
                        //User::where('id', $user->id)->increment('upiwallet', $post->amount );
                        
                        $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        $usergstAmt =$usercommission*18/100;
                      
                        $update = [
                            "status"=>"success",
                            "refno"=>$post->utr_no,
                            "amount"=>$post->amount,//number_format($post->amount, 0, '.', ''),
                            "orderAmount"=>$report->amount,
                            "payid"=>$post->utr_no,
                            'option1'=>$post->utr_no,
                            "charge"  => $usercommission,
                            "gst"  => '0',
                            "payer_vpa"=>$post->payerVpa??'',
                            "payerAccName"=>$post->payerAccName??'',
                            "number"=>$post->vpaadress??''
                        ];
                        //dd($update);
                        $update = \DB::table('upirorders')->whereTxnid($post->order_id)->where('status','initiated')->update($update);
                        $reportOrderAmt = \DB::table('upirorders')->whereTxnid($post->order_id)->first();
                        $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
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
                        User::where('id', $report->user_id)->increment('upiwallet', $post->amount-$totalCharge);
                       // dd($user->role->slug);
                    if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        //dd("3232323");
                            $output['status'] = "success";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $report->mytxnid;
                            $output['txnid']     = $post->order_id;
                            $output['vpaadress']   = $post->vpaadress??'';
                            $output['npciTxnId']   = $post->utr_no;
                            $output['payId']   = $post->order_id;
                            $output['amount']   = $post->amount;
                            $output['bankTxnId']   = $post->utr_no;
                            $output['payerVpa']  = $post->payerVpa??'';
                            $output['payerAccName']= $post->payerAccName??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                            
                        }
                     
                }
    }
    
    public function sfpayCallback(Request $post){
        \Log::info('Received callback request:', $post->all());
        \DB::table('paytmlogs')->insert(['response' => 'sfUpinew'.($post)]);
        $doc = json_encode($post->all());
       // dd($post->order_id);
        $report = \DB::table('upirorders')->where('txnid',$post->order_id)->first();
                if(!$report){
                     return response()->json([
                        'status'    => 'TXF', 
                        'statuscode' => 'TXF',
                        'message'   => 'Transaction Not Found',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
                if($report->status =='initiated' && $post->status=="SUCCESS"){
                      
                        //dd($report);
                        $user  = User::where('id', $report->user_id)->first();
                        $provider = Provider::where('recharge1', 'upi')->first();
                        $post['provider_id'] = $provider->id;
                        //$post['parent_id'] = $user->parent_id;
                       // $parentUser = User::where('id',$user->id)->first();
                        //$mastermerchentCh = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
                        //User::where('id', $user->id)->increment('upiwallet', $post->amount );
                        
                        $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        $usergstAmt =$usercommission*18/100;
                      
                        $update = [
                            "status"=>"success",
                            "refno"=>$post->utr_no,
                            "amount"=>$post->amount,//number_format($post->amount, 0, '.', ''),
                            "orderAmount"=>$report->amount,
                            "payid"=>$post->utr_no,
                            'option1'=>$post->utr_no,
                            "charge"  => $usercommission,
                            "gst"  => '0',
                            "payer_vpa"=>$post->payerVpa??'',
                            "payerAccName"=>$post->payerAccName??'',
                            "number"=>$post->vpaadress??''
                        ];
                        //dd($update);
                        $update = \DB::table('upirorders')->whereTxnid($post->order_id)->where('status','initiated')->update($update);
                        $reportOrderAmt = \DB::table('upirorders')->whereTxnid($post->order_id)->first();
                        $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        
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
                        User::where('id', $report->user_id)->increment('upiwallet', $post->amount-$totalCharge);
                       // dd($user->role->slug);
                    if($user->role->slug == "whitelable" || $user->role->slug == "apiuser"){
                        //dd("3232323");
                            $output['status'] = "success";
                            $output['product'] = "smartupi";
                            $output['clientid']  = $report->mytxnid;
                            $output['txnid']     = $post->order_id;
                            $output['vpaadress']   = $post->vpaadress??'';
                            $output['npciTxnId']   = $post->utr_no;
                            $output['payId']   = $post->order_id;
                            $output['amount']   = $post->amount;
                            $output['bankTxnId']   = $post->utr_no;
                            $output['payerVpa']  = $post->payerVpa??'';
                            $output['payerAccName']= $post->payerAccName??'';
                            $output['orderAmount']= $reportOrderAmt->orderAmount;
                            /*if($post->ip() =="45.248.27.202"){
                              dd(http_build_query($output));
                            }*/
                            \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "UpiCallback", $report->mytxnid);
                            
                        }
                     
                }
    }
    
    public function sprintCallbackdecode(Request $post)
    {
        $logs = \DB::table('paytmlogs')
          ->where('response', 'like', '%sprint%')
          ->where('created_at', 'like', '2024-06-11 01:19%')
          ->get();
        $AES_ENCRYPTION_KEY = '39451d4102bf7d4ef7b3dea195a56d27';
        $AES_ENCRYPTION_IV = 'f1d56c25b38d57f7'; 
        
        
        
        foreach($logs as $log)
        {
            
            $data = preg_match('/\{.*\}/', $log->response, $matches);

         
            
            // Encode the array back to JSON
            $newJsonString = json_decode($matches[0]);
            //dd($newJsonString);
            $base64Data = base64_decode($newJsonString->encdata);
        
            $cipher = openssl_decrypt($base64Data, 'AES-256-CBC', $AES_ENCRYPTION_KEY, $options = OPENSSL_RAW_DATA, $AES_ENCRYPTION_IV);
            $jsonData = json_decode($cipher);
           // $jsonData = json_decode($cipher);
            // Store decrypted data
            $allDecryptedData[] = $jsonData;
            
        }
       return response()->json($allDecryptedData);
        
    }
    
    public function checkLoad(Request $post)
    {
        $apiUrl = 'https://blinkpe.co.in/api/upi/generateQr';
        $endTime = Carbon::now()->addMinutes(1);
        while (Carbon::now()->lt($endTime)) {
            for ($i = 1; $i <= 1500; $i++) {
                   $req = [
                    "token"=>"GnEsK2juccp0vug1qXjjwIuY8A8mCa",
                    "clientOrderId"=>"SMLOAD".rand(1111,9999),
                    "returnUrl"=>"https://example.com",
                    "amount"=>rand(10,100),
                
                ];
            
                $jsonReq = json_encode($req);
                $curl = curl_init();
        
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://blinkpe.co.in/api/upi/generateQr',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>$jsonReq,
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                  ),
                ));
                
                $response = curl_exec($curl);
                //dd($response);
                if($response){
                    $doc = json_decode($response);
                    //dd($doc);
                } 
                
            }
            
        }
        
    }
}