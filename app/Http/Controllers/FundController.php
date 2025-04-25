<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Model\Fundreport;
use App\Model\Aepsfundrequest;
use App\Model\Aepsreport;
use App\Model\Upifundrequest;
use App\Model\Microatmfundrequest;
use App\Model\Microatmreport;
use App\Model\Upireport;
use App\Model\Report;
use App\Model\Fundbank;
use App\Model\Paymode;
use App\Model\Api;
use App\Model\Provider;
use App\Model\PortalSetting;
use Illuminate\Validation\Rule;
use App\Classes\PaytmChecksum;
use App\Helpers\AesCipher;
use Carbon\Carbon;

class FundController extends Controller
{
    public $fundapi, $admin;

    public function __construct()
    {
        $this->fundapi = Api::where('code', 'fund')->first();
        $this->paytmsettlement  = Api::where('code', 'paytmsettlement')->first();
        $this->psettlement = Api::where('code', 'psettlement')->first();
        $this->admin = User::whereHas('role', function ($q){
            $q->where('slug', 'admin');
        })->first();

    }
    
    public function sabPaisa(Request $post)
    {
        $encData=null;

        $clientCode='2IYZ0G';
        $username='info_6307';
        $password='2IYZ0G_SP6307';
        $authKey='YncJLSMG55waN1eb';
        $authIV='gKqJkJQP8MItedNU';
        
        $payerName= \Auth::user()->name;
        $payerEmail=\Auth::user()->email;
        $payerMobile=\Auth::user()->mobile;
        $payerAddress=\Auth::user()->address;
        
        $clientTxnId=date('YmdHisA').\Auth::id().rand(1000,9999);
        $amount=$post->TXN_AMOUNT;
        $amountType='INR';
        $mcc=5137;
        $channelId='W';
        $callbackUrl=url('fund/sabpaisa/updateData');
        
        
        $encData="?clientCode=".$clientCode."&transUserName=".$username."&transUserPassword=".$password."&amount=".$amount.
        "&amountType=".$amountType."&clientTxnId=".$clientTxnId."&payerName=".$payerName."&payerMobile=".$payerMobile.
        "&payerEmail=".$payerEmail."&mcc=".$mcc."&channelId=".$channelId."&callbackUrl=".$callbackUrl;
        				
        $AesCipher = new AesCipher(); 
        $data = $AesCipher->encrypt($authKey, $authIV, $encData);
        
         return response()->json(['status'=>"TXN","data"=>$data]);
    
    }
    
