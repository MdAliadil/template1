<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Utiid;
use App\Model\Report;
use App\Model\Provider;
use App\Model\Circle;
use App\User;
use Carbon\Carbon;
use App\Model\Nsdlpan;

class PancardController extends Controller
{
    public function index($type)
    {
        switch ($type) {
            case 'uti':
                $permission = "utipancard_service";
                break;

            case 'nsdl':
                $permission = "nsdl_service";
                break;
            
            default:
                abort(404);
                break;
        }

        if (!\Myhelper::can($permission)) {
            abort(403);
        }
        $data['type'] = $type;

        switch ($type) {
            case 'uti':
                $data['vledata'] = Utiid::where('user_id', \Auth::id())->first();
                if($data['vledata'] && $data['vledata']->status == "pending"){
                    $provider = Provider::where('recharge1', 'utipancard')->first();
                    $url = $provider->api->url."UATUTIAgentRequestStatus";
                    $parameter['securityKey'] = $provider->api->password;
                    $parameter['createdby']   = $provider->api->username;
                    $parameter['requestid']   = $data['vledata']->payid;

                    $result = \Myhelper::curl($url, "POST", json_encode($parameter), ["Content-Type: application/json", "Accept: application/json"], "no");
                    //dd($result);
                    if(!$result['error'] || $result['response'] != ''){
                        $doc = json_decode($result['response']);
                        if(isset($doc[0]->StatusCode) && $doc[0]->StatusCode == "000"){
                            Utiid::where('user_id', \Auth::id())->update(['status' => 'success', 'remark' => "Done"]);
                        }else{
                            Utiid::where('user_id', \Auth::id())->update(['remark' => $doc[0]->Message]);
                        }
                        $data['vledata'] = Utiid::where('user_id', \Auth::id())->first();
                    }
                }
                break;

            case 'nsdl':
                $data['aocodes'] = \DB::table('ao_codes')->get();
                $data['state'] = Circle::get();
                break;
        }

        return view("service.".$type)->with($data);
    }

    public function payment(Request $post)
    {
        switch ($post->actiontype) {
            case 'vleid':
            case 'purchase':
                $permission = "utipancard_service";
                break;
        }

        if (isset($permission) && !\Myhelper::can($permission)) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
        }

