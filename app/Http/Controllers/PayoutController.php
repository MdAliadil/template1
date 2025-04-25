<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    //protected $api;
    public function __construct()
    {
       // $this->api = Api::where('code', 'xettlepayout')->first();
        //$this->api = Api::where('code', 'ipayout')->first();
    }
    
    public function index(Request $post){
        if(!\Myhelper::can(['payout_from_dashboard'])){
           abort(403); 
        }
        return view('service.payout');
    }
    
    public function transaction(Request $post){
        $rules = array(
                    'f_name'    => 'required',
                    'l_name'    => 'required',
                    'mobile'    => 'required',
                    'email'    => 'required',
                    'account'    => 'required',
                    'ifsc'    => 'required',
                    'bank'    => 'required'
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
            if (!\Myhelper::hasRole('admin')) {
                return response()->json(['status' => "Permission Not Allowed"], 400);
            }
        
            $user = \Auth::user();
            $post['user_id'] = $user->id;
            
            $post['transactionType'] = 'paycornpayout';
            switch ($post->transactionType) {
             case 'cspayout':
                
                
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                
                //$post['account'] = $post->account;
                
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
                
                User::where('id', $aepsreports['user_id'])->decrement('mainwallet',$aepsreports['amount']+$aepsreports['charge']);
                $myaepsreport = Report::create($aepsreports);
                if(isset($post->mode)){
                    $post['pmode'] = $post->mode; 
                }else{
                    $post['pmode'] = 'IMPS'; 
                }
                
              
                
                $parameters["first_name"]   = $post->f_name;
                $parameters["last_name"]   = $post->l_name;
                $parameters["mobile_no"] = $post->mobile;
                $parameters["email_id"]       = $post->email;
                $parameters["bene_name"]      = $post->f_name.' '.$post->l_name;
                $parameters["account_no"]  = $post->account;
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

                case '0':
                case '-1':
                    Report::where('id', $myaepsreport->id)->update([
                            'status' => 'failed',
                            'refno'  => (isset($data->utr_no)) ? $data->utr_no : "failed"
                        ]);
                    User::where('id', $aepsreports['user_id'])->increment('mainwallet',$aepsreports['amount']+$aepsreports['charge']);    
                    return response()->json([
                        'status' => 'TXF',
                        'statuscode' => 'TXF',
                        'message'=> (isset($data->res_message))? $data->res_message : 'Transaction Failed',
                        'rrn'    => (isset($data->utr_no))? $data->utr_no : $myaepsreport->id,
                    ]);
                break;
                } 
            break; 
            
            case 'paycornpayout':
                
                
                if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                }
            
                $post['provider_id'] = $provider->id;
                
                //$post['account'] = $post->account;
                
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
                
                $url = 'https://api.paycoonstar.com/90373567/api/v1/quick_transfers/PaycoonPayout2?payout_refno='.$post->payoutid.'&amount='.$post->amount.'&payout_mode=IMPS&user_mobile_number='.$post->mobile.'&account_name='.$post->f_name	.'&account_no='.$post->account.'&ifsc='.$post->ifsc;
                
                
                $aepsreports['api_id'] = $api->id;
                $aepsreports['number'] = $post->account;
            
                $aepsreports['provider_id']  = $provider->id;
                $aepsreports['mobile']       = $post->mobile;
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
                //dd($data);
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
                            'statuscode' => 'TXN',
                            'status'    => 'TXN', 
                            'message'   => 'Transaction Accepted',
                            'rrn'       => $myaepsreport->id
                        ]);
                break;
                } 
            break;
            }
    }
   
}