    public function sabpaisaupdateData(Request $post)
    {
        \DB::table('paytmlogs')->insert(['response' => json_encode($post->all()), 'txnid' =>'SABpaisa']);
        //dd($post->encResponse);
        //$query = '4qHCSThxP+QH7cax/vKE60IqLoTPzw504UdZl56N/6WxzyQDf779LAyTa+2jCAQ+7aRyaz9Z7Uw2Bfq+hZQPz3QWBiHfQEUxT2KC4eYRyJwO+3d5e3TYaUoiRDh+9zNGqlm+BHUSlOCH30yf/RsqNsR3u81CKDuMs+KtTX4gS1lMmFRgeKYTp/++Yy4E/vh8JH41p8JKqTuYyxyeS9LE1Gojg4puWCxU70zNIk6PpiCmi4MKlaZK3zYc7KRjTeeAsJLwNuZdH94wMZQ78n8sH7NrMZ+ayyQq502bHY93EsVkzoaKdB+59wJbJBNpwW7lQkWIlsLpHbUppvrcwgr4M0Z3zmiLmTx6uOD5Ha5yjmx2a8WO6HVGwNtJW+dhZhdCj3U44fWUt1Bq3shos7v8GdIYQrnSQwOBkbp5OUIE4/lHRIboKnaL1ccKbQ5t1sWMPfVMXBf0eapmzMZI+8M4bs7ZBFLKjXxXUk3tGfV7Ws1eMbPBR1KUx9Ak+KTzWJWzXeCrGuju8y9dDLbZ2a3a3QdiOvrlG+TzEcoVk+cHPN7M5aFTZtjsAXCe5TGQreLPfP++MKF7LMVqzXCpZO5v6wsDh/XotL5n7uKuXB+8+0vg8e4ADGpINi1EJMfXK3zFGauYxXi0mqzjvOsSYhyKz8guNglI7dCl2wG9XCUtt38pvjWTg8IsiXSmmBNvt7NArB1NO/8lE4xKi5UKRdK+QJK2r1T4KnpRCTtj8Tud/6gx3Op1lI3oZNEydf0ZMrhfL12+B7c8tn0bAn7ZtG8jXT/l1mU9G09Gm+Rysl2SQ54jp8PKh5bbhTDGQRlL0M8i5I+9tbpbeoeD02rHWYdxRLlCkrfTZeqIxWSBf58iV9A=:Z0txSmtKUVA4TUl0ZWROVQ==';
        $query = $post->encResponse;

        $authKey = 'YncJLSMG55waN1eb';
        $authIV = 'gKqJkJQP8MItedNU';
        
        $decText = null;
        $AesCipher = new AesCipher();
        $decText = $AesCipher -> decrypt($authKey, $authIV, $query);
        $d = parse_str($decText, $data);
        $jsonData = json_encode($data);
        
        $paymentData = json_decode($jsonData);
       // dd($paymentData);
        $provide = Provider::where('recharge1', 'fund')->first();
        $post['provider_id'] = $provide->id;

        $user = User::where('mobile',$paymentData->payerMobile)->first();
        $userparent = 1;
        $admin=User::where('id',$userparent)->first();
        if($paymentData->statusCode === "0000" && $paymentData->status == "SUCCESS") 
        {
          
           
            if ($paymentData->statusCode === "0000")
            {
                $savedata['status']  = "approved";
                $savedata['paymode'] = $paymentData->paymentMode;
                $savedata['paydate'] = $paymentData->transDate;
                $userwalletincrment  = User::where('id',$user->id)->increment('mainwallet',$paymentData->amount);
                //$this->getGst($paymentData->amount);
                $insert = 
                [
                    'number' => $user->mobile,
                    'mobile' => $user->mobile,
                    'provider_id' =>$post->provider_id,
                    'api_id' => $this->fundapi->id,
                    'amount' => $paymentData->amount,
                    'charge' => '0.00',
                    'profit' => '0.00',
                    'gst' => '0.00',
                    'tds' => '0.00',
                    'apitxnid' => NULL,
                    'txnid' => $paymentData->clientTxnId,
                    'payid' => $paymentData->sabpaisaTxnId,
                    'refno' => $paymentData->bankTxnId,
                    'description' => NULL,
                    'remark' => $paymentData->bankMessage."- Online Payment",
                    'option1' => 1,
                    'option2' => $paymentData->paymentMode,
                    'option3' =>$paymentData->transDate,
                    'option4' => NULL,
                    'status' => 'success',
                    'user_id' => $user->id,
                    'credit_by' =>$userparent,
                    'rtype' => 'main',
                    'via' => 'portal',
                    'adminprofit' => '0.00',
                    'balance' => $user->mainwallet,
                    'trans_type' => "credit",
                    'product' => "fund request"
                ];  
                
                $usercredit = Report::create($insert);
                $admindecrement = User::where('id', $userparent)->decrement('mainwallet', $paymentData->amount);
                $admininsert = 
                [
                    'number' => $admin->mobile,
                    'mobile' => $admin->mobile,
                    'provider_id' =>$post->provider_id,
                    'api_id' => $this->fundapi->id,
                    'amount' => $paymentData->amount,
                    'charge' => '0.00',
                    'profit' => '0.00',
                    'gst' => '0.00',
                    'tds' => '0.00',
                    'apitxnid' => NULL,
                    'txnid' => $paymentData->clientTxnId,
                    'payid' => $paymentData->sabpaisaTxnId,
                    'refno' => $paymentData->bankTxnId,
                    'description' => NULL,
                    'remark' => $paymentData->bankMessage."- Online Payment",
                    'option1' => 1,
                    'option2' => $paymentData->paymentMode,
                    'option3' =>$paymentData->transDate,
                    'option4' => NULL,
                    'status' => 'success',
                    'user_id' => $admin->id,
                    'credit_by' =>$user->id,
                    'rtype' => 'main',
                    'via' => 'portal',
                    'adminprofit' => '0.00',
                    'balance' => $admin->mainwallet,
                    'trans_type' => "debit",
                    'product' => "fund request"
                ];  
                 $adminreport = Report::create($admininsert);  
            }else{
               $savedata['status'] = "failed";
               $savedata['paymode']="Online Paymnt";
               $savedata['paydate']=date('y-m-d H:i:s');
            }
                $savedata['fundbank_id'] = 1;
                $savedata['type']        = 'onlinerquest';
                $savedata['amount']      = $paymentData->amount;
                $savedata['ref_no']      = $paymentData->bankTxnId;
                $savedata['user_id']     =  $user->id;
                $savedata['credited_by'] = $userparent;
                $savedata['remark']=$paymentData->bankMessage."- Online Payment";
           
            $action = Fundreport::create($savedata);
            if($action){
                //dd("23232323");
              return response()->json(['status' => "Success"],200);
            }else{
                //dd("ABC");
                return redirect('fund/request');
            }

            
        }
        else{
            echo "<b>Checksum mismatched.</b>";
            //Process transaction as suspicious.
        }
    }