        switch ($post->actiontype) {
            case 'vleid':
                $vledata = Utiid::where('user_id', \Auth::id())->where('status', 'failed')->first();
                if($vledata){
                    Utiid::where('user_id', \Auth::id())->where('status', 'failed')->delete();
                }

                $user = User::where('id', \Auth::id())->first();
                $post['name'] = $user->shopname;
                $post['location'] = $user->address." ".$user->city;
                $post['contact_person'] = $user->name;
                $post['pincode'] = $user->pincode;
                $post['state'] = $user->state;
                $post['email'] = $user->email;
                $post['mobile'] = $user->mobile;
                $post['adhaar'] = $user->aadharcard;
                $post['pan'] = $user->pancard;
                $post['user_id'] = \Auth::id();
                $post['type'] = "new";
                //dd($post->all());
                do {
                    $post['txnid'] = "EB".rand(1111111111, 9999999999);
                } while (Utiid::where("txnid", "=", $post->txnid)->first() instanceof Utiid);
                
                $rules = array(
                    'name'    => 'required',
                    'location'    => 'required',
                    'contact_person'    => 'required',
                    'pincode'    => 'required|numeric|digits:6',
                    'state'    => 'required',
                    'email'    => 'required',
                    'mobile'    => 'required|numeric|digits_between:10,11',
                    'adhaar'    => 'required|numeric|digits:12',
                    'pan'    => 'required',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    return response()->json(['status' => $error], 400);
                }

                $provider = Provider::where('recharge1', 'utipancard')->first();
                $post['api_id'] = $provider->api_id;
                if(!$provider){
                    return response()->json(['status' => "Operator Not Found"], 400);
                }
        
                if($provider->status == 0){
                    return response()->json(['status' => "Operator Currently Down."], 400);
                }
        
                if(!$provider->api || $provider->api->status == 0){
                    return response()->json(['status' => "Utipancard Service Currently Down."], 400);
                }

                $parameter['createdby'] = $provider->api->username;
                $parameter['securityKey'] = $provider->api->password;
                $parameter['psaname'] = $post->name;
                $parameter['location'] = $post->location;
                $parameter['contactperson'] = $post->contact_person;
                $parameter['pincode'] = $post->pincode;
                $parameter['state'] = $post->state;
                $parameter['emailid'] = $post->email;
                $parameter['phone1'] = $post->mobile;
                $parameter['phone2'] = $post->mobile;
                $parameter['adhaar'] = $post->adhaar;
                $parameter['pan'] = $post->pan;
                $parameter['dob'] = date('m/d')."/1989";
                $parameter['udf1'] = $post->txnid;
                $parameter['udf2'] = "";
                $parameter['udf3'] = "";
                $parameter['udf4'] = "";
                $parameter['udf5'] = "";
                $url = $provider->api->url."UATInsUTIAgent";

                $result = \Myhelper::curl($url, "POST", json_encode($parameter), ["Content-Type: application/json", "Accept: application/json"], "yes", 'App\Model\Utiid' , $post->txnid);
                //dd([$url, $result, $parameter]);
                if(!$result['error'] || $result['response'] != ''){
                    $doc = json_decode($result['response']);
                    if(isset($doc[0]->StatusCode) && $doc[0]->StatusCode == "000"){
                        $post['payid'] = $doc[0]->Request;
                        $post['vleid'] = $doc[0]->psaid;
                        $action = Utiid::create($post->all());
                        if ($action) {
                            return response()->json(['status' => "success"], 200);
                        }else{
                            return response()->json(['status' => "Task Failed, please try again"], 200);
                        }
                    }else{
                        return response()->json(['status' =>(isset($doc[0]->Message))? $doc[0]->Message : "Task Failed, please try again"], 200);
                    }
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'purchase':
                $rules = array(
                    'vleid'    => 'required',
                    'tokens'    => 'required|numeric|min:1',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $provider = Provider::where('recharge1', 'utipancard')->first();
                $post['provider_id'] = $provider->id;
                if(!$provider){
                    return response()->json(['status' => "Operator Not Found"], 400);
                }
                
                 if ($this->pinCheck($post) == "fail") {
                    return response()->json(['status' => "Transaction Pin is incorrect"], 400);
                }
        
                if($provider->status == 0){
                    return response()->json(['status' => "Operator Currently Down."], 400);
                }
        
                if(!$provider->api || $provider->api->status == 0){
                    return response()->json(['status' => "Utipancard Service Currently Down."], 400);
                }

                $user = \Auth::user();
                $post['user_id'] = $user->id;
                if($user->status != "active"){
                    return response()->json(['status' => "Your account has been blocked."], 400);
                }

                if($user->mainwallet < $post->tokens * 107){
                    return response()->json(['status'=> 'Low Balance, Kindly recharge your wallet.'], 400);
                }
                $vledata = Utiid::where('user_id', \Auth::id())->first();

                $previousrecharge = Report::where('number', $vledata->vleid)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                if($previousrecharge > 0){
                    return response()->json(['status'=> 'Same Transaction allowed after 2 min.'], 400);
                }
                
                $post['amount'] = $post->tokens * 107;
                $post['profit'] = $post->tokens * \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                do {
                    $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

                $action = User::where('id', $post->user_id)->decrement('mainwallet', $post->amount - $post->profit);
                if ($action) {
                    $insert = [
                        'number' => $vledata->vleid,
                        'mobile' => $user->mobile,
                        'provider_id' => $provider->id,
                        'api_id' => $provider->api->id,
                        'amount' => $post->amount,
                        'profit' => $post->profit,
                        'txnid' => $post->txnid,
                        'option1' => $post->tokens,
                        'status' => 'pending',
                        'user_id'    => $user->id,
                        'credit_by'  => $user->id,
                        'rtype'      => 'main',
                        'via'        => 'portal',
                        'balance'    => $user->mainwallet,
                        'trans_type' => 'debit',
                        'product'    => 'utipancard'
                    ];

                    $report = Report::create($insert);
                    
                    $parameter['createdby'] = $provider->api->username;
                    $parameter['securityKey'] = $provider->api->password;
                    $parameter['totalcoupon_physical'] = $post->tokens;
                    $parameter['psaid'] = $vledata->vleid;
                    $parameter['transactionid'] = $post->txnid;
                    $parameter['transactiondate'] = date('m/d/Y h:i:s A');
                    $parameter['udf1'] = $post->txnid;
                    $parameter['udf2'] = "";
                    $parameter['udf3'] = "";
                    $parameter['udf4'] = "";
                    $parameter['udf5'] = "";

                    if (env('APP_ENV') == "server") {
                        $url = $provider->api->url."UATInsCouponRequest";
                        $result = \Myhelper::curl($url, "POST", json_encode($parameter), ["Content-Type: application/json", "Accept: application/json"], "yes", "App\Model\Report", $post->txnid);
                    }else{
                        $result = [
                            'error' => true,
                            'response' => '' 
                        ];
                    }
        
                    if($result['error'] || $result['response'] == ''){
                        $update['status'] = "pending";
                        $update['payid'] = "pending";
                        $update['refno'] = "pending";
                        $update['description'] = "pan token request pending";
                    }else{
                        $doc = json_decode($result['response']);
                        if(isset($doc[0]->StatusCode)){
                            if($doc[0]->StatusCode == "000"){
                                $update['status'] = "success";
                                $update['payid'] = $doc[0]->RequestId;
                                $update['description'] = "Pancard Token Request Accepted";
                            }else{
                                $update['status'] = "failed";
                                if($doc[0]->StatusCode == "008"){
                                    $update['description'] = "Service down for sometime.";
                                }else{
                                    $update['description'] = (isset($doc[0]->message)) ? $doc[0]->message : "failed";
                                }
                            }
                        }else{
                            $update['status'] = "pending";
                            $update['payid'] = "pending";
                            $update['refno'] = "pending";
                            $update['description'] = "pan token request pending";
                        }
                    }
        
                    if($update['status'] == "success" || $update['status'] == "pending"){
                        Report::where('id', $report->id)->update($update);
                        $post['reportid'] = $report->id;
                        \Myhelper::commission($report);
                    }else{
                        User::where('id', $user->id)->increment('mainwallet', $post->amount - $post->profit);
                        Report::where('id', $report->id)->update($update);
                    }
                    return response()->json($update, 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'nsdl':
                $rules = array(
                    'mobile'        => 'required|numeric|digits:10',
                    'adhaarnumber'  => 'required|numeric|digits:12',
                    'raddpincode'   => 'required|numeric|digits:6',
                    'adhaarpics'    => 'required|mimes:pdf|max:2048',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    return response()->json(['status' => $error], 400);
                }

                $provider = Provider::where('recharge1', 'nsdlpancard')->first();
                $post['provider_id'] = $provider->id;
                if(!$provider){
                    return response()->json(['status' => "Operator Not Found"], 400);
                }
        
                if($provider->status == 0){
                    return response()->json(['status' => "Operator Currently Down."], 400);
                }
        
                if(!$provider->api || $provider->api->status == 0){
                    return response()->json(['status' => "Utipancard Service Currently Down."], 400);
                }

                $user = \Auth::user();
                $post['user_id'] = $user->id;
                if($user->status != "active"){
                    return response()->json(['status' => "Your account has been blocked."], 400);
                }
                $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);

                if($user->mainwallet < $post->charge){
                    return response()->json(['status'=> 'Low Balance, Kindly recharge your wallet.'], 400);
                }

                $transact['api_id'] = $provider->api->id;
                $transact['provider_id'] = $post->provider_id;
                $transact['mobile'] = $user->mobile;
                $transact['number'] = $user->mobile;
                $transact['user_id'] = $user->id;
                $transact['status'] = "pending";
                $transact['credit_by'] = $user->id;
                $transact['trans_type'] = "debit";
                $transact['product'] = "nsdlpan";
                $transact['service'] = $provider->type;
                $transact['balance'] = $user->mainwallet;
                $transact['amount'] = $post->charge;

                $action = User::where('id', $post->user_id)->decrement('mainwallet', $transact['amount']);
                if ($action) {
                    $txnid = Report::create($transact);
                    $post['txnid'] = $txnid->id;
                    if($post->hasFile('adhaarpics')){
                        $filename ='nsdlpanformsadhar'.\Auth::id().date('ymdhis').".".$post->file('adhaarpics')->guessExtension();
                        $post->file('adhaarpics')->move(public_path('nsdlpanforms/'), $filename);
                        $post['adhaarpic'] = $filename;
                    }

                    if($post->type == "correction"){
                        $post['correction_value'] = implode("|", $post->correction_value);
                    }

                    $pancard = Nsdlpan::create($post->all());
                    $txn     = Report::where('id', $txnid->id)->update(['txnid'=>$pancard->id]);
                    \Myhelper::commission($txnid);
                    return response()->json(['status'=> "success"], 200);
                }else{
                    return response()->json(['status'=> "fail"], 400);
                }
                break;
                
        }
    }

    public function nsdlview($id)
    {
        $data['pancard'] = Nsdlpan::where('id', $id)->first();
        return view('pancard.nsdlview')->with($data);
    }
}
