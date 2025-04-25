<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Model\Mahastate;
use Illuminate\Validation\Rule;
use App\Model\Api;
use App\Model\Report;
use App\Model\Commission;
use App\Model\Aepsreport;
use App\Model\Aepsfundreport;
use App\Model\Aepsfundrequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Model\Upiid;
use Carbon\Carbon;
use App\Model\Provider;

class UpiController extends Controller
{
    protected $xettleupi;
    public function __construct()
    {
        $this->xettleupi = Api::where('code', 'xettleupi')->first();
    }
    
    public function index(Request $post)
    {
        $data['mahastate'] = \App\Model\Mahastate::get();
        $data['upi'] = \App\Model\Upiid::where('user_id', \Auth::id())->where('serviceType', 'upi')->first();
        
        if($data['upi']){
            if(!\DB::table('qrcodes')->where('vpa', $data['upi']->vpa1)->first()){
                $this->qrcodeGenerate($data['upi']->vpa1);
            }
            
            if(!\DB::table('qrcodes')->where('vpa', $data['upi']->vpa2)->first()){
                $this->qrcodeGenerate($data['upi']->vpa2);
            }
            
            $data['vpa1qr'] = \DB::table('qrcodes')->where('vpa', $data['upi']->vpa1)->first();
            $data['vpa2qr'] = \DB::table('qrcodes')->where('vpa', $data['upi']->vpa2)->first();
        }
        $data['van'] = \App\Model\Upiid::where('user_id', \Auth::id())->where('serviceType', 'van')->first();
        return view('service.upi')->with($data);
    }
    
    public function transaction(Request $post)
    {
        $post['user_id'] = \Auth::id();
        
        $rules = array(
            'businessName'  => 'required',
            'bankAccountNo' => 'required',
            'bankIfsc'      => 'required',
            "contactEmail"  => "required",
            "panNo"      => "required",
            "gstn"       => "required",
            "mobile"     => "required",
            "address"    => "required",
            "state"      => "required",
            "city"       => "required",
            "pinCode"    => "required",
            'serviceType'=> ['required', Rule::in(['upi', 'van'])]
        );

        if($post->serviceType == "upi"){
            $rules['vpaAddress'] = "required";
        }
        
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
        
        $user = User::where('id', $post->user_id)->first();
        $url  = $this->xettleupi->url."merchant";
        $header = array(
            "authorization: Basic ".base64_encode($this->xettleupi->username.":".$this->xettleupi->password),
            "cache-control: no-cache",
            "content-type: application/json"
        );
        
        $parameter = [
            "businessName"    => $post->businessName,
            "panNo"           => $post->panNo,
            "contactEmail"    => $post->contactEmail,
            "gstn"            => $post->gstn,
            "bankAccountNo"   => $post->bankAccountNo,
            "bankIfsc"        => strtoupper($post->bankIfsc),
            "mobile"      => $post->mobile,
            "address"     => $post->address,
            "state"       => $post->state,
            "city"        => $post->city,
            "pinCode"     => $post->pinCode,
            "serviceType" => $post->serviceType
        ];

        if($post->serviceType == "upi"){
            $parameter['vpaAddress'] = $post->vpaAddress;
        }
                
        $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", 'Upiid', $post->panNo);
        if($result['response'] != ""){
            $response = json_decode($result['response'], true);
            if(isset($response['code']) && $response['code'] == "0x0200"){
                $parameter['status']  = "success";
                $parameter['user_id'] = $user->id;

                if($post->serviceType == "upi"){
                    $parameter['vpa1'] = $response['data']['upi']['yesbank']['vpa'];
                    $parameter['vpa2'] = $response['data']['upi']['icici']['vpa'];
                }

                if($post->serviceType == "van"){
                    $parameter['vpa1'] = $response['data']['van']['YESB']['accountNumber']."/".$response['data']['van']['YESB']['ifsc'];
                    $parameter['vpa2'] = $response['data']['van']['IDFB']['accountNumber']."/".$response['data']['van']['IDFB']['ifsc'];
                }

                $report = Upiid::create($parameter);
                return response()->json([
                    'statuscode' => "TXN",
                    'status'     => "Transaction Successfull",
                    'message'    => "Transaction Successfull"
                ]);
            }
            
            return response()->json(['statuscode' => "ERR", "status" => isset($response->message) ? $response->message : "Something went wrong", "message" => isset($response->data) ? json_encode($response->data) : "Something went wrong"]);

        }

        return response()->json(['statuscode' => "ERR", "status" => isset($response->message) ? $response->message : "Something went wrong", "message" => isset($response->message) ? $response->message : "Something went wrong"]);
    }
    