    public function index($type, $action="none")
    {
        $data = [];
        switch ($type) {
            case 'tr':
                $permission = ['fund_transfer', 'fund_return'];
                break;
            
            case 'request':
                $permission = 'fund_request';
                break;
            
            case 'requestview':
                $permission = 'setup_bank';
                break;
            
            case 'statement':
            case 'requestviewall':
                $permission = 'fund_report';
                break;

            case 'upi':
                $data['neftcharge']        = $this->neftcharge();
                $data['impschargeupto25']  = $this->impschargeupto25();
                $data['impschargeabove25'] = $this->impschargeabove25();
                
                $permission = 'aeps_fund_request';
                break;
            
            case 'upirequest':
            case 'payoutrequest':
                $permission = 'aeps_fund_view';
                break;

            case 'aepsfund':
            case 'aepsrequestall':
                $permission = 'aeps_fund_report';
                break;

            case 'microatm':
                $permission = 'microatm_fund_request';
                break;
            
            case 'microatmrequest':
                $permission = 'microatm_fund_view';
                break;

            case 'microatmfund':
            case 'microatmrequestall':
                $permission = 'microatm_fund_report';
                break;

            default:
                abort(404);
                break;
        }

       /* if (!\Myhelper::can($permission)) {
            abort(403);
        }*/

        if ($this->fundapi->status == "0") {
            abort(503);
        }

        switch ($type) {
            case 'request':
                $data['banks'] = Fundbank::where('user_id', \Auth::user()->parent_id)->where('status', '1')->get();
                if(!\Myhelper::can('setup_bank', \Auth::user()->parent_id)){
                    $admin = User::whereHas('role', function ($q){
                        $q->where('slug', 'whitelable');
                    })->where('company_id', \Auth::user()->company_id)->first(['id']);

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
                break;
        }

        return view('fund.'.$type)->with($data);
    }

    public function transaction(Request $post)
    {
        if ($this->fundapi->status == "0") {
            return response()->json(['status' => "This function is down."],400);
        }
       
        $provide = Provider::where('recharge1', 'fund')->first();
        $post['provider_id'] = $provide->id;

        switch ($post->type) {
            case 'transfer':
            case 'return':
                if($post->type == "transfer" && !\Myhelper::can('fund_transfer')){
                    return response()->json(['status' => "Permission not allowed"],400);
                }

                if($post->type == "return" && !\Myhelper::can('fund_return')){
                    return response()->json(['status' => "Permission not allowed"],400);
                }

                $rules = array(
                    'amount'    => 'required|numeric|min:1',
                );
        
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                if($post->type == "transfer"){
                    if($post->walletType == "mainwallet"){
                        if(\Auth::user()->mainwallet < $post->amount){
                            return response()->json(['status' => "Insufficient wallet balance."],400);
                        }
                    }else{
                      if(\Auth::user()->upiwallet < $post->amount){
                            return response()->json(['status' => "Insufficient Upi wallet balance."],400);
                        }  
                    }
                }else{
                    $user = User::where('id', $post->user_id)->first();
                    if($post->walletType == "mainwallet"){
                        if($user->mainwallet < $post->amount){
                            return response()->json(['status' => "Insufficient balance in user wallet."],400);
                        }
                    }else{
                         if($user->upiwallet < $post->amount){
                            return response()->json(['status' => "Insufficient balance in user Upi wallet."],400);
                        }
                    }    
                }
                $post['txnid'] = 0;
                $post['option1'] = 0;
                $post['option2'] = 0;
                $post['option3'] = 0;
                $post['refno'] = date('ymdhis');
                return $this->paymentAction($post);

                break;

            case 'requestview':
                if(!\Myhelper::can('setup_bank')){
                    return response()->json(['status' => "Permission not allowed"],400);
                }

                $fundreport = Fundreport::where('id', $post->id)->first();
                
                if($fundreport->status != "pending"){
                    return response()->json(['status' => "Request already approved"],400);
                }

                $post['amount'] = $fundreport->amount;
                $post['type'] = "request";
                $post['user_id'] = $fundreport->user_id;
                if ($post->status == "approved") {
                    if(\Auth::user()->mainwallet < $post->amount){
                        return response()->json(['status' => "Insufficient wallet balance."],200);
                    }
                    $action = Fundreport::updateOrCreate(['id'=> $post->id], [
                        "status" => $post->status,
                        "remark" => $post->remark
                    ]);

                    $post['txnid'] = $fundreport->id;
                    $post['option1'] = $fundreport->fundbank_id;
                    $post['option2'] = $fundreport->paymode;
                    $post['option3'] = $fundreport->paydate;
                    $post['refno'] = $fundreport->ref_no;
                    return $this->paymentAction($post);
                }else{
                    $action = Fundreport::updateOrCreate(['id'=> $post->id], [
                        "status" => $post->status,
                        "remark" => $post->remark
                    ]);

                    if($action){
                        return response()->json(['status' => "success"],200);
                    }else{
                        return response()->json(['status' => "Something went wrong, please try again."],200);
                    }
                }
                
                return $this->paymentAction($post);
                break;

            case 'request':
                if(!\Myhelper::hasRole('whitelable')){
                    return response()->json(['status' => "Permission not allowed"],400);
                }

                $rules = array(
                    'fundbank_id'    => 'required|numeric',
                    'paymode'    => 'required',
                    'amount'    => 'required|numeric|min:100',
                    'ref_no'    => 'required|unique:fundreports,ref_no',
                    'paydate'    => 'required'
                );
        
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $post['user_id'] = \Auth::id();
                $post['credited_by'] = \Auth::user()->parent_id;
                if(!\Myhelper::can('setup_bank', \Auth::user()->parent_id)){
                    $admin = User::whereHas('role', function ($q){
                        $q->where('slug', 'whitelable');
                    })->where('company_id', \Auth::user()->company_id)->first(['id']);

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
                if($post->hasFile('payslips')){
                    $filename ='payslip'.\Auth::id().date('ymdhis').".".$post->file('payslips')->guessExtension();
                    $post->file('payslips')->move(public_path('deposit_slip/'), $filename);
                    $post['payslip'] = $filename;
                }
                $action = Fundreport::create($post->all());
                if($action){
                    return response()->json(['status' => "success"],200);
                }else{
                    return response()->json(['status' => "Something went wrong, please try again."],200);
                }
                break;

            case 'bank':
               
                $banksettlementtype = $this->banksettlementtype();
                $impschargeupto25   = $this->impschargeupto25();
                $impschargeabove25  = $this->impschargeabove25();
                $provider   = Provider::where('recharge1', 'aepsfund')->first();
                
                if($banksettlementtype == "down"){
                    return response()->json(['status' => "Aeps Settlement Down For Sometime"],400);
                }
                
                 $user = User::where('id',\Auth::user()->id)->first();
               
                

                $post['user_id'] = \Auth::id();

                if($user->account == '' && $user->bank == '' && $user->ifsc == ''){
                    $rules = array(
                        'amount'    => 'required|numeric|min:10',
                        'account'   => 'sometimes|required',
                        'bank'   => 'sometimes|required',
                        'ifsc'   => 'sometimes|required'
                    );
                }else{
                    $rules = array(
                        'amount'    => 'required|numeric|min:10'
                    );

                    $post['account'] = $user->account;
                    $post['bank']    = $user->bank;
                    $post['ifsc']    = $user->ifsc;
                }
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }
                
                if($user->account == '' && $user->bank == '' && $user->ifsc == ''){
                    User::updateOrCreate(['id' => \Auth::user()->id], ['account' => $post->account, 'bank' => $post->bank, 'ifsc'=>$post->ifsc]);
                }

                $settlerequest = Aepsfundrequest::where('user_id', \Auth::user()->id)->where('status', 'pending')->count();
    
                if($settlerequest > 0){
                    return response()->json(['status'=> "One request is already submitted"], 400);
                }

                if($user->account == '' && $user->bank == '' && $user->ifsc == ''){
                    User::updateOrCreate(['id' => \Auth::user()->id], ['account' => $post->account, 'bank' => $post->bank, 'ifsc'=>$post->ifsc]);
                }


                if( ($user->upiwallet < $post->amount + $impschargeupto25)){
                    return response()->json(['status'=>  "Low Upi balance to make this request1."], 400);
                }

                $post['charge'] = 0;
                if($post->amount <= 25000){
                    $post['charge'] = $impschargeupto25;
                }

                if( $post->amount > 25000){
                    $post['charge'] = $impschargeabove25;
                }

               
                    $post['pay_type'] = "manual";
                    
                   // dd($post->all());
                   // $request = Aepsfundrequest::create($post->all());
                    $request = Upifundrequest::create($post->all());

                if($request){
                    return response()->json(['status'=>"success", 'message' => "Fund request successfully submitted"], 200);
                }else{
                    return response()->json(['status'=>"ERR", 'message' => "Something went wrong."], 400);
                }
                break;

            case 'wallet':
                //  if ($this->pinCheck($post) == "fail") {
                //     return response()->json(['status' => "Transaction Pin is incorrect"]);
                // }
               
                $settlementtype = $this->settlementtype();

                if($settlementtype == "down"){
                    return response()->json(['status' => "Aeps Settlement Down For Sometime"],400);
                }

                $rules = array(
                    'amount'    => 'required|numeric|min:1',
                );
        
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $user = User::where('id',\Auth::user()->id)->first();

                $request = Upifundrequest::where('user_id', \Auth::user()->id)->where('status', 'pending')->count();
                if($request > 0){
                    return response()->json(['status'=> "One request is already submitted"], 400);
                }

                if(\Auth::user()->upiwallet < $post->amount+\Auth::user()->disputewallet){
                    return response()->json(['status'=>  "Low Upi balance to make this request2"], 400);
                }

                $post['user_id'] = \Auth::id();

                if($settlementtype == "auto"){
                    $previousrecharge = Aepsfundrequest::where('type', $post->type)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                    if($previousrecharge > 0){
                        return response()->json(['status'=> "Transaction Allowed After 5 Min."]);
                    }

                    $post['status'] = "approved";
                    $load = Aepsfundrequest::create($post->all());
                    $payee = User::where('id', \Auth::id())->first();
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
                            'number' => $payee->account,
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
                    $load = Upifundrequest::create($post->all());
                }

                if($load){
                    return response()->json(['status' => "success"],200);
                }else{
                    return response()->json(['status' => "fail"],200);
                }
                break;
                
                
            
            case 'userwallet':
                //  if ($this->pinCheck($post) == "fail") {
                //     return response()->json(['status' => "Transaction Pin is incorrect"]);
                // }
               
                $settlementtype = $this->settlementtype();

                if($settlementtype == "down"){
                    return response()->json(['status' => "Aeps Settlement Down For Sometime"],400);
                }

                $rules = array(
                    'amount'    => 'required|numeric|min:1',
                );
        
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $user = User::where('id',$post->user_id)->first();

                $request = Upifundrequest::where('user_id', $user->id)->where('status', 'pending')->count();
                if($request > 0){
                    return response()->json(['status'=> "One request is already submitted"], 400);
                }

                if($user->upiwallet < $post->amount+$user->disputewallet){
                    return response()->json(['status'=>  "Low Upi balance to make this request2"], 400);
                }

                //$post['user_id'] = $post->user_id;

                if($settlementtype == "auto"){
                    $previousrecharge = Aepsfundrequest::where('type', 'wallet')->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                    if($previousrecharge > 0){
                        return response()->json(['status'=> "Transaction Allowed After 5 Min."]);
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
                            'number' => $payee->account,
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
                    $post['type'] ='wallet';
                    $load = Upifundrequest::create($post->all());
                }

                if($load){
                    return response()->json(['status' => "success"],200);
                }else{
                    return response()->json(['status' => "fail"],200);
                }
                break;    

            case 'matmbank':
                $banksettlementtype = $this->banksettlementtype();
                $impschargeupto25 = $this->impschargeupto25();
                $impschargeabove25 = $this->impschargeabove25();
                $neftcharge = $this->neftcharge(); 

                if($banksettlementtype == "down"){
                    return response()->json(['status' => "Aeps Settlement Down For Sometime"],400);
                }

                $user = User::where('id',\Auth::user()->id)->first();

                $post['user_id'] = \Auth::id();

                if($user->account == '' && $user->bank == '' && $user->ifsc == ''){
                    $rules = array(
                        'amount'    => 'required|numeric|min:10',
                        'account'   => 'sometimes|required',
                        'bank'   => 'sometimes|required',
                        'ifsc'   => 'sometimes|required'
                    );
                }else{
                    $rules = array(
                        'amount'    => 'required|numeric|min:10'
                    );

                    $post['account'] = $user->account;
                    $post['bank']    = $user->bank;
                    $post['ifsc']    = $user->ifsc;
                }

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }

                if($user->account == '' && $user->bank == '' && $user->ifsc == ''){
                    User::updateOrCreate(['id' => \Auth::user()->id], ['account' => $post->account, 'bank' => $post->bank, 'ifsc'=>$post->ifsc]);
                }

                $settlerequest = Microatmfundrequest::where('user_id', \Auth::user()->id)->where('status', 'pending')->count();
                if($settlerequest > 0){
                    return response()->json(['status'=> "One request is already submitted"], 400);
                }

                $post['charge'] = 0;
                if($post->amount <= 25000){
                    $post['charge'] = $impschargeupto25;
                }

                if($post->amount > 25000){
                    $post['charge'] = $impschargeabove25;
                }
                
                if($user->mainwallet < $post->amount + $post->charge){
                    return response()->json(['status'=>  "Low Upi balance to make this request3."], 400);
                }

                if($banksettlementtype == "auto"){

                    $previousrecharge = Microatmfundrequest::where('account', $post->account)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subSeconds(30)->format('Y-m-d H:i:s'), Carbon::now()->addSeconds(30)->format('Y-m-d H:i:s')])->count();
                    if($previousrecharge){
                        return response()->json(['status'=> "Transaction Allowed After 1 Min."]);
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
                        return response()->json(['status'=> "Duplicate Transaction Not Allowed, Please Check Transaction History"]);
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
                        "token"    => $api->username,
                        'ip'       => $post->ip(),
                        'callback' => url('api/callback/update/payout')
                    ];
                    $header = array("Content-Type: application/json");

                    if(env('APP_ENV') != "local"){
                        $result = \Myhelper::curl($url, 'POST', json_encode($parameter), $header, 'yes',$post->payoutid);
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
                        Microatmfundrequest::updateOrCreate(['id'=> $aepsrequest->id], ['status' => "approved", "payoutref" => $response->rrn]);
                        return response()->json(['status'=>"success"], 200);
                    }else{
                        User::where('id', $aepsreports['user_id'])->increment('mainwallet', $aepsreports['amount']+$aepsreports['charge']);
                        Microatmreport::updateOrCreate(['id'=> $myaepsreport->id], ['status' => "failed", "refno" => isset($response->rrn) ? $response->rrn : $response->message]);
                        Microatmfundrequest::updateOrCreate(['id'=> $aepsrequest->id], ['status' => "rejected"]);
                        return response()->json(['status'=>'ERR', 'message' => $response->message], 400);
                    }
                }else{
                    $post['pay_type'] = "manual";
                    $request = Microatmfundrequest::create($post->all());
                }

                if($request){
                    return response()->json(['status'=>"success", 'message' => "Fund request successfully submitted"], 200);
                }else{
                    return response()->json(['status'=>"ERR", 'message' => "Something went wrong."], 400);
                }
                break;

            case 'matmwallet':
                if(!\Myhelper::can('aeps_fund_request')){
                    return response()->json(['status' => "Permission not allowed"],400);
                }
                $settlementtype = $this->settlementtype();

                if($settlementtype == "down"){
                    return response()->json(['status' => "Aeps Settlement Down For Sometime"],400);
                }

                $rules = array(
                    'amount'    => 'required|numeric|min:1',
                );
        
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $user = User::where('id',\Auth::user()->id)->first();

                $request = Microatmfundrequest::where('user_id', \Auth::user()->id)->where('status', 'pending')->count();
                if($request > 0){
                    return response()->json(['status'=> "One request is already submitted"], 400);
                }

                if(\Auth::user()->mainwallet < $post->amount){
                    return response()->json(['status'=>  "Low Upi balance to make this request4"], 400);
                }

                $post['user_id'] = \Auth::id();

                if($settlementtype == "auto"){
                    $previousrecharge = Microatmfundrequest::where('type', $post->type)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                    if($previousrecharge > 0){
                        return response()->json(['status'=> "Transaction Allowed After 5 Min."]);
                    }

                    $post['status'] = "approved";
                    $load  = Microatmfundrequest::create($post->all());
                    $payee = User::where('id', \Auth::id())->first();
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
                            'number' => $payee->account,
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
                            'description' =>  "MicroAtm Fund Recieved",
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
                    return response()->json(['status' => "success"],200);
                }else{
                    return response()->json(['status' => "fail"],200);
                }
                break;
                
            case 'aepstransfer':
                if(\Myhelper::hasNotRole('admin')){
                    return response()->json(['status' => "Permission not allowed"],400);
                }

                $user = User::where('id',\Auth::user()->id)->first();
                if($user->upiwallet < $post->amount +$user->disputewallet){
                    return response()->json(['status' => "Insufficient Aeps Wallet Balance"],400);
                }

                $request = Upifundrequest::find($post->id);
                $action  = Upifundrequest::where('id', $post->id)->update(['status'=>$post->status, 'remark'=> $post->remark]);
                $payee   = User::where('id', $request->user_id)->first();

                if($action){
                    if($post->status == "approved" && $request->status == "pending"){
                        User::where('id', $payee->id)->decrement('upiwallet', $request->amount);
                        
                        $inserts = [
                            "mobile"  => $payee->mobile,
                            "amount"  => $request->amount,
                            "bank"    => $payee->bank,
                            'txnid'   => $request->id,
                            'refno'   => $post->refno,
                            "user_id" => $payee->id,
                            "credited_by" => $user->id,
                            "balance"     => $payee->upiwallet,
                            'trans_type'        => "debit",
                            'provider_id'        => "64",
                            'api_id'        => "2",
                            'transtype'   => 'fund',
                            'status'      => 'success',
                            'remark'      => "Move To Upi Wall Request",
                        ];

                        if($request->type == "wallet"){
                            $inserts['payid'] = "Wallet Transfer Request";
                            $inserts["aadhar"]= $payee->aadhar;
                        }else{
                            $inserts['payid'] = $payee->bank." ( ".$payee->ifsc." )";
                            $inserts['aadhar'] = $payee->account;
                        }
                        
                        Upireport::create($inserts);
                        
                        if($request->type == "wallet"){
                            $provide = Provider::where('recharge1', 'aepsfund')->first();
                            User::where('id', $payee->id)->increment('mainwallet', $request->amount);
                            $insert = [
                                'number' => $payee->mobile,
                                'mobile' => $payee->mobile,
                                'provider_id' => $provide->id,
                                'api_id' => $this->fundapi->id,
                                'amount' => $request->amount,
                                'charge' => '0.00',
                                'profit' => '0.00',
                                'gst' => '0.00',
                                'tds' => '0.00',
                                'txnid' => $request->id,
                                'payid' => $request->id,
                                'refno' => $post->refno,
                                'description' =>  "Aeps Fund Recieved",
                                'remark' => $post->remark,
                                'option1' => $payee->name,
                                'status' => 'success',
                                'user_id' => $payee->id,
                                'credit_by' => $user->id,
                                'rtype' => 'main',
                                'via' => 'portal',
                                'balance' => $payee->mainwallet,
                                'trans_type' => 'credit',
                                'product' => "fund request"
                            ];

                            Report::create($insert);
                        }
                    }
                    return response()->json(['status'=> "success"], 200);
                }else{
                    return response()->json(['status'=> "fail"], 400);
                }

                break;

            case 'microatmtransfer':
                if(\Myhelper::hasNotRole('admin')){
                    return response()->json(['status' => "Permission not allowed"],400);
                }

                $user = User::where('id',\Auth::user()->id)->first();
                if($user->mainwallet < $post->amount){
                    return response()->json(['status' => "Insufficient Aeps Wallet Balance"],400);
                }

                $request = Microatmfundrequest::find($post->id);
                $action  = Microatmfundrequest::where('id', $post->id)->update(['status'=>$post->status, 'remark'=> $post->remark]);
                $payee   = User::where('id', $request->user_id)->first();

                if($action){
                    if($post->status == "approved" && $request->status == "pending"){
                        User::where('id', $payee->id)->decrement('mainwallet', $request->amount);

                        $inserts = [
                            "mobile"  => $payee->mobile,
                            "amount"  => $request->amount,
                            "bank"    => $payee->bank,
                            'txnid'   => $request->id,
                            'refno'   => $post->refno,
                            "user_id" => $payee->id,
                            "credited_by" => $user->id,
                            "balance"     => $payee->mainwallet,
                            'type'        => "debit",
                            'transtype'   => 'fund',
                            'status'      => 'success',
                            'remark'      => "Move To ".ucfirst($request->type)." Request",
                        ];

                        if($request->type == "wallet"){
                            $inserts['payid'] = "Wallet Transfer Request";
                            $inserts["aadhar"]= $payee->aadhar;
                        }else{
                            $inserts['payid'] = $payee->bank." ( ".$payee->ifsc." )";
                            $inserts['aadhar'] = $payee->account;
                        }

                        Microatmreport::create($inserts);

                        if($request->type == "wallet"){
                            $provide = Provider::where('recharge1', 'aepsfund')->first();
                            User::where('id', $payee->id)->increment('mainwallet', $request->amount);
                            $insert = [
                                'number' => $payee->mobile,
                                'mobile' => $payee->mobile,
                                'provider_id' => $provide->id,
                                'api_id' => $this->fundapi->id,
                                'amount' => $request->amount,
                                'charge' => '0.00',
                                'profit' => '0.00',
                                'gst' => '0.00',
                                'tds' => '0.00',
                                'txnid' => $request->id,
                                'payid' => $request->id,
                                'refno' => $post->refno,
                                'description' =>  "MicroAtm Fund Recieved",
                                'remark' => $post->remark,
                                'option1' => $payee->name,
                                'status' => 'success',
                                'user_id' => $payee->id,
                                'credit_by' => $user->id,
                                'rtype' => 'main',
                                'via' => 'portal',
                                'balance' => $payee->mainwallet,
                                'trans_type' => 'credit',
                                'product' => "fund request"
                            ];

                            Report::create($insert);
                        }
                    }
                    return response()->json(['status'=> "success"], 200);
                }else{
                    return response()->json(['status'=> "fail"], 400);
                }

                break;
            
            case 'loadwallet':
                if(\Myhelper::hasNotRole('admin')){
                    return response()->json(['status' => "Permission not allowed"],400);
                }
                $action = User::where('id', \Auth::id())->increment('mainwallet', $post->amount);
                if($action){
                    $insert = [
                        'number' => \Auth::user()->mobile,
                        'mobile' => \Auth::user()->mobile,
                        'provider_id' => $post->provider_id,
                        'api_id' => $this->fundapi->id,
                        'amount' => $post->amount,
                        'charge' => '0.00',
                        'profit' => '0.00',
                        'gst' => '0.00',
                        'tds' => '0.00',
                        'apitxnid' => NULL,
                        'txnid' => date('ymdhis'),
                        'payid' => NULL,
                        'refno' => NULL,
                        'description' => NULL,
                        'remark' => $post->remark,
                        'option1' => NULL,
                        'option2' => NULL,
                        'option3' => NULL,
                        'option4' => NULL,
                        'status' => 'success',
                        'user_id' => \Auth::id(),
                        'credit_by' => \Auth::id(),
                        'rtype' => 'main',
                        'via' => 'portal',
                        'adminprofit' => '0.00',
                        'balance' => \Auth::user()->mainwallet,
                        'trans_type' => 'credit',
                        'product' => "fund ".$post->type
                    ];
                    $action = Report::create($insert);
                    if($action){
                        return response()->json(['status' => "success"], 200);
                    }else{
                        return response()->json(['status' => "Technical error, please contact your service provider before doing transaction."],400);
                    }
                }else{
                    return response()->json(['status' => "Fund transfer failed, please try again."],400);
                }
                break;
            
            default:
                # code...
                break;
        }
    }

    public function paymentAction($post)
    {
        $user = User::where('id', $post->user_id)->first();

        if($post->type == "transfer" || $post->type == "request"){
            if($post->walletType == "mainwallet"){
                $action = User::where('id', $post->user_id)->increment('mainwallet', $post->amount);
            }else{
              $action = User::where('id', $post->user_id)->increment('upiwallet', $post->amount);  
            }
        }else{
            if($post->walletType == "mainwallet"){
                $action = User::where('id', $post->user_id)->decrement('mainwallet', $post->amount);
            }else{
                $action = User::where('id', $post->user_id)->decrement('upiwallet', $post->amount);
            }
        }

        if($action){
            if($post->type == "transfer" || $post->type == "request"){
                $post['trans_type'] = "credit";
            }else{
                $post['trans_type'] = "debit";
            }
            if($post->walletType == "mainwallet"){
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
                    'credit_by' => \Auth::id(),
                    'rtype' => 'main',
                    'via' => 'portal',
                    'adminprofit' => '0.00',
                    'balance' => $user->mainwallet,
                    'trans_type' => $post->trans_type,
                    'product' => "fund ".$post->type
                ];
                $action = Report::create($insert);
            }else{
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
                    'credit_by' => \Auth::id(),
                    'rtype' => 'main',
                    'via' => 'portal',
                    'adminprofit' => '0.00',
                    'balance' => $user->upiwallet,
                    'trans_type' => $post->trans_type,
                    'product' => "fund ".$post->type
                ];
                $action = Upireport::create($insert); 
            }
            if($action){
                return $this->paymentActionCreditor($post);
            }else{
                return response()->json(['status' => "Technical error, please contact your service provider before doing transaction."],400);
            }
        }else{
            return response()->json(['status' => "Fund transfer failed, please try again."],400);
        }
    }

    public function paymentActionCreditor($post)
    {
        $payee = $post->user_id;
        $user = User::where('id', \Auth::id())->first();
        if($post->type == "transfer" || $post->type == "request"){
            if($post->walletType == "mainwallet"){
                $action = User::where('id', $user->id)->decrement('mainwallet', $post->amount);
            }else{
                $action = User::where('id', $user->id)->decrement('upiwallet', $post->amount);
            }    
        }else{
            if($post->walletType == "mainwallet"){
                $action = User::where('id', $user->id)->increment('mainwallet', $post->amount);
            }else{
                $action = User::where('id', $user->id)->increment('upiwallet', $post->amount);
            }
        }

        if($action){
            if($post->type == "transfer" || $post->type == "request"){
                $post['trans_type'] = "debit";
            }else{
                $post['trans_type'] = "credit";
            }
            if($post->walletType == "mainwallet"){
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
            }else{
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
                'balance' => $user->upiwallet,
                'trans_type' => $post->trans_type,
                'product' => "fund ".$post->type
            ];
            $action = Upireport::create($insert); 
            }
            
            if($action){
                return response()->json(['status' => "success"], 200);
            }else{
                return response()->json(['status' => "Technical error, please contact your service provider before doing transaction."],400);
            }
        }else{
            return response()->json(['status' => "Technical error, please contact your service provider before doing transaction."],400);
        }
    }
    
    public function getGst($amount)
    {
        return $amount*100/118;
    }
}