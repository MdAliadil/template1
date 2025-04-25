<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessCallback;
use Illuminate\Support\Facades\Queue;
use App\Model\Api;
use App\Model\Apitoken;
use App\Model\Provider;
use App\Model\Mahabank;
use App\Model\Report;
use App\Model\Commission;
use App\Model\Aepsreport;
use App\Model\Aepsfundrequest;
use App\Model\Contact;
use App\Model\Easebuzcontact;
use App\Model\Bankopencontact;
use App\User;
use App\Helpers\Cp_payout;
use Carbon\Carbon;

class PayoutController extends Controller
{
    protected $api;
    public function __construct()
    {
        $this->api = Api::where('code', 'cepayout')->first();
    }

    public function transaction(Request $post)
    {
       return response()->json(['statuscode'=>'ERR', 'message'=> "Issue at bank side"]);
            $rules = array(
                'token' => 'required',
                'transactionType' => 'required'
            );
    
            $validator = \Validator::make($post->all(), array_reverse($rules));
            if ($validator->fails()) {
                
                foreach ($validator->errors()->messages() as $key => $value) {
                    $error = $value[0];
                }
                
                return response()->json(array(
                    'statuscode'  => 'ERR',
                    'message' => $error,
                    "extmsg"=>"fwd"
                ));
            }
        $token = Apitoken::where('token', $post->token)->where('ip', $post->ip())->first(['user_id']);
        if(!$token){
             return response()->json(array(
                    'statuscode'  => 'ERR',
                    'message' => "IP or Token is Invalid, your IP is ".$post->ip()
                )); 
        }
        $post['user_id'] = $token->user_id;
        
        // if($post->user_id!="2"){
        //      return response()->json(['statuscode'=>"ERR","message"=>"Service Down"]);
        // }
        
        $user = User::where('id', $post->user_id)->first();
        if($user->lockedamount>=$user->mainwallet){
         return response()->json(['statuscode'=>"ERR","message"=>"Your Wallet Balance is low"]);   
        }
            /*if($user->id =='20'){
                return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => "Your Payout balance on hold"
                    )); 
            }*/
        
        
       /*if($user->id == "10"){
            $post['transactionType'] = 'bankopen';
        }else{
            $post['transactionType'] = $this->bankpayoutapi();
        }*/
        
        
       /* if($post->transactionType !="easebuz" && strtolower($post->mode) =="upi"){
            return response()->json(['statuscode'=>"ERR","message"=>"Currently UPI payment mode disabled"]);
        }*/
        
        $post['transactionType'] = $this->bankpayoutapi();
        
        switch ($post->transactionType) {
            
            
            case 'payout':
                
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required|unique:reports',
                    'account' => 'required',
                    'bank' => 'required',
                    'ifsc' => 'required',
                    'amount' => 'required|numeric|min:1000'
                );
        
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error
                    ));
                }
                
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['charge'] = $usercommission;
                //dd($usercommission);
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode"=>'ERR','message'=>  "Low payout balance to make this request."]);
                }
               $api = Api::where('code', 'cepayout')->first();
                if(!$api){
                    return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                }


                do {
                    $uniquePortion = time() . mt_rand(111111111111, 999999999999);
                    $post['payoutid'] = $this->transcode().$uniquePortion;
                } while (Report::where("txnid", "=", $post->payoutid)->first() instanceof Report);
  
                $provider = Provider::where('recharge1', 'payout1')->first();
                
                $url = 'https://proapi.safepayindia.com/PayoutService.svc/PayoutTransfer';//$api->url.'single_payout';
                
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->account;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $user->mobile;
                $aepsreports['apitxnid']        = $post->apitxnid;
                $aepsreports['refno']        = "success";
                $aepsreports['aadhar']       = $post->account;
                $aepsreports['amount']       = $post->amount;
                $aepsreports['charge']       = $post->charge;
                $aepsreports['option3']      = $post->bank;
                $aepsreports['option4']      = $post->ifsc;
                $aepsreports['mode']         = 'IMPS';
                $aepsreports['txnid']        = $post->payoutid;
                $aepsreports['user_id']      = $user->id;
                $aepsreports['credited_by']  = '1';
                $aepsreports['balance']      = $user->mainwallet;
                $aepsreports['trans_type']         = "debit";
                $aepsreports['transtype']    = 'fund';
                $aepsreports['status']       = 'pending';
                $aepsreports['product']      = 'payout';
                $aepsreports['remark']       = "Bank Settlement";
                //dd($aepsreports);
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                if(isset($post->mode)){
                    $post['pmode'] = $post->mode; 
                }else{
                    $post['pmode'] = 'IMPS'; 
                }
                $payout = new Cp_payout();
                $reqData = array(
                          "mode"=> $post->pmode,
                          "remarks"=> "Vendor Payout",
                          "amount"=> $post->amount,
                          "type"=> "vendor",
                          "bene_name"=> $post->bene_name??$user->name,
                          "bene_mobile"=> $post->bene_mobile??$user->mobile,
                          "bene_email"=> $post->bene_email??$user->email,
                          "bene_acc"=> $post->account,
                          "bene_ifsc"=> $post->ifsc,
                          "bene_acc_type"=> "Saving",
                          "refid"=> $post->payoutid,
                          "bene_bank_name"=> $post->bank
                        
                    );
                //dd($reqData);
                $payout = new Cp_payout();
               
                
                $response = $payout->singlepayout($reqData);
                //dd($response);
                $log = \DB::table('payoutlogs')->insert(['request'=>json_encode($reqData),"response"=>json_encode($response)??"","txnId"=>$post->payoutid,"user_id"=>$user->id,"service"=>"payout"]);
                

                if(isset($response['statuscode']) && $response['statuscode']=='401'){
                  return response()->json(['statuscode'=> "TUP",'message'=> $response['msg']]);  
                }
               
                if(isset($response['statuscode']) && $response == ''){
                    return response()->json(['statuscode'=> "TUP",'message'=> "Transaction under process"]);
                }
                
              
                return response()->json(['statuscode'=> "TXN","message"=>"Transaction intiate successfully"]);
                break;
                
            case 'spayout':
                 //dd($post->all());
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required|unique:reports',
                    'accountNumber' => 'required',
                    'bank' => 'required',
                    'ifsc' => 'required',
                    'amount' => 'required|numeric|min:10',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'email' => 'required',
                    'mobile' => 'required'
                );
        
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error,
                        "extmsg"=>"fwd"
                    ));
                }
                
                if($post->amount > 1 && $post->amount <= 500){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount > 500 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout2k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                
                $post['account'] = $post->accountNumber;
                
                $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['charge'] = $usercommission;
                //dd($usercommission);
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode"=>'ERR','message'=>  "Low payout balance to make this request."]);
                }
               $api = Api::where('code', 'spayout')->first();
                if(!$api){
                    return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                }


                do {
                    $uniquePortion = 'BL'. mt_rand(11111111, 99999999);
                    $post['payoutid'] = $this->transcode().$uniquePortion;
                } while (Report::where("txnid", "=", $post->payoutid)->first() instanceof Report);
  
                $provider = Provider::where('recharge1', 'payout1')->first();
                
                $url = 'https://proapi.safepayindia.com/PayoutService.svc/PayoutTransfer';
                
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->account;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $user->mobile;
                $aepsreports['apitxnid']        = $post->apitxnid;
                $aepsreports['refno']        = "pending";
                $aepsreports['aadhar']       = $post->account;
                $aepsreports['amount']       = $post->amount;
                $aepsreports['charge']       = $post->charge;
                $aepsreports['option3']      = $post->bank;
                $aepsreports['option4']      = $post->ifsc;
                $aepsreports['mode']         = 'IMPS';
                $aepsreports['txnid']        = $post->payoutid;
                $aepsreports['user_id']      = $user->id;
                $aepsreports['credited_by']  = '1';
                $aepsreports['balance']      = $user->mainwallet;
                $aepsreports['trans_type']         = "debit";
                $aepsreports['transtype']    = 'fund';
                $aepsreports['status']       = 'pending';
                $aepsreports['product']      = 'payout';
                $aepsreports['remark']       = "Bank Settlement";
                //dd($aepsreports);
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                if(isset($post->mode)){
                    $post['pmode'] = $post->mode; 
                }else{
                    $post['pmode'] = 'IMPS'; 
                }
                
                $parameters["first_name"]   = $post->firstName;
                $parameters["last_name"]   = $post->lastName;
                $parameters["mobile_no"] = $post->mobile;
                $parameters["email_id"]       = $post->email;
                $parameters["bene_name"]      = $post->firstName.' '.$post->lastName;
                $parameters["account_no"]  = $post->accountNumber;
                $parameters["ifsc"]       = $post->ifsc;
                $parameters["bank_name"]       = $post->bank;
                $parameters["order_id"]      = $post->payoutid;
                $parameters["amount"]      = $post->amount;
                $parameters["payment_remark"]      = $post->payment_remark??"Transfer";
               
    
               $header = array(
                            'Content-Type: application/json',
                            'Client-ID:'.$user->pclientId,
                            'Client-Secret:'.$user->pclientSecret,
                         ); 
    
                $result = \Myhelper::curl($url, 'POST', json_encode($parameters),$header, "yes", 'App\Model\Report', $post->payoutid);
                //dd($result);        
                if($result['error'] || $result['response'] == ''){
                    return response()->json([
                        'status'    => 'TUP', 
                        'message'   => 'Transaction Under Process',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
    
                $data = json_decode($result['response']);
                    if(!isset($data->res_code)){
                        return response()->json([
                            'status'    => 'TUP', 
                            'message'   => 'Transaction Under Process',
                            //'rrn'       => $myaepsreport->id
                        ]);
                    }
                    
                switch ($data->res_code) {
                    case '1':
                        // Report::where('id', $myaepsreport->id)->update([
                        //     'status' => 'success',
                        //     'refno'  => (isset($data->utr_no)) ? $data->utr_no : "Success"
                        // ]);

                        $response = [
                            'statuscode' => 'TXN',
                            'status'=>"TXN",
                            'message'=> (isset($data->res_message))? $data->res_message : 'Transaction Successfull',
                            'rrn'    => (isset($data->utr_no))? $data->utr_no : $myaepsreport->id,
                        ];
                       
                       
                    //   $output = array();
                    //   $output['status'] = 'TXN';
                    //   $output['statuscode'] = 'TXN';
                    //   $output['utr'] = $data->utr_no??'';
                    //   $output['message'] = $data->res_message??'';
                    //   $output['amount'] = $myaepsreport->amount;
                    //   $output['clientTxnid'] = $myaepsreport->apitxnid;
                    //   $output['apitxnid'] = $myaepsreport->txnid;
                    //   $output['product'] = 'spayout';
                    //   //dd($post->user_id);
                    //     $jsonResponse = response()->json($response);
                    //     //$user = User::where('id', $post->user_id)->first();
                    //   //dd($user);
                    //     dispatch(function() use ($output, $user, $myaepsreport) {
                           
                    
                    //         try {
                    //             \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "PayoutCallback", $myaepsreport->apitxnid); 
                    //         } catch (\Exception $e) {
                    //             // Handle the exception, possibly log the error
                    //             \Log::error('Webhook failed: ' . $e->getMessage());
                    //         }
                    //     });
                    
                        return $response;
                     break;

                // case '0':
                // case '-1':
                //     Report::where('id', $myaepsreport->id)->update([
                //             'status' => 'failed',
                //             'refno'  => (isset($data->utr_no)) ? $data->utr_no : "failed"
                //         ]);
                //     User::where('id', $aepsreports['user_id'])->increment('mainwallet',$aepsreports['amount']+$aepsreports['charge']);    
                //     return response()->json([
                //         'status' => 'TXF',
                //         'statuscode' => 'TXF',
                //         'message'=> (isset($data->res_message))? $data->res_message : 'Transaction Failed',
                //         'rrn'    => (isset($data->utr_no))? $data->utr_no : $myaepsreport->id,
                //     ]);
                // break;
                } 
            break;
            
            case 'cspayout':
                 //dd($post->all());
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required|unique:reports',
                    'accountNumber' => 'required',
                    'bank' => 'required',
                    'ifsc' => 'required',
                    'amount' => 'required|numeric|min:10',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'email' => 'required',
                    'mobile' => 'required'
                );
        
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error,
                        "extmsg"=>"fwd"
                    ));
                }
                
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                
                $post['account'] = $post->accountNumber;
                
                $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['charge'] = $usercommission;
                //dd($usercommission);
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode"=>'ERR','message'=>  "Low payout balance to make this request."]);
                }
               $api = Api::where('code', 'cspayout')->first();
                if(!$api){
                    return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                }


                do {
                    $uniquePortion = 'BL'. mt_rand(11111111, 99999999);
                    $post['payoutid'] = $this->transcode().$uniquePortion;
                } while (Report::where("txnid", "=", $post->payoutid)->first() instanceof Report);
  
                $provider = Provider::where('recharge1', 'payout1')->first();
                
                $url = 'https://connect.safepayindia.com/v3/api/smartpay/transaction';
                
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->account;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $user->mobile;
                $aepsreports['apitxnid']        = $post->apitxnid;
                $aepsreports['refno']        = "pending";
                $aepsreports['aadhar']       = $post->account;
                $aepsreports['amount']       = $post->amount;
                $aepsreports['charge']       = $post->charge;
                $aepsreports['option3']      = $post->bank;
                $aepsreports['option4']      = $post->ifsc;
                $aepsreports['mode']         = 'IMPS';
                $aepsreports['txnid']        = $post->payoutid;
                $aepsreports['user_id']      = $user->id;
                $aepsreports['credited_by']  = '1';
                $aepsreports['balance']      = $user->mainwallet;
                $aepsreports['trans_type']         = "debit";
                $aepsreports['transtype']    = 'fund';
                $aepsreports['status']       = 'pending';
                $aepsreports['product']      = 'payout';
                $aepsreports['remark']       = "Bank Settlement";
                //dd($aepsreports);
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                if(isset($post->mode)){
                    $post['pmode'] = $post->mode; 
                }else{
                    $post['pmode'] = 'IMPS'; 
                }
                
                $parameters["token"]   = "pO0ykSqPqa508jceLCBamZfJUMkMaG";
                $parameters["transactionType"]   = 'spayout';
                $parameters["apitxnid"]      = $post->payoutid;
                $parameters["amount"]      = $post->amount;
                $parameters["firstName"]   = $post->firstName;
                $parameters["lastName"]   = $post->lastName;
                $parameters["email"]       = $post->email;
                $parameters["mobile"] = $post->mobile;
                $parameters["mode"] = 'imps';
                $parameters["accountNumber"]  = $post->accountNumber;
                $parameters["ifsc"]       = $post->ifsc;
                $parameters["bank"]       = $post->bank;
                
               
                
                

    
               $header = array(
                            'Content-Type: application/json',
                         ); 
    
                $result = \Myhelper::curl($url, 'POST', json_encode($parameters),$header, "yes", 'App\Model\Report', $post->payoutid);
                //dd($result);        
                if($result['error'] || $result['response'] == ''){
                    return response()->json([
                        'status'    => 'TUP', 
                        'message'   => 'Transaction Under Process',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
    
                $data = json_decode($result['response']);
                    if(!isset($data->statuscode)){
                        return response()->json([
                            'status'    => 'TUP', 
                            'message'   => 'Transaction Under Process',
                            //'rrn'       => $myaepsreport->id
                        ]);
                    }
                    
                switch ($data->statuscode) {
                    case 'TXN':
                       
                        $response = [
                            'statuscode' => 'TXN',
                            'status'=>"TXN",
                            'message'=> (isset($data->message))? $data->message : 'Transaction Acepted',
                            'rrn'    => (isset($data->rrn))? $data->rrn : $myaepsreport->id,
                        ];
                       
                       return response()->json($response);
                     break;

                case 'TXF':
                case 'ERR':
                    Report::where('id', $myaepsreport->id)->update([
                            'status' => 'failed',
                            'refno'  => (isset($data->rrn)) ? $data->rrn : "failed"
                        ]);
                    User::where('id', $aepsreports['user_id'])->increment('mainwallet',$aepsreports['amount']+$aepsreports['charge']);    
                    return response()->json([
                        'status' => 'TXF',
                        'statuscode' => 'TXF',
                        'message'=> (isset($data->message))? $data->message : 'Transaction Failed',
                        'rrn'    => (isset($data->rrn))? $data->rrn : $myaepsreport->id,
                    ]);
                break;
                case 'TUP':
                   return response()->json([
                            'status'    => 'TUP', 
                            'message'   => 'Transaction Under Process',
                            'rrn'       => $myaepsreport->id
                        ]);
                break;
                } 
            break;
            
            case 'payonpayout':
                 //dd($post->all());
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required|unique:reports',
                    'accountNumber' => 'required',
                    'bank' => 'required',
                    'ifsc' => 'required',
                    'amount' => 'required|numeric|min:10',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'email' => 'required',
                    'mobile' => 'required'
                );
        
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error,
                        "extmsg"=>"fwd"
                    ));
                }
                
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                
                $post['account'] = $post->accountNumber;
                
                $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['charge'] = $usercommission;
                //dd($usercommission);
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode"=>'ERR','message'=>  "Low payout balance to make this request."]);
                }
               $api = Api::where('code', 'cspayout')->first();
                if(!$api){
                    return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                }


                do {
                    $uniquePortion = 'BL'. mt_rand(11111111, 99999999);
                    $post['payoutid'] = $this->transcode().$uniquePortion;
                } while (Report::where("txnid", "=", $post->payoutid)->first() instanceof Report);
  
                $provider = Provider::where('recharge1', 'payout1')->first();
                
                $url = 'https://api.paycoonstar.com/90373567/api/v1/quick_transfers/PaycoonPayout2?payout_refno='.$post->payoutid.'&amount='.$post->amount.'&payout_mode=IMPS&user_mobile_number='.$post->mobile.'&account_name='.$post->firstName.'&account_no='.$post->accountNumber.'&ifsc='.$post->ifsc;
                
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->account;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $user->mobile;
                $aepsreports['apitxnid']        = $post->apitxnid;
                $aepsreports['refno']        = "pending";
                $aepsreports['aadhar']       = $post->account;
                $aepsreports['amount']       = $post->amount;
                $aepsreports['charge']       = $post->charge;
                $aepsreports['option3']      = $post->bank;
                $aepsreports['option4']      = $post->ifsc;
                $aepsreports['mode']         = 'IMPS';
                $aepsreports['txnid']        = $post->payoutid;
                $aepsreports['user_id']      = $user->id;
                $aepsreports['credited_by']  = '1';
                $aepsreports['balance']      = $user->mainwallet;
                $aepsreports['trans_type']         = "debit";
                $aepsreports['transtype']    = 'fund';
                $aepsreports['status']       = 'pending';
                $aepsreports['product']      = 'payout';
                $aepsreports['remark']       = "Bank Settlement";
                //dd($aepsreports);
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                if(isset($post->mode)){
                    $post['pmode'] = $post->mode; 
                }else{
                    $post['pmode'] = 'IMPS'; 
                }
                
                $parameters["merchantId"]   = "1895225";
                $parameters["clientid"]   = 'L628NG67-W8KY-DPWO-NANO-8V9WLF8D8TE1';
                $parameters["clientSecretKey"]      = 'TJ1BZF5E6SEXO6ZI9GIOFKEU6E2HHM8D';
               
               $header = array(
                            'Content-Type: application/json',
                         ); 
    
                $result = \Myhelper::curl($url, 'POST', json_encode($parameters),$header, "yes", 'App\Model\Report', $post->payoutid);
                //dd([$url, 'POST', json_encode($parameters),$header,$result]);        
                if($result['error'] || $result['response'] == ''){
                    return response()->json([
                        'status'    => 'TUP', 
                        'message'   => 'Transaction Under Process',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
    
                $data = json_decode($result['response']);
                    if(!isset($data->response_code)){
                        return response()->json([
                            'status'    => 'TUP', 
                            'message'   => 'Transaction Under Process',
                            //'rrn'       => $myaepsreport->id
                        ]);
                    }
                    
                switch ($data->response_code) {
                    case '1':
                      /*Report::where('id', $myaepsreport->id)->update([
                            'status' => 'success',
                            'payid'  => (isset($data->rrn)) ? $data->rrn : "Success",
                            'refno'  => (isset($data->rrn)) ? $data->rrn : "Success"
                        ]);*/
                        $response = [
                            'statuscode' => 'TXN',
                            'status'=>"TXN",
                            'message'=> (isset($data->message))? $data->message : 'Transaction Acepted',
                            'rrn'    => (isset($data->rrn))? $data->rrn : $myaepsreport->id,
                        ];
                       
                       return response()->json($response);
                     break;

                
               default:
                   return response()->json([
                            'status'    => 'TUP', 
                            'message'   => 'Transaction Under Process',
                            'rrn'       => $myaepsreport->id
                        ]);
                break;
                } 
            break;
            
            case 'mmoney':
                 //dd($post->all());
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required|unique:reports',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                    'accountNumber' => 'required',
                    'ifsc' => 'required'
                );
                 if (isset($post->mode) && strtoupper($post->mode) === 'UPI') {
                    unset($rules['accountNumber'], $rules['ifsc']);
                    $rules['vpa'] = 'required';
                }
                
        
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error,
                        "extmsg"=>"fwd"
                    ));
                }
                
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                
                $post['account'] = $post->accountNumber;
                
                $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['charge'] = $usercommission;
                //dd($usercommission);
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode"=>'ERR','message'=>  "Low payout balance to make this request."]);
                }
               $api = Api::where('code', 'mmoney')->first();
                if(!$api){
                    return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                }


                do {
                    $uniquePortion = 'BL'. mt_rand(11111111, 99999999);
                    $post['payoutid'] = $this->transcode().$uniquePortion;
                } while (Report::where("txnid", "=", $post->payoutid)->first() instanceof Report);
  
                $provider = Provider::where('recharge1', 'payout1')->first();
                
                $url = $api->url.'merchant/bank/payout';
                
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->account??$post->vpa;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $user->mobile;
                $aepsreports['apitxnid']        = $post->apitxnid;
                $aepsreports['refno']        = "pending";
                $aepsreports['aadhar']       = $post->account;
                $aepsreports['amount']       = $post->amount;
                $aepsreports['charge']       = $post->charge;
                $aepsreports['option3']      = $post->bank;
                $aepsreports['option4']      = $post->ifsc;
                $aepsreports['mode']         = 'IMPS';
                $aepsreports['txnid']        = $post->payoutid;
                $aepsreports['user_id']      = $user->id;
                $aepsreports['credited_by']  = '1';
                $aepsreports['balance']      = $user->mainwallet;
                $aepsreports['trans_type']         = "debit";
                $aepsreports['transtype']    = 'fund';
                $aepsreports['status']       = 'pending';
                $aepsreports['product']      = 'payout';
                $aepsreports['remark']       = "Bank Settlement";
                //dd($aepsreports);
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                
                $post['paymode'] = strtoupper($post->mode) =="UPI"?"UPI":'IMPS';
                
                $parameters["token"]         = $api->username;
                $parameters["paymode"]   = strtoupper($post->mode) =="UPI"?"UPI":'IMPS';
                $parameters["ip"] = '118.139.164.244';
                $parameters["amount"]       = $post->amount;
                $parameters["name"]  = $post->bene_name??$user->name;
                $parameters["apitxnid"]       = $post->payoutid;
                $parameters["callback"]       = 'https://blinkpe.co.in/api/callback/update/mmoney';
                
                if($post->paymode !="UPI"){
                    $ifscfind = "https://ifsc.razorpay.com/".$post->ifsc;
                    $result = \Myhelper::curl($ifscfind, "GET", "", [], "yes", "ISFC", $post->txnid);
                    $dataifsc = json_decode($result['response']);
                    
                    $parameters["account"]      = $post->account;
                    $parameters["ifsc"]       = $post->ifsc;
                    $parameters["bank"]       = $dataifsc->BANK??'Axis Bank';
                }else{
                    $parameters["upiid"]       = $post->vpa;
                }
               
    
                $result = \Myhelper::curl($url, 'POST', json_encode($parameters), array("Accept: application/json","Cache-Control: no-cache","Content-Type: application/json"), "yes", 'App\Model\Report', $post->payoutid);
                //dd($result);        
                if($result['error'] || $result['response'] == ''){
                    return response()->json([
                        'status'    => 'TUP', 
                        'message'   => 'Transaction Under Process',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
    
                $data = json_decode($result['response']);
                    if(!isset($data->status)){
                        return response()->json([
                            'status'    => 'TUP', 
                            'message'   => 'Transaction Under Process',
                            //'rrn'       => $myaepsreport->id
                        ]);
                    }
                    
                switch ($data->status) {
                    case 'TXN':
                        Report::where('id', $myaepsreport->id)->update([
                           // 'status' => 'success',
                            'payid'  => (isset($data->rrn)) ? $data->rrn : "Success"
                        ]);

                        return response()->json([
                            'statuscode' => 'TXN',
                            'status'=>"TXN",
                            'message'=> (isset($data->message))? $data->message : 'Transaction Successfull',
                            'rrn'    => (isset($data->rrn))? $data->rrn : $myaepsreport->id,
                        ]);
                     break;

                case 'TXF':
                    Report::where('id', $myaepsreport->id)->update([
                            'status' => 'failed',
                            'payid'  => (isset($data->rrn)) ? $data->rrn : "failed"
                        ]);
                    User::where('id', $aepsreports['user_id'])->increment('mainwallet',$aepsreports['amount']+$aepsreports['charge']);    
                    return response()->json([
                        'status' => 'TXF',
                        'statuscode' => 'TXF',
                        'message'=> (isset($data->message))? $data->message : 'Transaction Failed',
                        'rrn'    => (isset($data->rrn))? $data->rrn : $myaepsreport->id,
                    ]);
                break;
                } 
            break;
            
            case 'easebuz':
              //dd($post->all());
              /*if($user->id !="10"){
              return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);    
              }*/
              
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required|unique:reports',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                    //'mode'=>'required',
                    'amount' => 'required|numeric|min:10',
                    'accountNumber' => 'required_if:mode,imps',
                    'ifsc' => 'required_if:mode,imps',
                    //"upi_id"=>"required_if:mode,upi"
                );
                
                $post['mode'] = strtolower($post->mode)??'IMPS';
                
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error
                    ));
                }
                 
                $api = Api::where('code','espayout')->first();
                if(!$api){
                    return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                }
                if($post->mode == "upi"){
                   $post['accountNumber'] = $post->upi_id; 
                }
                $checkContactId = Easebuzcontact::where('accountNumber',$post->accountNumber)->first();
                if(!$checkContactId){
                       // $apiUrl = $api->url."contacts/";
                        $post['fullname'] = $post->firstName.' '.$post->lastName;
                        $req = [
                            "key"=>$api->username,
                            "name"=>$post->fullname,
                            "email"=>$post->email,
                            "phone"=>$post->mobile
                    ];
                    $inputString = $api->username.'|'.$post->fullname.'|'.$post->email.'|'.$post->mobile.'|'.$api->password;
                    //dd($inputString);
                    $hash = hash('sha512', $inputString);
                    
                    $header = array(
                            'Content-Type: application/json',
                            'Authorization:'.$hash,
                            'WIRE-API-KEY:'.$api->username,
                          ); 
                     
                    $result = \Myhelper::curl($api->url."contacts/", "POST",json_encode($req), $header, "yes", 'ECONTACT', $post->apitxnid);
                        
                    if($result['response'] == ''){
                       return response()->json([ 'statuscode' => "ERR", 'message' => "Something Went Wrong,Contact to Admin"]);
                    }
                    
                    
                    $response = $result['response'];
                       
                    $doc = json_decode($response);
                    if($doc->success !="true"){
                        
                        return response()->json(['statuscode'=>"ERR","message"=>$doc->message??"Something went wrong"]);
                    }else{
                        
                        $insert = [
                           
                                "firstName"=>$post->firstName,
                                "lastName"=>$post->lastName,
                                "email"=>$post->email,
                                "mobile"=>$post->mobile,
                                "accountNumber"=>$post->accountNumber,
                                "ifsc"=>$post->ifsc,
                                "type"=>'customer',
                                "accountType"=>'bank_account',
                                "referenceId"=>$post->apitxnid,
                                "user_id"=>$post->user_id,
                                "contactId"=>$doc->data->contact->id
                            ];
                            $insertData = Easebuzcontact::create($insert);
                            if(!$insertData){
                              return response()->json([ 'statuscode' => "ERR", 'message' => "Something Went Wrong,Contact to Admin"]);
                            }
                            
                        //970EA1BF79|cont365ed5eb41398899278ea1363903|Sourav Mullick|922010067734917|UTIB0004668||FEE20D5CC1
                        $post['beneficiary_type'] = $post->mode =="upi"?'upi':'bank_account';
                        $reqbene = [
                            "key"=>$api->username,
                            "contact_id"=>$doc->data->contact->id,
                            "beneficiary_type"=>$post->beneficiary_type,//'bank_account',
                            "beneficiary_name"=>$post->fullname,
                            "account_number"=>$post->accountNumber,
                            "ifsc"=>$post->ifsc,
                            //"upi_handle"=>$post->upi_id
                        ];
                        $inputStringbene = $api->username.'|'.$doc->data->contact->id.'|'.$post->fullname.'|'.$post->accountNumber.'|'.$post->ifsc.'|'.$post->upi_id.'|'.$api->password;
                        $hashbene = hash('sha512', $inputStringbene);
                        
                        $headerbene = array(
                                'Content-Type: application/json',
                                'Authorization:'.$hashbene,
                                'WIRE-API-KEY:'.$api->username,
                              ); 
                         
                        $resultbene = \Myhelper::curl($api->url."beneficiaries/", "POST",json_encode($reqbene), $headerbene, "yes", 'ECONTACT', $post->apitxnid);
                          // dd([$inputStringbene,$resultbene,$reqbene]);
                        if($resultbene['response'] == ''){
                           return response()->json([ 'statuscode' => "ERR", 'message' => "Something Went Wrong,Contact to Admin"]);
                        }
                        
                        
                        $responsebene = $resultbene['response'];
                           
                        $docbene = json_decode($responsebene); 
                        
                        $updatebene = Easebuzcontact::where('contactId', $doc->data->contact->id)->update(['beneId' => $docbene->data->beneficiary->id]);
                            
                            
                          //return response()->json([ 'statuscode' => "TXN", 'message' => "Contact Created Successfully"]);
                    }
                }
                
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['charge'] = $usercommission;
                //dd($usercommission);
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode"=>'ERR','message'=>  "Low payout balance to make this request."]);
                }
              // $api = Api::where('code', 'spayout')->first();
                


                do {
                    $uniquePortion = 'BL'. mt_rand(11111111, 99999999);
                    $post['payoutid'] = $this->transcode().$uniquePortion;
                } while (Report::where("txnid", "=", $post->payoutid)->first() instanceof Report);
  
                $provider = Provider::where('recharge1', 'payout1')->first();
                
                $url = $api->url.'transfers/initiate/';
                
                $checkContactIdData = Easebuzcontact::where('accountNumber',$post->accountNumber)->first();
                
                if(!$checkContactIdData->beneId){
                  return response()->json([
                        'status'    => 'ERR', 
                        'statuscode' => 'ERR',
                        'message'   => 'Your bank account is blocked at our end,contact to admin',
                    ]);  
                }
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->accountNumber;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $user->mobile;
                $aepsreports['apitxnid']        = $post->apitxnid;
                $aepsreports['refno']        = "pending";
                $aepsreports['aadhar']       = $post->accountNumber;
                $aepsreports['amount']       = $post->amount;
                $aepsreports['charge']       = $post->charge;
                $aepsreports['option3']      = $post->bank;
                $aepsreports['option4']      = $post->ifsc;
                $aepsreports['mode']         = 'IMPS';
                $aepsreports['txnid']        = $post->payoutid;
                $aepsreports['user_id']      = $user->id;
                $aepsreports['credited_by']  = '1';
                $aepsreports['balance']      = $user->mainwallet;
                $aepsreports['trans_type']         = "debit";
                $aepsreports['transtype']    = 'fund';
                $aepsreports['status']       = 'pending';
                $aepsreports['product']      = 'payout';
                $aepsreports['remark']       = "Bank Settlement";
                //dd($aepsreports);
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                
                $reqtransfer = [
                            "key"=>$api->username,
                            "virtual_account_number"=>'008563400003530',
                            "beneficiary_code"=>$checkContactIdData->beneId,
                            "unique_request_number"=>$post->payoutid,
                            "payment_mode"=>'IMPS', //strtoupper($post->mode),//'IMPS',
                            "amount"=>floatval($post->amount),
                            "narration"=>'transfer'
                    ];
                    $inputStringtransfer = $api->username.'|'.$checkContactIdData->beneId.'|'.$post->payoutid.'|'.floatval($post->amount).'|'.$api->password;
                    //970EA1BF79|bene5a50004149fbac14674526430101|SMTEST2024001|10|FEE20D5CC1
                    $hashtransfer = hash('sha512', $inputStringtransfer);
                    //dd([$inputStringtransfer,$hashtransfer]);
                    $header = array(
                            'Content-Type: application/json',
                            'Authorization:'.$hashtransfer,
                            'WIRE-API-KEY:'.$api->username,
                          ); 
                     
                    $result1 = \Myhelper::curl($url, "POST",json_encode($reqtransfer), $header, "yes", 'ETRANSFER', $post->apitxnid);
                   
                   // dd($result1);
                
                if($result1['error'] || $result1['response'] == ''){
                    return response()->json([
                        'status'    => 'TUP', 
                        'statuscode' => 'TUP',
                        'message'   => 'Transaction Under Process',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
    
                $data = json_decode($result1['response']);
                //dd($data);
                if(!isset($data->success)){
                        return response()->json([
                            'status'    => 'TUP', 
                            'message'   => 'Transaction Under Process',
                            //'rrn'       => $myaepsreport->id
                        ]);
                    }
                    
                if($data->success=='true'){
                     Report::where('id', $myaepsreport->id)->update([
                            'status' => 'pending',
                            'refno'  => (isset($data->data->transfer_request->unique_transaction_reference)) ? $data->data->transfer_request->unique_transaction_reference : "Success"
                        ]);
                        
                      //$user  = User::where('id',$myaepsreport->user_id)->first();
                      /* $output = array();
                       $output['status'] = 'TXN';
                       $output['statuscode'] = 'TXN';
                       $output['utr'] = $myaepsreport->refno;//(isset($data->data->transfer_request->unique_transaction_reference))? $data->data->transfer_request->unique_transaction_reference : $myaepsreport->id;
                       $output['message'] = 'Transaction Successfull';
                       $output['amount'] = $myaepsreport->amount;
                       $output['clientTxnid'] = $myaepsreport->apitxnid;
                       $output['apitxnid'] = $myaepsreport->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "eayoutCallback", $myaepsreport->apitxnid); */
                     
                     //ProcessCallback::dispatch($myaepsreport)->onQueue('callbacks');
                     
                        return response()->json([
                            'statuscode' => 'TXN',
                            'status'=>"TXN",
                            'message'=>'Transaction Successfull',
                            'rrn'    => (isset($data->data->transfer_request->unique_transaction_reference))? $data->data->transfer_request->unique_transaction_reference : $myaepsreport->id,
                        ]);
                        
                    //dispatch_after_response(new ProcessCallback($myaepsreport))->onQueue('callbacks');    
                        
                }else{
                   Report::where('id', $myaepsreport->id)->update([
                            'status' => 'failed',
                            'refno'  => (isset($data->data->transfer_request->unique_transaction_reference)) ? $data->data->transfer_request->unique_transaction_reference : "failed"
                        ]);
                    User::where('id', $aepsreports['user_id'])->increment('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                    
                      /* $output = array();
                       $output['status'] = 'TXF';
                       $output['statuscode'] = 'TXF';
                       $output['utr'] = (isset($data->data->transfer_request->unique_transaction_reference))? $data->data->transfer_request->unique_transaction_reference : $myaepsreport->id;
                       $output['message'] = 'Transaction Failed';
                       $output['amount'] = $myaepsreport->amount;
                       $output['clientTxnid'] = $myaepsreport->apitxnid;
                       $output['apitxnid'] = $myaepsreport->txnid;
                       $output['product'] = 'spayout';
                     \Myhelper::curl($user->callbackurl."?".http_build_query($output), "GET", "", [], "yes", "eayoutCallback", $myaepsreport->apitxnid); */
                     
                    return response()->json([
                        'status' => 'TXF',
                        'statuscode' => 'TXF',
                        'message'=> 'Transaction Failed',
                        'rrn'    => (isset($data->message))? $data->message : $myaepsreport->id,
                    ]); 
                }    
            break;
            
            
            case 'bankopen':
              //dd($post->all());
              $post['mode'] = isset($post->mode)?strtolower($post->mode):'imps';
              //dd($post->mode);
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required|unique:reports',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                    //'mode'=>'required',
                    'amount' => 'required|numeric|min:10',
                    'accountNumber' => 'required_if:mode,imps',
                    'ifsc' => 'required_if:mode,imps',
                    "upi_id"=>"required_if:mode,upi"
                );
                
                //$post['mode'] = strtolower($post->mode)??'IMPS';
                
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error
                    ));
                }
                // return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                $api = Api::where('code','bankopen')->first();
                if(!$api){
                    return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                }
                if($post->mode == "upi"){
                   $post['accountNumber'] = $post->upi_id; 
                }
                $checkContactId = Bankopencontact::where('accountNumber',$post->accountNumber)->first();
               // dd($checkContactId);
                if(!$checkContactId){
                       // $apiUrl = $api->url."contacts/";
                        $post['fullname'] = $post->firstName.' '.$post->lastName;
                        $req = [
                            "type" => $post->mode == 'upi' ? 'vpa' : 'account_number',
                            "name_of_account_holder" => $post->fullname,
                            "email" => $post->email,
                            "phone" => $post->mobile,
                        ];
                        
                        if ($post->mode != 'upi') {
                            $req["bank_account_number"] = $post->accountNumber;
                            $req["bank_ifsc_code"] = $post->ifsc;
                        } else {
                            $req["vpa"] = $post->upi_id;
                        }
                    
                    $header = array(
                            'Content-Type: application/json',
                            'Authorization:Bearer '.$api->username.':'.$api->password
                          ); 
                     
                    $result = \Myhelper::curl($api->url.'accounts/'.$api->optional1.'/beneficiaries', "POST",json_encode($req), $header, "yes", 'B-CONTACT', $post->apitxnid);
                    //dd([$api->url.'accounts/'.$api->optional1.'/beneficiaries', "POST",json_encode($req), $header, "yes", 'B-CONTACT', $post->apitxnid,$result]);   
                    if($result['response'] == ''){
                       return response()->json([ 'statuscode' => "ERR", 'message' => "Something Went Wrong,Contact to Admin"]);
                    }
                    
                    
                    $response = $result['response'];
                       
                    $doc = json_decode($response);
                    //dd($doc);
                    if(isset($doc->error)){
                        
                        return response()->json(['statuscode'=>"ERR","message"=>$doc->error->message??"Something went wrong"]);
                    }else{
                        //dd($doc);
                        $insert = [
                           
                                "firstName"=>$post->firstName,
                                "lastName"=>$post->lastName,
                                "email"=>$post->email,
                                "mobile"=>$post->mobile,
                                "accountNumber"=>$post->mode !='upi'?$post->accountNumber:$post->upi_id,
                                "ifsc"=>$post->ifsc,
                                "type"=>'customer',
                                "accountType"=>'account_number',
                                "account_id"=>$doc->account_id,
                                "user_id"=>$post->user_id,
                                "beneId"=>$doc->id,
                                'status'=>$doc->status
                            ];
                            $insertData = Bankopencontact::create($insert);
                            if(!$insertData){
                              return response()->json([ 'statuscode' => "ERR", 'message' => "Something Went Wrong,Contact to Admin"]);
                            }
                        
                    }
                }
                //return response()->json([ 'statuscode' => "ERR", 'message' => "Created"]);
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['charge'] = $usercommission;
                //dd($usercommission);
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode"=>'ERR','message'=>  "Low payout balance to make this request."]);
                }
              // $api = Api::where('code', 'spayout')->first();
                


                do {
                    $uniquePortion = 'BL'. mt_rand(11111111, 99999999);
                    $post['payoutid'] = $this->transcode().$uniquePortion;
                } while (Report::where("txnid", "=", $post->payoutid)->first() instanceof Report);
  
                $provider = Provider::where('recharge1', 'payout1')->first();
                
                $url = $api->url.'transfers';
                
                $checkContactIdData = Bankopencontact::where('accountNumber',$post->accountNumber)->first();
                
                if(!$checkContactIdData->beneId){
                  return response()->json([
                        'status'    => 'ERR', 
                        'statuscode' => 'ERR',
                        'message'   => 'Your bank account is blocked at our end,contact to admin',
                    ]);  
                }
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->mode!='upi'?$post->accountNumber:$post->upi_id;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $user->mobile;
                $aepsreports['apitxnid']        = $post->apitxnid;
                $aepsreports['refno']        = "pending";
                $aepsreports['aadhar']       = $post->accountNumber;
                $aepsreports['amount']       = $post->amount;
                $aepsreports['charge']       = $post->charge;
                $aepsreports['option3']      = $post->bank;
                $aepsreports['option4']      = $post->ifsc;
                $aepsreports['mode']         = strtoupper($post->mode);
                $aepsreports['txnid']        = $post->payoutid;
                $aepsreports['user_id']      = $user->id;
                $aepsreports['credited_by']  = '1';
                $aepsreports['balance']      = $user->mainwallet;
                $aepsreports['trans_type']         = "debit";
                $aepsreports['transtype']    = 'fund';
                $aepsreports['status']       = 'pending';
                $aepsreports['product']      = 'payout';
                $aepsreports['remark']       = "Bank Settlement";
                //dd($aepsreports);
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                
                $reqtransfer = [
                            "debit_account_id"=>$api->optional1,
                            "beneficiary_id"=>$checkContactIdData->beneId,
                            "amount"=>floatval($post->amount),
                            "currency_code"=>'inr', 
                            "payment_mode"=>'imps', 
                            "merchant_reference_id"=>$post->payoutid
                    ];
                 if ($post->mode != 'upi') {
                        $reqtransfer["payment_mode"] ='imps';
                        $reqtransfer["type"] = 'account_number';
                    } else {
                        $reqtransfer["type"] = 'vpa';
                    }
                    $header = array(
                            'Content-Type: application/json',
                            'Authorization:Bearer '.$api->username.':'.$api->password
                          ); 
                     
                    $result1 = \Myhelper::curl($url, "POST",json_encode($reqtransfer), $header, "yes", 'B-TRANSFER', $post->apitxnid);
                   
                    //dd($result1);
                
                if($result1['error'] || $result1['response'] == ''){
                    return response()->json([
                        'status'    => 'TUP', 
                        'statuscode' => 'TUP',
                        'message'   => 'Transaction Under Process',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
    
                $data = json_decode($result1['response']);
               
                if(isset($doc->error)){
                        Report::where('id', $myaepsreport->id)->update([
                            'status' => 'failed',
                            'payid'  => (isset($data->error->message)) ? $data->error->message : "failed"
                                ]);
                        User::where('id', $aepsreports['user_id'])->increment('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                        return response()->json([
                            'status'    => 'TXF', 
                            'message'   => 'Transaction Failed'
                        ]);
                    }
                    
               Report::where('id', $myaepsreport->id)->update([
                            'status' => 'pending',
                            'payid'  => (isset($data->id)) ? $data->id : "Success"
                        ]);
                        
                      
                     
                        return response()->json([
                            'statuscode' => 'TXN',
                            'status'=>"TXN",
                            'message'=>'Transaction Accepted',
                            'rrn'    => '',
                        ]);
                        
                    //dispatch_after_response(new ProcessCallback($myaepsreport))->onQueue('callbacks');    
                        
                  
            break;
            
            case 'expresspayout':
              //dd($post->all());
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required|unique:reports',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                    'amount' => 'required|numeric|min:100',
                    'accountNumber' => 'required',
                    'ifsc' => 'required'
                );
        
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error
                    ));
                }
                $api = Api::where('code', 'spayout')->first();
                if(!$api){
                    return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                }
                $credentials = $api->username . ':' . $api->password;
                $base64Credentials = base64_encode($credentials);
                $headersend =array("Accept: application/json","Cache-Control: no-cache","Content-Type: application/json", "Authorization: Basic ".$base64Credentials);
                $checkContactId = Contact::where('accountNumber',$post->accountNumber)->first();
            
                if(!$checkContactId){
                        $apiUrl = "https://business.payscope.in/v1/service/payout/contacts";
                        
                        $req = [
                            "firstName"=>$post->firstName,
                            "lastName"=>$post->lastName,
                            "email"=>$post->email,
                            "mobile"=>$post->mobile,
                            "type"=>"customer",
                            "accountType"=>'bank_account',
                            "accountNumber"=>$post->accountNumber,
                            "ifsc"=>$post->ifsc,
                            "referenceId"=>$post->apitxnid
                        
                    ];
                   /* $header = array(
                            'Content-Type: application/json',
                            'Authorization:Basic U0FGRUVfODFhYjJhMTkzMGVkNzU1MTExMDA0NzgxMDEzMTU0MTY2OmFlNWEwMjQ5ZGUwYTQ1MWYwZDQyM2Y1ZWVhMmZkNDdjMTEwMDQ3ODEwMTMxNjEyNTU='
                          );*/ 
                     
                    $result = \Myhelper::curl($apiUrl, "POST",json_encode($req), $headersend, "yes", 'SPAYOUT', $post->apitxnid);
                    //dd([$apiUrl, "POST",json_encode($req), $headersend, "yes", 'SPAYOUT',$result]);
                    if($result['response'] == ''){
                       return response()->json([ 'statuscode' => "ERR", 'message' => "Something Went Wrong,Contact to Admin"]);
                    }
                    
                    
                    $response = $result['response'];
                       
                    $doc = json_decode($response);
                    if($doc->code !="0x0200"){
                        
                        return response()->json(['statuscode'=>"ERR","message"=>$doc->message??"Something went wrong"]);
                    }else{
                        
                        $insert = [
                           
                                "firstName"=>$post->firstName,
                                "lastName"=>$post->lastName,
                                "email"=>$post->email,
                                "mobile"=>$post->mobile,
                                "accountNumber"=>$post->accountNumber,
                                "ifsc"=>$post->ifsc,
                                "type"=>'customer',
                                "accountType"=>'bank_account',
                                "referenceId"=>$post->apitxnid,
                                "user_id"=>$post->user_id,
                                "contactId"=>$doc->data->contactId
                            ];
                            $insertData = Contact::create($insert);
                            if(!$insertData){
                              return response()->json([ 'statuscode' => "ERR", 'message' => "Something Went Wrong,Contact to Admin"]);
                            }
                            $data = [
                                "contactId"=>$doc->data->contactId
                                ];
                          //return response()->json([ 'statuscode' => "TXN", 'message' => "Contact Created Successfully","data"=>$data]);
                    }
                }
                
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['charge'] = $usercommission;
                //dd($usercommission);
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode"=>'ERR','message'=>  "Low payout balance to make this request."]);
                }
               


                do {
                    $uniquePortion = 'BL'. mt_rand(11111111, 99999999);
                    $post['payoutid'] = $this->transcode().$uniquePortion;
                } while (Report::where("txnid", "=", $post->payoutid)->first() instanceof Report);
  
                $provider = Provider::where('recharge1', 'payout1')->first();
                
                $url = $api->url.'payout/orders';
                
                $checkContactIdData = Contact::where('accountNumber',$post->accountNumber)->first();
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->account;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $user->mobile;
                $aepsreports['apitxnid']        = $post->apitxnid;
                $aepsreports['refno']        = "pending";
                $aepsreports['aadhar']       = $post->account;
                $aepsreports['amount']       = $post->amount;
                $aepsreports['charge']       = $post->charge;
                $aepsreports['option3']      = $post->bank;
                $aepsreports['option4']      = $post->ifsc;
                $aepsreports['mode']         = 'IMPS';
                $aepsreports['txnid']        = $post->payoutid;
                $aepsreports['user_id']      = $user->id;
                $aepsreports['credited_by']  = '1';
                $aepsreports['balance']      = $user->mainwallet;
                $aepsreports['trans_type']         = "debit";
                $aepsreports['transtype']    = 'fund';
                $aepsreports['status']       = 'pending';
                $aepsreports['product']      = 'payout';
                $aepsreports['remark']       = "Bank Settlement";
                //dd($aepsreports);
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                

                
                $parameters["amount"]       = $post->amount;
                $parameters["purpose"]      = 'others';
                $parameters["mode"]  = 'IMPS';
                $parameters["contactId"]  = $checkContactIdData->contactId; //$post->contactId;
                $parameters["clientRefId"]      = $post->payoutid;
                $parameters["udf1"]      = '';
                $parameters["udf2"]      ='';
    
               
                /*$header = array(
                    'Content-Type: application/json',
                    'Authorization:Basic U0FGRUVfODFhYjJhMTkzMGVkNzU1MTExMDA0NzgxMDEzMTU0MTY2OmFlNWEwMjQ5ZGUwYTQ1MWYwZDQyM2Y1ZWVhMmZkNDdjMTEwMDQ3ODEwMTMxNjEyNTU='
                  ); */
             
                $result1 = \Myhelper::curl($url, "POST",json_encode($parameters), $headersend, "yes", 'SPAYOUTInitiate', $post->payoutid);
                //dd($result);
                //$result = \Myhelper::curl($url, 'POST', json_encode($parameters), array("Accept: application/json","Cache-Control: no-cache","Content-Type: application/json"), "yes", 'App\Model\Report', $post->payoutid);
                
                if($result1['error'] || $result1['response'] == ''){
                    return response()->json([
                        'status'    => 'TUP', 
                        'statuscode' => 'TUP',
                        'message'   => 'Transaction Under Process',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
    
                $data = json_decode($result1['response']);
                //dd($data);
                if($data->code == "0x0203"){
                  return response()->json([
                        'statuscode' => 'ERR',
                        'status' => 'ERR',
                        'message'=> 'Something went wrong,contact to administator',
                        //'rrn'    => (isset($data->data->orderRefId))? $data->data->orderRefId : $myaepsreport->id,
                    ]);  
                }
                if($data){
                    
                      return response()->json([
                        'statuscode' => 'TXN',
                        'status' => 'TXN',
                        'message'=> 'Order accepted successfully',
                        //'rrn'    => (isset($data->data->orderRefId))? $data->data->orderRefId : $myaepsreport->id,
                    ]);
                }
            
            
            
                    
            break;
                
            case 'txnstatus':
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required'
                );
        
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error,
                        "extmsg"=>"fwd"
                    ));
                }
                
                $myaepsreport = Report::where('apitxnid', $post->apitxnid)->first();
               // dd($myaepsreport);
                if(!$myaepsreport){
                    return response()->json([ 'statuscode' => "ERR", 'message' => "Transaction Not Found"]);
                }

                return response()->json([ 'statuscode' => "TXN", 'message' => "Record found", 'status' => $myaepsreport->status, "refno" => $myaepsreport->refno]);
                break;
                
            case 'contactCreate':
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                    'apitxnid' => 'required',
                    'accountNumber' => 'required',
                    'ifsc' => 'required'
                );
        
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error
                    ));
                }
                
                $token = Apitoken::where('token', $post->token)->first(['user_id']);
                if(!$token){
                     return response()->json(array(
                            'statuscode'  => 'ERR',
                            'message' => "IP or Token is Invalid"
                        )); 
                }
                $post['user_id'] = $token->user_id;
                
                
                
                $apiUrl = "https://business.payscope.in/v1/service/payout/contacts";
               
                $req = [
                    "firstName"=>$post->firstName,
                    "lastName"=>$post->lastName,
                    "email"=>$post->email,
                    "mobile"=>$post->mobile,
                    "type"=>"customer",
                    "accountType"=>'bank_account',
                    "accountNumber"=>$post->accountNumber,
                    "ifsc"=>$post->ifsc,
                    "referenceId"=>$post->apitxnid
                
            ];
            $header = array(
                    'Content-Type: application/json',
                    'Authorization:Basic U0FGRUVfODFhYjJhMTkzMGVkNzU1MTExMDA0NzgxMDEzMTU0MTY2OmFlNWEwMjQ5ZGUwYTQ1MWYwZDQyM2Y1ZWVhMmZkNDdjMTEwMDQ3ODEwMTMxNjEyNTU='
                  ); 
             
            $result = \Myhelper::curl($apiUrl, "POST",json_encode($req), $header, "yes", 'SPAYOUT', $post->apitxnid);
                
            if($result['response'] == ''){
               return response()->json([ 'statuscode' => "ERR", 'message' => "Something Went Wrong,Contact to Admin"]);
            }
            
            
            $response = $result['response'];
               
            $doc = json_decode($response);
            if($doc->code !="0x0200"){
                
                return response()->json(['statuscode'=>"ERR","message"=>$doc->message??"Something went wrong"]);
            }else{
                
                $insert = [
                   
                        "firstName"=>$post->firstName,
                        "lastName"=>$post->lastName,
                        "email"=>$post->email,
                        "mobile"=>$post->mobile,
                        "accountNumber"=>$post->accountNumber,
                        "ifsc"=>$post->ifsc,
                        "type"=>'customer',
                        "accountType"=>'bank_account',
                        "referenceId"=>$post->apitxnid,
                        "user_id"=>$post->user_id,
                        "contactId"=>$doc->data->contactId
                    ];
                    $insertData = Contact::create($insert);
                    if(!$insertData){
                      return response()->json([ 'statuscode' => "ERR", 'message' => "Something Went Wrong,Contact to Admin"]);
                    }
                    $data = [
                        "contactId"=>$doc->data->contactId
                        ];
                  return response()->json([ 'statuscode' => "TXN", 'message' => "Contact Created Successfully","data"=>$data]);
            }

            
            break;    
        }        
        
    }
    
    public function txnStatusCheck(Request $post)
    {
       
        $rules = array(
            'token' => 'required',
            'apitxnid' => 'required'
        );
    
        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            
            return response()->json(array(
                'statuscode'  => 'ERR',
                'message' => $error,
                "extmsg"=>"fwd"
            ));
        }
        $token = Apitoken::where('token', $post->token)->where('ip', $post->ip())->first(['user_id']);
        if(!$token){
             return response()->json(array(
                    'statuscode'  => 'ERR',
                    'message' => "IP or Token is Invalid, your IP is ".$post->ip()
                )); 
        }
        $post['user_id'] = $token->user_id;
        
        $myaepsreport = Report::where('apitxnid', $post->apitxnid)->first();
       // dd($myaepsreport);
        if(!$myaepsreport){
            return response()->json([ 'statuscode' => "ERR", 'message' => "Transaction Not Found"]);
        }
    
        return response()->json([ 'statuscode' => "TXN", 'message' => "Record found", 'status' => $myaepsreport->status, "refno" => $myaepsreport->refno]);
                
    }
    
    public function smartpaytransaction(Request $post)
    {
      /*  return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => "Your Payout balance on hold"
                    )); */
       
            $rules = array(
                'token' => 'required',
                'transactionType' => 'required'
            );
    
            $validator = \Validator::make($post->all(), array_reverse($rules));
            if ($validator->fails()) {
                
                foreach ($validator->errors()->messages() as $key => $value) {
                    $error = $value[0];
                }
                
                return response()->json(array(
                    'statuscode'  => 'ERR',
                    'message' => $error,
                    "extmsg"=>"fwd"
                ));
            }
        $token = Apitoken::where('token', $post->token)->where('ip', $post->ip())->first(['user_id']);
        if(!$token){
             return response()->json(array(
                    'statuscode'  => 'ERR',
                    'message' => "IP or Token is Invalid, your IP is ".$post->ip()
                )); 
        }
        $post['user_id'] = $token->user_id;
        

        
        $user = User::where('id', $post->user_id)->first();
            /*if($user->id =='20'){
                return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => "Your Payout balance on hold"
                    )); 
            }*/
        
        
       /* if($user->id == "10"){
            $post['transactionType'] = 'mmoney';
        }else{
            $post['transactionType'] = $this->bankpayoutapi();
        }*/
        
        
       /* if($post->transactionType !="easebuz" && strtolower($post->mode) =="upi"){
            return response()->json(['statuscode'=>"ERR","message"=>"Currently UPI payment mode disabled"]);
        }*/
        
        $post['transactionType'] = 'mmoney';
        
        switch ($post->transactionType) {
            
            case 'mmoney':
                 //dd($post->all());
                $rules = array(
                    'token' => 'required',
                    'apitxnid' => 'required|unique:reports',
                    'firstName' => 'required',
                    'lastName' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                    'accountNumber' => 'required',
                    'ifsc' => 'required',
                    'bank' => 'required'
                );
                 if (isset($post->mode) && strtoupper($post->mode) === 'UPI') {
                    unset($rules['accountNumber'], $rules['ifsc'],$rules['bank']);
                    $rules['vpa'] = 'required';
                }
                
        
                $validator = \Validator::make($post->all(), array_reverse($rules));
                if ($validator->fails()) {
                    
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    
                    return response()->json(array(
                        'statuscode'  => 'ERR',
                        'message' => $error,
                        "extmsg"=>"fwd"
                    ));
                }
                
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                
                $post['account'] = $post->accountNumber;
                
                $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['charge'] = $usercommission;
                //dd($usercommission);
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode"=>'ERR','message'=>  "Low payout balance to make this request."]);
                }
               $api = Api::where('code', 'mmoney')->first();
                if(!$api){
                    return response()->json(["statuscode"=>'ERR','message'=> "Api down for some time"]);
                }


                do {
                    $uniquePortion = 'BL'. mt_rand(11111111, 99999999);
                    $post['payoutid'] = $this->transcode().$uniquePortion;
                } while (Report::where("txnid", "=", $post->payoutid)->first() instanceof Report);
  
                $provider = Provider::where('recharge1', 'payout1')->first();
                
                $url = $api->url.'merchant/bank/payout';
                
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->account??$post->vpa;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $user->mobile;
                $aepsreports['apitxnid']        = $post->apitxnid;
                $aepsreports['refno']        = "pending";
                $aepsreports['aadhar']       = $post->account;
                $aepsreports['amount']       = $post->amount;
                $aepsreports['charge']       = $post->charge;
                $aepsreports['option3']      = $post->bank;
                $aepsreports['option4']      = $post->ifsc;
                $aepsreports['mode']         = 'IMPS';
                $aepsreports['txnid']        = $post->payoutid;
                $aepsreports['user_id']      = $user->id;
                $aepsreports['credited_by']  = '1';
                $aepsreports['balance']      = $user->mainwallet;
                $aepsreports['trans_type']         = "debit";
                $aepsreports['transtype']    = 'fund';
                $aepsreports['status']       = 'pending';
                $aepsreports['product']      = 'payout';
                $aepsreports['remark']       = "Bank Settlement";
                //dd($aepsreports);
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                
                $post['paymode'] = strtoupper($post->mode) =="UPI"?"UPI":'IMPS';
                
                $parameters["token"]         = $api->username;
                $parameters["paymode"]   = strtoupper($post->mode) =="UPI"?"UPI":'IMPS';
                $parameters["ip"] = '118.139.164.244';
                $parameters["amount"]       = $post->amount;
                $parameters["name"]  = $post->bene_name??$user->name;
                $parameters["apitxnid"]       = $post->payoutid;
                $parameters["callback"]       = 'https://blinkpe.co.in/api/callback/update/mmoney';
                
                if($post->paymode !="UPI"){
                    
                    
                    $parameters["account"]      = $post->account;
                    $parameters["ifsc"]       = $post->ifsc;
                    $parameters["bank"]       = $post->bank;
                }else{
                    $parameters["upiid"]       = $post->vpa;
                }
               
    
                $result = \Myhelper::curl($url, 'POST', json_encode($parameters), array("Accept: application/json","Cache-Control: no-cache","Content-Type: application/json"), "yes", 'App\Model\Report', $post->payoutid);
                //dd($result);        
                if($result['error'] || $result['response'] == ''){
                    return response()->json([
                        'status'    => 'TUP', 
                        'message'   => 'Transaction Under Process',
                        //'rrn'       => $myaepsreport->id
                    ]);
                }
    
                $data = json_decode($result['response']);
                    if(!isset($data->status)){
                        return response()->json([
                            'status'    => 'TUP', 
                            'message'   => 'Transaction Under Process',
                            //'rrn'       => $myaepsreport->id
                        ]);
                    }
                    
                switch ($data->status) {
                    case 'TXN':
                        Report::where('id', $myaepsreport->id)->update([
                           // 'status' => 'success',
                            'payid'  => (isset($data->rrn)) ? $data->rrn : "Success"
                        ]);

                        return response()->json([
                            'statuscode' => 'TXN',
                            'status'=>"TXN",
                            'message'=> (isset($data->message))? $data->message : 'Transaction Successfull',
                            'rrn'    => (isset($data->rrn))? $data->rrn : $myaepsreport->id,
                        ]);
                     break;

                case 'TXF':
                    Report::where('id', $myaepsreport->id)->update([
                            'status' => 'failed',
                            'payid'  => (isset($data->rrn)) ? $data->rrn : "failed"
                        ]);
                    User::where('id', $aepsreports['user_id'])->increment('mainwallet',$aepsreports['amount']+$aepsreports['charge']);    
                    return response()->json([
                        'status' => 'TXF',
                        'statuscode' => 'TXF',
                        'message'=> (isset($data->message))? $data->message : 'Transaction Failed',
                        'rrn'    => (isset($data->rrn))? $data->rrn : $myaepsreport->id,
                    ]);
                break;
                } 
            break; 
            
            default:
                return response()->json([
                        'status' => 'TXF',
                        'statuscode' => 'TXF',
                        'message'=> 'Something Went Wrong',
                        'rrn'    => '',
                    ]);
                break;
        }        
        
    }
   
}