    public function qrcodeGenerate($vpaAddress, $api="no")
    {
        $url  = $this->xettleupi->url."static/qr";
        $header = array(
            "authorization: Basic ".base64_encode($this->xettleupi->username.":".$this->xettleupi->password),
            "cache-control: no-cache",
            "content-type: application/json"
        );
        
        $parameter = [
            "vpaAddress" => $vpaAddress
        ];
                
        $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "no");
        if($result['response'] != ""){
            $response = json_decode($result['response'], true);
            if(isset($response['code']) && $response['code'] == "0x0200"){
                \DB::table('qrcodes')->insert(["vpa" => $vpaAddress , "qr" => $response['data']['qrCode']]);
                
                if($api == "yes"){
                    return response()->json([
                        'statuscode' => "TXN",
                        'status'     => "Transaction Successfull",
                        'message'    => "Transaction Successfull"
                    ]);
                }
            }
        }
        if($api == "yes"){
            return response()->json(['statuscode' => "ERR", "status" => isset($response->message) ? $response->message : "Something went wrong", "message" => isset($response->message) ? $response->message : "Something went wrong"]);
        }
    }
    
    public function Upicallback(Request $post){
        \DB::table('upilogs')->insert(['product' => 'upiLog', 'response' => json_encode($post->all())]);
        
        if($post->event=="upi.receive.success"){
            $decode = json_decode(json_encode($post->all()));
            $agent  = \DB::table('xettlemerchants')->where('vpaaddress', $decode->data->payeeVPA)->first();
            
            if($agent){
                $report = Report::where('refno', $decode->data->customerRefId)->first();
                
                $user  = User::where('id', $agent->user_id)->first();
                $provider = Provider::where('recharge1', 'upi')->first();
                $post['provider_id'] = $provider->id;
                $usercommission = \Myhelper::getCommission($decode->data->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                    
                if($decode->data->customerRefId == "134198316320"){
                    //dd($decode->data->amount, $user->scheme_id, $post->provider_id, $user->role->slug, $usercommission);
                }
                
                if(!$report){
                    $insert = [
                        "mobile"   => $agent->mobile,
                        "payeeVPA" => $agent->vpaaddress,
                        'txnid'    => $decode->data->merchantTxnRefId,
                        "refno"    => $decode->data->customerRefId,
                        "payid"    =>  $decode->data->bankTxnId,
                        'mytxnid'  => $decode->data->npciTxnId,
                        'number'   =>  $decode->data->payerAccNo,
                        'authcode' => $decode->data->originalOrderId,
                        'payerMobile'  => $decode->data->payerMobile,
                        'payerAccName' => $decode->data->payerAccName,
                        'payerIFSC'    => $decode->data->payerIFSC,
                        "amount"  => $decode->data->amount,
                        "charge"  => $usercommission,
                        "api_id"  => $provider->api->id,
                        "user_id" => $user->id,
                        'aepstype'=> "UPI",
                        'status'  => 'success',
                        'credited_by' => $user->id,
                        'trans_type' => 'credit',
                        'balance'     => $user->mainwallet,
                        'provider_id' => $post->provider_id,
                        'product'    => "upicollect"
                    ];
                    
                    if(isset($decode->code) && ($decode->code) == "0x0200"){
                        $report = Report::create($insert);
                        User::where('id', $user->id)->increment('mainwallet', $decode->data->amount - $usercommission);
                        
                        try {
                            \Myhelper::commission(Report::where('id', $report->id)->first());
                        } catch (\Exception $th) {}
                
                    }
                    
                    try {
                        if($user->role->slug == "apiuser"){
                            $output['status'] = "success";
                            $output['clientid']  = $decode->data->originalOrderId;
                            $output['txnid']     = $decode->data->merchantTxnRefId;
                            $output['vpaadress']   = $agent->vpaaddress;
                            $output['npciTxnId']   = $decode->data->npciTxnId;
                            $output['amount']      = $decode->data->amount;
                            $output['bankTxnId']   = $decode->data->bankTxnId;
                            $output['payerAccNo']  = $decode->data->payerAccNo;
                            $output['payerMobile'] = $decode->data->payerMobile;
                            $output['payerAccName']= $decode->data->payerAccName;
                            $output['payerIFSC']   = $decode->data->payerIFSC;
                            //dd($output);
                                
                            \Myhelper::curl($agent->requestUrl."?".http_build_query($output), "GET", "", [], "yes", "Report", $decode->data->merchantTxnRefId);
                        }
                    } catch (\Exception $th) {}
                }
            }
        }
        
        if($post->event=="collect.receive.success"){
            $decode = json_decode(json_encode($post->all()));
            //dd($decode);
            
            //dd($datas);
            if($decode->data->serviceType =="van"){
              $agent  = \DB::table('upiids')->where('vpa1', 'like','%'.$decode->data->vAccountNumber.'%')->orWhere('vpa2','like', '%'.$decode->data->vAccountNumber.'%')->first();

            }else{
            $agent  = \DB::table('upiids')->where('vpa1', $decode->data->virtualVpaId)->orWhere('vpa2', $decode->data->virtualVpaId)->first();
            }
            
           //dd($agent);
           
            if($agent){
                $report = Report::where('refno', $decode->data->utr)->first();
                
                $user  = User::where('id', $agent->user_id)->first();
                $provider = Provider::where('recharge1', 'upi')->first();
                $post['provider_id'] = $provider->id;
                $usercommission = \Myhelper::getCommission($decode->data->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                    
                // if($decode->data->customerRefId == "134198316320"){
                //     //dd($decode->data->amount, $user->scheme_id, $post->provider_id, $user->role->slug, $usercommission);
                // }
                
                if(!$report){
                    $insert = [
                        "mobile"   => $agent->mobile,
                        "payeeVPA" => ($decode->data->serviceType =="van") ? $decode->data->vAccountNumber : $decode->data->virtualVpaId,
                        'txnid'    => $decode->data->referenceId,
                        "refno"    => $decode->data->utr,
                        "payid"    =>  $decode->data->remitterName,
                        'number'   =>  $decode->data->remitterAccount,
                        "amount"   => $decode->data->amount,
                        "charge"   => $usercommission,
                        "api_id"   => $provider->api->id,
                        "user_id"  => $user->id,
                        'aepstype' => "UPI",
                        'status'   => 'success',
                        'credited_by' => $user->id,
                        'trans_type'  => 'credit',
                        'balance'     => $user->mainwallet,
                        'provider_id' => $post->provider_id,
                        'product'    => "upicollect"
                    ];
                    
                    if(isset($decode->code) && ($decode->code) == "0x0200"){
                        $report = Report::create($insert);
                        User::where('id', $user->id)->increment('mainwallet', $decode->data->amount - $usercommission);
                        
                        try {
                            \Myhelper::commission(Report::where('id', $report->id)->first());
                        } catch (\Exception $th) {}
                    }
                    
                    try {
                        if($user->role->slug == "apiuser"){
                            $output['status'] = "success";
                            $output['clientid']  = $decode->data->referenceId;
                            $output['txnid']     = $decode->data->referenceId;
                            $output['vpaadress']   = ($decode->data->serviceType =="van") ? $decode->data->vAccountNumber : $decode->data->virtualVpaId;
                            $output['npciTxnId']   = $decode->data->referenceId;
                            $output['amount']      = $decode->data->amount;
                            $output['bankTxnId']   = $decode->data->utr;
                            $output['payerAccNo']  = $decode->data->referenceId;
                            $output['payerMobile'] = $decode->data->referenceId;
                            $output['payerAccName']= $decode->data->remitterName;
                            $output['payerIFSC']   = $decode->data->remitterName;
                            //dd($output);
                                
                            \Myhelper::curl($agent->requestUrl."?".http_build_query($output), "GET", "", [], "yes", "Report", $decode->data->referenceId);
                        }
                    } catch (\Exception $th) {}
                }
            }
        }
    
        if($post->event=="payout.transfer.success"){
            $encode=json_encode($post->all());
            $decode=json_decode($encode);
            if(isset($decode->code) && ($decode->code) == "0x0200"){
                $myaepsreport =  Aepsfundrequest::where('payoutid', $decode->data->clientRefId)->first();
                
                if($myaepsreport){
                    Aepsfundrequest::where('payoutid', $decode->data->clientRefId)->update(['status' => "approved", "payoutref" => $decode->data->utr,'apitxnid'=>$decode->data->orderRefId]);
                    Report::where('payid', $myaepsreport->id)->update(['status' => "success", "refno" => isset($decode->data->utr) ? $decode->data->utr : $decode->data->utr]); 
                }
                
                $aepsreport =  Report::where('txnid', $decode->data->clientRefId)->first();
                if($aepsreport){
                    Report::where('id', $aepsreport->id)->update(['status' => "success", "refno" => isset($decode->data->utr) ? $decode->data->utr : $decode->data->utr]); 
                }
                
                try {
                    if($aepsreport->user->role->slug == "apiuser"){
                        $output['status'] = "success";
                        $output['txnid']  = $aepsreport->apitxnid;
                        $output['refno']  = $decode->data->utr;
                        \Myhelper::curl($aepsreport->authcode."?".http_build_query($output), "GET", "", [], "yes", "Report", $aepsreport->apitxnid);
                    }
                } catch (\Exception $th) {}
            }
        
            $output['STATUS']   = "SUCCESS";
            $output['MESSAGE']  = "Success";
            return response()->json([$output],200);
        }
    
        if($post->event=="payout.transfer.failed" || $post->event=="payout.transfer.reversed"){
            $encode=json_encode($post->all());
            $decode=json_decode($encode);
            if(isset($decode->code) && ($decode->code) == "0x0202"){
                $myaepsreport=  Aepsfundrequest::where('payoutid', $decode->data->clientRefId)->whereIn('status', ["approved","pending"])->first();
                
                if($myaepsreport){
                    Aepsfundrequest::where('payoutid', $decode->data->clientRefId)->update(['status' => "rejected", "payoutref" => $decode->data->reason,'apitxnid'=>$decode->data->orderRefId]);
                    Report::where('txnid', $decode->data->clientRefId)->update(['status' => "reversed", "refno" => $decode->data->reason]);
                    $aepsreport = Report::where('txnid', $decode->data->clientRefId)->first();
                    \Myhelper::transactionRefund($aepsreport->id);
                }else{
                    $aepsreport=  Report::where('txnid', $decode->data->clientRefId)->whereIn('status', ["success","pending"])->first();
                    if($aepsreport){
                        Report::where('txnid', $decode->data->clientRefId)->update(['status' => "reversed", "refno" => $decode->data->reason]);
                        \Myhelper::transactionRefund($aepsreport->id);
                        try {
                            if($aepsreport->user->role->slug == "apiuser"){
                                $output['status'] = "failed";
                                $output['txnid']  = $aepsreport->apitxnid;
                                $output['refno']  = $decode->data->utr;
                                \Myhelper::curl($aepsreport->authcode."?".http_build_query($output), "GET", "", [], "yes", "Report", $aepsreport->apitxnid);
                            }
                        } catch (\Exception $th) {}
                    }
                }
                
                
            }
            
            $output['STATUS']  = "SUCCESS";
            $output['MESSAGE'] = "Success";
            return response()->json([$output],200);
        }
    }
    
    public function UpiUnpecallback(Request $post)
    {
       \DB::table('microlog')->insert(['response' => json_encode($post->all())]); 
    }
}    