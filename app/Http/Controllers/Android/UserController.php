<?php

namespace App\Http\Controllers\Android;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\Mahaagent;
use App\Model\Api;
use App\Model\Utiid;
use App\Model\Role;
use App\Model\Companydata;
use App\Model\Provider;
use App\Model\Microatmreport;
use App\Model\Aepsreport;
use App\Model\Securedata;
use App\Helpers\AesCipher;
use App\Model\Pindata;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function login(Request $post)
    {
        $rules = array(
            'password' => 'required',
            'mobile'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = User::where('mobile', $post->mobile)->with(['role'])->first();
        if(!$user){
            return response()->json(['status' => 'ERR', 'message' => "Your aren't registred with us." ]);
        }
        
    $geodata = geoip($post->ip());
                $log['ip']           = $post->ip();
                $log['user_agent']   = $post->server('HTTP_USER_AGENT');
                $log['user_id']      = $user->id;
                $log['geo_location'] = $geodata->lat."/".$geodata->lon;
                $log['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $log['parameters']   = 'app';
                \DB::table('login_activitylogs')->insert($log);          
        
          if($user->role->slug == 'admin'){
          return response()->json(['status' => 'ERR', 'message' => "Admin Login is disabled in Application" ]);
        }

        if (!\Auth::validate(['mobile' => $post->mobile, 'password' => $post->password])) {
            $attempts = \DB::table('attempts')->where('mobile', $post->mobile)->first();
            
            if(!$attempts){
                \DB::table('attempts')->insert([
                    'mobile' => $post->mobile,
                    'login'  => 1,
                    'tpin'   => 0
                ]);
            }else{
                if($attempts->login < 2){
                    \DB::table('attempts')->where('mobile', $post->mobile)->increment('login', 1);
                }else{
                    User::where('mobile', $post->mobile)->update(['status' => "block"]);
                    \DB::table('attempts')->where('mobile', $post->mobile)->update(['login' =>  0]);
                    return response()->json(['status' => 'ERR', 'message' => "Your account has been de-activated, please contact administrator" ]);
                }
            }
            
            return response()->json(['status' => 'ERR', 'message' => 'Username and Password is incorrect, '.(2-$attempts->login)." attempts left." ]);
        }

        if (!\Auth::validate(['mobile' => $post->mobile, 'password' => $post->password, 'status' => "active"])) {
            return response()->json(['status' => 'ERR', 'message' => 'Your account currently de-activated, please contact administrator']);
        }

        $apptoken = Securedata::where('user_id', $user->id)->first();
        
        if ($apptoken) {
            return response()->json(['status' => 'ERR', 'message' => 'You are already logged in to aonther devices']);
        }

        if(!$apptoken){
            do {
                $string = str_random(40);
            } while (Securedata::where("apptoken", "=", $string)->first() instanceof Securedata);

            try {
                $apptoken = Securedata::create([
                    'apptoken' => $string,
                    'ip'       => $post->ip(),
                    'user_id'  => $user->id,
                    'last_activity' => time()
                ]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'ERR', 'message' => 'Already Logged In']);
            }
        }

        $user = User::where('mobile', $post->mobile)->with(['role'])->first();
        $user['apptoken']    = $apptoken->apptoken;
        $utiid = Utiid::where('user_id', $user->id)->first();
        $news = Companydata::where('company_id', $user->company_id)->first();  
        
        if($news){
            $user['news'] = $news->news;
            $user['notice'] = $news->notice;
            $user['billnotice'] = $news->billnotice;
            $user['supportnumber'] = $news->number;
            $user['supportemail'] = $news->email;
        }else{
            $user['news'] = "";
            $user['notice'] = "";
            $user['billnotice'] = "";
            $user['supportnumber'] = "";
            $user['supportemail'] = "";
        }

        if($utiid){
            $user['utiid'] = $utiid->vleid;
            $user['utiidtxnid'] = $utiid->id;
            $user['utiidstatus'] = $utiid->status;
        }else{
            $user['utiid'] = 'no';
            $user['utiidstatus'] = 'no';
            $user['utiidtxnid'] = 'no';
        }
        $user['tokenamount'] = '107';
        return response()->json(['status' => 'TXN', 'message' => 'User details matched successfully', 'userdata' => $user]);
    }
    
    public function logout(Request $post)
    {
        $rules = array(
            'apptoken' => 'required',
            'user_id'  => 'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }
        
        $delete = Securedata::where('user_id', $post->user_id)->where('apptoken', $post->apptoken)->delete();
        if($delete){
            return response()->json(['status' => 'TXN', 'message' => 'User Successfully Logout']);
        }else{
            return response()->json(['status' => 'ERR', 'message' => 'Something went wrong']);
        }
    }

    public function getbalance(Request $post)
    {
        $rules = array(
            'apptoken' => 'required',
            'user_id'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = User::where('id',$post->user_id)->first(['mainwallet','mainwallet']);
        if($user){
            $output['status'] = "TXN";
            $output['message'] = "Balance Fetched Successfully";
            $output['data'] = [ "mainwallet" => $user->mainwallet , "mainwallet" => $user->mainwallet];
        }else{
            $output['status'] = "ERR";
            $output['message'] = "User details not matched";
        }
        return response()->json($output);
    }

    public function aepsInitiate(Request $post)
    {
        $rules = array(
            'apptoken' => 'required',
            'user_id'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        if (!\Myhelper::can('aeps_service', $post->user_id)) {
            return response()->json(['status' => "ERR", "message" => "Service Not Allowed"]);
        }

        $user = User::where('id', $post->user_id)->count();
        if($user){
            $agent = Mahaagent::where('user_id', $post->user_id)->first();
            
            if($agent){
                $api = Api::where('code', 'aeps')->first();

                if(!$api || $api->status == 0){
                    return response()->json(['status' => "ERR", "message" => "Service Not Allowed"]);
                }

                $output['status'] = "TXN";
                $output['message'] = "Deatils Fetched Successfully";
                $output['data'] = [ 
                    "saltKey" => $api->username , 
                    "secretKey" => $api->password,
                    "BcId" => $agent->bc_id,
                    "UserId" => $post->user_id,
                    "bcEmailId" => $agent->emailid,
                    "Phone1" => $agent->phone1
                ];
            }
            else{
                $output['status'] = "ERR";
                $output['message'] = "Aeps Registration Pending";
            }
        }else{
            $output['status'] = "ERR";
            $output['message'] = "User details not matched";
        }

        return response()->json($output);
    }

    public function microatmInitiate(Request $post)
    {   
        // $user=User::where('id',$post->user_id)->first();
        
        // if($user->id != 124){
        //      return response()->json(['statuscode' => "ERR", "message" => "Permission Not Allowed"],200);
        // }
        
        \DB::table('microlog')->insert(['response' => json_encode($post->all())]);
        $rules = array(
            'apptoken' => 'required',
            'user_id'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        if (!\Myhelper::can('matm_service', $post->user_id)) {
            return response()->json(['status' => "ERR", "message" => "Service Not Alloweds"]);
        }

        $user = User::where('id', $post->user_id)->first();
        if($user){

            $agent = Mahaagent::where('user_id', $post->user_id)->first();

            if($agent){
                $api = Api::where('code', 'microatm')->first();

                if(!$api || $api->status == 0){
                    return response()->json(['status' => "ERR", "message" => "Service Not Allowed"]);
                }

                do {
                    $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                } while (Microatmreport::where("txnid", "=", $post->txnid)->first() instanceof Microatmreport);

                $insert = [
                    "mobile"   => $agent->phone1,
                    "aadhar"   => $agent->bc_id,
                    "txnid"    => $post->txnid,
                    "user_id"  => $user->id,
                    "balance"  => $user->mainwallet,
                    'status'   => 'pending',
                    'credited_by' => $user->id,
                    'type'        => 'credit',
                    'api_id'      => $api->id,
                    'aepstype'    => "matm"
                ];

                $matmreport = Microatmreport::create($insert);

                if($matmreport){
                    $output['status'] = "TXN";
                    $output['message'] = "Deatils Fetched Successfully";
                    $output['data'] = [ 
                        "saltKey" => $api->username , 
                        "secretKey" => $api->password,
                        "BcId" => $agent->bc_id,
                        "UserId" => $post->user_id,
                        "bcEmailId" => $agent->emailid,
                        "Phone1" => $agent->phone1,
                        "clientrefid" => $post->txnid
                    ];
                }else{
                    $output['status'] = "ERR";
                    $output['message'] = "Something went wrong, please try again";
                }
            }
            else{
                $output['status'] = "ERR";
                $output['message'] = "Aeps Registration Pending";
            }
        }else{
            $output['status'] = "ERR";
            $output['message'] = "User details not matched";
        }

        return response()->json($output);
    }

    public function microatmUpdate(Request $post)
    {
        \DB::table('microlog')->insert(['response' => json_encode($post->all())]);
       $rules = array(
            'apptoken' => 'required',
            'user_id'  =>'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        if (!\Myhelper::can('matm_service', $post->user_id)) {
            return response()->json(['status' => "ERR", "message" => "Service Not Allowed"]);
        }

        $user = User::where('id', $post->user_id)->first();
        if(!$user){
            $output['status'] = "ERR";
            $output['message'] = "User details not matched";
            return response()->json($output);
        }

        $response = json_decode($post->response);

        $rules = array(
            // 'clientrefid' => 'required',
            // 'refstan'     => 'required',
            // 'statuscode'  => 'required',
            // 'tid'      => 'required',
            // 'txnamount'=> 'required',
            // 'cardno'   => 'required',
            // 'mid'      => 'required',
        );

        $validator = \Validator::make((array)$response, array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'status' => 'ERR',  
                'message' => $error
            ));
        }

        $report = Microatmreport::where('txnid', $response->clientrefid)->where('user_id', $post->user_id)->first();

        if(!$report){
            $output['status'] = "ERR";
            $output['message'] = "Report Not Found";
            return response()->json($output);
        }
        //dd($response);
        

        $update['amount'] = ($response->txnamount != "") ? $response->txnamount : '0';
        $update['payid']  = (isset($response->refstan)) ? $response->refstan : '';
        $update['refno']  = $response->rrn;
        $update['remark'] = $response->bankremarks;
        $update['aadhar'] = $response->cardno;
        $update['mytxnid']= (isset($response->refstan)) ? $response->refstan : '';
        $update['balance']= $user->mainwallet;
        if($response->statuscode === '00'){
            $update['status'] = "success";
        }elseif($response->statuscode == '999'){
            $update['status'] = "pending";
        }else{
            $update['status'] = "failed";
        }
        
        $updates = Microatmreport::where('id', $report->id)->update($update);
        
        if($updates && $update['amount'] > 0 && $response->statuscode === '00'){
            if($response->txnamount >= 100 && $response->txnamount <= 500){
                $provider = Provider::where('recharge1', 'matm1')->first();
            }elseif($response->txnamount > 500 && $response->txnamount <= 1000){
                $provider = Provider::where('recharge1', 'matm2')->first();
            }elseif($response->txnamount > 1000 && $response->txnamount <= 1500){
                $provider = Provider::where('recharge1', 'matm3')->first();
            }elseif($response->txnamount > 1500 && $response->txnamount <= 2000){
                $provider = Provider::where('recharge1', 'matm4')->first();
            }elseif($response->txnamount > 2000 && $response->txnamount <= 2500){
                $provider = Provider::where('recharge1', 'matm5')->first();
            }elseif($response->txnamount > 2500 && $response->txnamount <= 3000){
                $provider = Provider::where('recharge1', 'matm6')->first();
            }elseif($response->txnamount > 3000 && $response->txnamount <= 4000){
                $provider = Provider::where('recharge1', 'matm7')->first();
            }elseif($response->txnamount > 4000 && $response->txnamount <= 5000){
                $provider = Provider::where('recharge1', 'matm8')->first();
            }elseif($response->txnamount > 5000 && $response->txnamount <= 7000){
                $provider = Provider::where('recharge1', 'matm9')->first();
            }elseif($response->txnamount > 7000 && $response->txnamount <= 10000){
                $provider = Provider::where('recharge1', 'matm10')->first();
            }
            
            $post['provider_id'] = $provider->id;
            $update['provider_id'] = $provider->id;
            if($response->txnamount > 500){
                $update['charge'] = \Myhelper::getCommission($response->txnamount, $user->scheme_id, $post->provider_id, $user->role->slug);
            }else{
                $update['charge'] = 0;
            }

            $credit = User::where('id', $user->id)->increment('mainwallet', $update['amount'] + $update['charge']);

            if($credit){
                $updates  = Microatmreport::where('id', $report->id)->update($update);
                $myreport = Microatmreport::where('id', $report->id)->first();

                $insert = [
                    "mobile"  => $myreport->mobile,
                    "aadhar"  => $myreport->aadhar,
                    "api_id"  => $myreport->api_id,
                    "provider_id"  => $provider->id,
                    "txnid"   => $myreport->txnid,
                    "refno"   => $myreport->refno,
                    "amount"  => $myreport->amount,
                    "charge"  => $myreport->charge,
                    "bank"    => $myreport->bank,
                    "user_id" => $myreport->user_id,
                    "balance" => $user->mainwallet,
                    'aepstype'=> $myreport->aepstype,
                    'status'  => 'success',
                    'authcode'=> $myreport->authcode,
                    'payid'   => $myreport->payid,
                    'mytxnid' => $myreport->mytxnid,
                    'terminalid' => $myreport->terminalid,
                    'TxnMedium'  => $myreport->TxnMedium,
                    'credited_by'=> $myreport->credited_by,
                    'type'    => 'credit',
                ];

                $matm = Report::create($insert);
                try {
                    if($response->txnamount > 500){
                        \Myhelper::commission(Report::where('id', $matm->id)->first());
                    }
                } catch (\Exception $e) {}
            }
        }
        
        $output['status']  = "TXN";
        $output['message'] = "Transaction Successfully";
            
        return response()->json($output);
    }
    
    public function registration(Request $post)
    {
        $rules = array(
            'name'       => 'required',
            'mobile'     => 'required|numeric|digits:10|unique:users,mobile',
            // 'email'      => 'required|email|unique:users,email',
            // 'shopname'   => 'required|unique:users,shopname',
            // 'pancard'    => 'required|unique:users,pancard',
            // 'aadharcard' => 'required|numeric|unique:users,aadharcard|digits:12',
            'state'      => 'required',
            'city'       => 'required',
            'address'    => 'required',
            'pincode'    => 'required|digits:6|numeric',
            'slug'       => ['required', Rule::In(['retailer', 'md', 'distributor', 'whitelable'])]
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $admin = User::whereHas('role', function ($q){
            $q->where('slug', 'admin');
        })->first(['id', 'company_id']);

        $role = Role::where('slug', $post->slug)->first();

        $post['role_id']    = $role->id;
        $post['id']         = "new";
        $post['parent_id']  = $admin->id;
        $post['password']   = bcrypt('12345678');
        $post['company_id'] = $admin->company_id;
        $post['status']     = "block";
        $post['kyc']        = "pending";

        $scheme = \DB::table('default_permissions')->where('type', 'scheme')->where('role_id', $role->id)->first();
        if($scheme){
            $post['scheme_id'] = $scheme->permission_id;
        }

        $response = User::updateOrCreate(['id'=> $post->id], $post->all());
        if($response){
            $permissions = \DB::table('default_permissions')->where('type', 'permission')->where('role_id', $post->role_id)->get();
            if(sizeof($permissions) > 0){
                foreach ($permissions as $permission) {
                    $insert = array('user_id'=> $response->id , 'permission_id'=> $permission->permission_id);
                    $inserts[] = $insert;
                }
                \DB::table('user_permissions')->insert($inserts);
            }

            try {
                $content = "Dear Partner, your login details are mobile - ".$post->mobile." & password - 12345678";
                \Myhelper::sms($post->mobile, $content);

                $otpmailid   = \App\Model\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Model\PortalSetting::where('code', 'otpsendmailname')->first();

                $mail = \Myhelper::mail('mail.member', ["username" => $post->mobile, "password" => "12345678", "name" => $post->name], $post->email, $post->name, $otpmailid, $otpmailname, "Member Registration");
            } catch (\Exception $e) {}

            return response()->json(['status' => "TXN", 'message' => "Success"], 200);
        }else{
            return response()->json(['status' => 'ERR', 'message' => "Something went wrong, please try again"], 400);
        }
    }

    public function passwordResetRequest(Request $post)
    {
        $rules = array(
            'mobile'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = \App\User::where('mobile', $post->mobile)->first();
        if($user){
            $otp = rand(11111111, 99999999);
            $content = "Dear Sahaj Money partner, your password reset token is ".$otp."-Sahaj Money";
            $sms = \Myhelper::sms($post->mobile, $content);
            $otpmailid   = \App\Model\PortalSetting::where('code', 'otpsendmailid')->first();
            $otpmailname = \App\Model\PortalSetting::where('code', 'otpsendmailname')->first();
            try {
                $mail = \Myhelper::mail('mail.password', ["token" => $otp, "name" => $user->name], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Reset Password");
            } catch (\Exception $e) {
                $mail = "fail";
            }
            
            if($sms == "success" || $mail == "success"){
                \App\User::where('mobile', $post->mobile)->update(['remember_token'=> $otp]);
                return response()->json(['status' => 'TXN', 'message' => "Password reset token sent successfully"]);
            }else{
                return response()->json(['status' => 'ERR', 'message' => "Something went wrong"]);
            }
        }else{
            return response()->json(['status' => 'ERR', 'message' => "You aren't registered with us"]);
        } 
    }

    public function passwordReset(Request $post)
    {
        $rules = array(
            'mobile'  =>'required|numeric',
            'password'  =>'required',
            'token'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = \App\User::where('mobile', $post->mobile)->where('remember_token' , $post->token)->get();
        if($user->count() == 1){
            $update = \App\User::where('mobile', $post->mobile)->update(['password' => bcrypt($post->password),'passwordold' => $post->password, 'status' => 'active']);
            if($update){
                return response()->json(['status' => "TXN", 'message' => "Password reset successfully"], 200);
            }else{
                return response()->json(['status' => 'ERR', 'message' => "Something went wrong"], 400);
            }
        }else{
            return response()->json(['status' => 'ERR', 'message' => "Please enter valid token"], 400);
        }
    }
    
    public function changepassword(Request $post)
    {
        $rules = array(
            'apptoken' => 'required',
            'user_id'  =>'required|numeric',
            'oldpassword'  =>'required|min:8',
            'password'  =>'required|min:8',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = User::where('id', $post->user_id)->first();
        if(!\Myhelper::can('password_reset', $post->user_id)){
            return response()->json(['status' => 'ERR', 'message' => "Permission Not Allowed"]);
        }

        if(\Myhelper::hasNotRole('admin')){
            $credentials = [
                'mobile' => $user->mobile,
                'password' => $post->oldpassword
            ];
    
            if(!\Auth::validate($credentials)){
                return response()->json(['status' => 'ERR', 'message' => "Please enter corret old password"]);
            }
        }

        $post['passwordold'] = $post->password;
        $post['password'] = bcrypt($post->password);

        $response = User::where('id', $post->user_id)->update(['password' => bcrypt($post->password)]);
        if($response){
            return response()->json(['status' => 'TXN', 'message' => 'User password changed successfully']);
        }else{
            return response()->json(['status' => 'ERR', 'message' => "Something went wrong"]);
        }
    }

    public function changeProfile(Request $post)
    {
        $rules = array(
            'apptoken' => 'required',
            'user_id'  =>'required|numeric',
            'name'     =>'required',
            'email'    =>'required|email',
            'address'  =>'required',
            'pincode'  =>'required|numeric|digits:6',
            'pancard'     =>'required',
            'aadharcard'  =>'required|numeric|digits:12',
            'shopname'    =>'required',
            'city'    =>'required',
            'state'   =>'required'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = User::where('id', $post->user_id)->count();

        if($user == 0){
            $output['status'] = "ERR";
            $output['message'] = "User details not matched";
            return response()->json($output);
        }

        $update = User::where('id', $post->user_id)->update(array(
            'name'     => $post->name,
            'email'    => $post->email,
            'address'  => $post->address,
            'pincode'  => $post->pincode,
            'pancard'     => $post->pancard,
            'aadharcard'  => $post->aadharcard,
            'shopname'    => $post->shopname,
            'city'    => $post->city,
            'state'   => $post->state
        ));

        if($update){
            return response()->json(['status' => 'TXN', 'message' => 'User profile updated successfully']);
        }else{
            return response()->json(['status' => 'ERR', 'message' => "Something went wrong"]);
        }
    }
    public function getotp(Request $post)
    {
        $rules = array(
            'mobile'  =>'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = \App\User::where('mobile', $post->mobile)->first();
        if($user){
            $otp = rand(111111, 999999);
            $content = "Dear Sahaj Money partner, your TPIN reset otp is ".$otp." -SSRD";
            $sms = \Myhelper::sms($post->mobile, $content);
            if($sms == "success"){
                $user = \DB::table('password_resets')->insert([
                    'mobile' => $post->mobile,
                    'token' => \Myhelper::encrypt($otp, "sdsada7657hgfh$$&7678"),
                    'last_activity' => time()
                ]);
            
                return response()->json(['statuscode' => 'TXN', 'message' => "Otp has been send successfully"]);
            }else{
                return response()->json(['statuscode' => 'ERR', 'message' => "Something went wrong"]);
            }
        }else{
            return response()->json(['statuscode' => 'ERR', 'message' => "You aren't registered with us"]);
        }  
    }
    
    public function setpin(Request $post)
    {
        //dd(\Myhelper::encrypt($post->otp, "a6e028f0c683"));
        $rules = array(
            'otp'  =>'required|numeric',
            'tpin'  =>'required|numeric|confirmed',
            'mobile'=> 'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = \DB::table('password_resets')->where('mobile', $post->mobile)->where('token' , \Myhelper::encrypt($post->otp, "sdsada7657hgfh$$&7678"))->first();
        if($user){
            try {
                Pindata::where('user_id', $post->id)->delete();
                $apptoken = Pindata::create([
                    'pin' => \Myhelper::encrypt($post->tpin, "sdsada7657hgfh$$&7678"),
                    'user_id'  => $post->user_id
                ]);
            } catch (\Exception $e) {
                return response()->json(['statuscode' => 'ERR', 'message' => 'Try Again']);
            }
            
              if($apptoken){
                \DB::table('password_resets')->where('mobile', $post->mobile)->where('token' , \Myhelper::encrypt($post->otp, "sdsada7657hgfh$$&7678"))->delete();
                \App\User::where('mobile', $post->mobile)->update(['status' => 'active']);
                return response()->json(['statuscode' => "TXN", "message" => 'Transaction Pin Generate Successfully']);
            }else{
                return response()->json(['statuscode' => "ERR", "message" => "Something went wrong"]);
            }
        }else{
            return response()->json(['statuscode' => "ERR", "message" => "Please enter valid otp"]);
        }  
    }
    
    public function addMember(Request $post)
    {
        $rules = array(
            'user_id'       => 'required',
            'name'       => 'required',
            'mobile'     => 'required|numeric|digits:10|unique:users,mobile',
            // 'email'      => 'required|email|unique:users,email',
            // 'shopname'   => 'required|unique:users,shopname',
            // 'pancard'    => 'required|unique:users,pancard',
            // 'aadharcard' => 'required|numeric|unique:users,aadharcard|digits:12',
            'state'      => 'required',
            'city'       => 'required',
            'address'    => 'required',
            'pincode'    => 'required|digits:6|numeric',
            'role_id'    => 'required'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $admin = User::where('id', $post->user_id)->first(['id', 'company_id']);

        $post['role_id']    = $post->role_id;
        $post['id']         = "new";
        $post['parent_id']  = $post->user_id;
        $post['password']   = bcrypt($post->mobile);
        $post['company_id'] = $admin->company_id;
        $post['status']     = "active";
        $post['kyc']        = "verified";

        $scheme = \DB::table('default_permissions')->where('type', 'scheme')->where('role_id', $post->role_id)->first();
        if($scheme){
            $post['scheme_id'] = $scheme->permission_id;
        }

        $response = User::updateOrCreate(['id'=> $post->id], $post->all());
        if($response){
            $permissions = \DB::table('default_permissions')->where('type', 'permission')->where('role_id', $post->role_id)->get();
            if(sizeof($permissions) > 0){
                foreach ($permissions as $permission) {
                    $insert = array('user_id'=> $response->id , 'permission_id'=> $permission->permission_id);
                    $inserts[] = $insert;
                }
                \DB::table('user_permissions')->insert($inserts);
            }

            try {
                $content = "Dear Sahaj Money partner, your login details are mobile - ".$post->mobile." & password - ".$post->mobile;
                \Myhelper::sms($post->mobile, $content);

                $otpmailid   = \App\Model\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Model\PortalSetting::where('code', 'otpsendmailname')->first();

                $mail = \Myhelper::mail('mail.member', ["username" => $post->mobile, "password" => "12345678", "name" => $post->name], $post->email, $post->name, $otpmailid, $otpmailname, "Member Registration");
            } catch (\Exception $e) {}

            return response()->json(['statuscode' => "TXN", 'message' => "Thank you for choosing, your request is successfully submitted for approval"], 200);
        }else{
            return response()->json(['statuscode' => 'ERR', 'message' => "Something went wrong, please try again"], 400);
        }
    }
    public function idStock(Request $post){
        //dd($post->type);
        $user = User::where('id', $post->user_id)->first();
       // dd($user->rstock);
            switch ($post->actiontype) {
                
                
             case 'mstock' :
             case 'dstock' :
             case 'rstock' :
                if(!\Myhelper::can('member_stock_manager',$post->user_id)){
                    return response()->json(['status' => "Permission Not Allowed"], 400);
                }

                
                    if($post->mstock > 0 && $user->mstock < $post->mstock){
                        return response()->json(['statuscode' => "ERR", "message" => "Low Id Stock"],200);
                    }

                    if($post->dstock > 0 && $user->dstock < $post->dstock){
                        return response()->json(['statuscode' => "ERR", "message" => "Low Id Stock"],200);
                    }
        
                    if($user->rstock < $post->rstock){
                        return response()->json(['statuscode' => "ERR", "message" => "Low Id Stock"],200);
                    }
                

                if($post->mstock != ''){
                    User::where('id', $post->user_id)->decrement('mstock', $post->mstock);
                    $response = User::where('id', $post->id)->increment('mstock', $post->mstock);
                }

                if($post->dstock != ''){
                    User::where('id', $post->user_id)->decrement('dstock', $post->dstock);
                    $response = User::where('id', $post->id)->increment('dstock', $post->dstock);
                }

                if($post->rstock != ''){
                    User::where('id', $post->user_id)->decrement('rstock', $post->rstock);
                    $response = User::where('id', $post->id)->increment('rstock', $post->rstock);
                }

                if($response){
                    return response()->json(['statuscode' => "TXN", "message" => "Id Stock Created Successfully"],200);
                }else{
                    return response()->json(['statuscode' => "ERR", "message" => "Something Went Wrong"],200);
                }

                break;
            }
            $response = User::where('id', $post->id)->updateOrCreate(['id'=> $post->id], $post->all());
        if($response){
            return response()->json(['statuscode' => "TXN", "message" => "Id Stock Created Successfully"],200);
        }else{
            return response()->json(['statuscode' => "ERR", "message" => "Something Went Wrong"],200);
        }

}

    
    public function aepskyc(Request $post){
        $this->api  = Api::where('code', 'aeps')->first(); 
        
        //dd($this->api);
        
        if($post->bc_l_name == ""){
            $bc_l_name= " ";
        }else{
           $bc_l_name = $post->bc_l_name;
        }
        
        if($post->bc_m_name == ""){
            $bc_m_name = " ";
        }else{
            $bc_m_name = $post->bc_m_name;
        }
        
        $data["bc_f_name"] = $post->bc_f_name;
        $data["bc_m_name"] = $bc_m_name;
        $data["bc_l_name"] = $bc_l_name;
        $data["emailid"] = $post->emailid;
        $data["phone1"] = $post->phone1;
        $data["phone2"] = $post->phone2;
        $data["bc_dob"] = $post->bc_dob;
        $data["bc_state"] = $post->bc_state;
        $data["bc_district"] = $post->bc_district;
        $data["bc_address"] = $post->bc_address;
        $data["bc_block"] = $post->bc_block;
        $data["bc_city"] = $post->bc_city;
        $data["bc_landmark"] = $post->bc_landmark;
        $data["bc_mohhalla"] = $post->bc_mohhalla;
        $data["bc_loc"] = $post->bc_loc;
        $data["bc_pincode"] = $post->bc_pincode;
        $data["bc_pan"] = $post->bc_pan;
        $data["shopname"] = $post->shopname;
        $data["shopType"] = $post->shopType;
        $data["qualification"] = $post->qualification;
        $data["population"] = $post->population;
        $data["locationType"] = $post->locationType;
        $data["saltkey"] = $this->api->username;
        $data["secretkey"] = $this->api->password;
        $data['kyc1'] = $post->kyc1;
        $data['kyc2'] = $post->kyc2;
        $data['kyc3'] = $post->kyc3;
        $data['kyc4'] = $post->kyc4;
        
        $url = $this->api->url."AEPS/APIBCRegistration";
        //dd($url);
        
        $header = array("Content-Type: application/json");
        $result = \Myhelper::curl($url, "POST", json_encode($data), $header, "no");
        if($result['response'] != ''){
            $response = json_decode($result['response']);
            if($response[0]->Message == "Success"){
                $data['bc_id'] = $response[0]->bc_id;
                $data['user_id'] = $post->user_id;
                $user = Mahaagent::create($data);

                try {
                    $gpsdata = geoip($post->ip());
                    $name  = $post->bc_f_name." ".$post->bc_l_name;
                    $burl  = $this->billapi->url."RegBBPSAgent";

                    $json_data = [
                        "requestby"     => $this->billapi->username,
                        "securityKey"   => $this->billapi->password,
                        "name"          => $name,
                        "contactperson" => $name,
                        "mobileNumber"  => $post->phone1,
                        'agentshopname' => $post->shopname,
                        "businesstype"  => $post->shopType,
                        "address1"      => $post->bc_address,
                        "address2"      => $post->bc_city,
                        "state"         => $post->bc_state,
                        "city"          => $post->bc_district,
                        "pincode"       => $post->bc_pincode,
                        "latitude"      => sprintf('%0.4f', $gpsdata->lat),
                        "longitude"     => sprintf('%0.4f', $gpsdata->lon),
                        'email'         => $post->emailid
                    ];
                    
                    $header = array(
                        "authorization: Basic ".$this->billapi->optional1,
                        "cache-control: no-cache",
                        "content-type: application/json"
                    );
                    $bbpsresult = \Myhelper::curl($burl, "POST", json_encode($json_data), $header, "yes", 'MahaBill', $post->phone1);
                   // dd($bbpsresult);
                    if($bbpsresult['response'] != ''){
                        $response = json_decode($bbpsresult['response']);
                        //dd($response);
                        if(isset($response->Data)){
                            $datas = $response->Data;
                            if(!empty($datas)){
                                $data['bbps_agent_id'] = $datas[0]->agentid;
                            }
                        }
                    }
                } catch (\Exception $e) {}
                
                return response()->json(['statuscode'=>'TXN',  'message'=> "Kyc Submitted"]);
            }else{
                //dd($response);
                return response()->json(['statuscode'=>'TXF',  'message'=> $response[0]->Message]);
            }
        }else{
            return response()->json(['statuscode'=>'TXF', 'message'=> "Something went wrong"]);
        }
        
    }
    public function GetState(Request $req){
        //dd("rttrrtrtrt");
        $url= 'http://uat.mahagram.in/Common/GetState';
       
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        
        $result= json_decode($response);
        
        //var_dump($result);
        return response()->json(['status' => 'success', 'message' => 'State Fached Successfully',"data"=>$result]);
  
}

    public function GetDistrictByState(Request $req){
        //dd("rttrrtrtrt");
        $url= 'http://uat.mahagram.in/Common/GetDistrictByState';
        $header = array("Content-Type: application/json");
        $parameter["stateid"] = $req->stateid;
        $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "no", 'App\Model\Report', '0');
        $res= $result['response'];
       // var_dump($res);
        $jsondata= json_decode($res);
        
        return response()->json(['status' => 'success', 'message' => 'District Fached Successfully',"data"=>$jsondata]);
  
} 
 public function bcstatus(Request $post){
        // dd("ttttt");
        $user = User::where('id', $post->user_id)->count();
        if($user){
            $agent = Mahaagent::where('user_id', $post->user_id)->first();
            if($agent){
               $data['bc_id'] = $agent->bc_id;
               $data['phone1'] = $agent->phone1;
               $data['status'] = $agent->status;
            }
            return response()->json(['statuscode'=>'TXN',  'message'=> "Bc id fatched successfully",'data'=>$data]);
        }
        
    }
     public function getactive(Request $post)
      {
            $rules = array(
                'apptoken' => 'required',
                'user_id'  =>'required|numeric',
            );
            
            $validate = \Myhelper::FormValidator($rules, $post);
            if($validate != "no"){
            	return $validate;
            }
            $output['data'] = [];
            
            $user = User::where('id', $post->user_id)->first();
            if(!$user){
            	$output['statuscode'] = "ERR";
    	        $output['message'] = "User details not matched";
    	        return response()->json($output);
            }
            $pserial = $user->role->pserial;
            $roles = Role::select(['id','name','slug'])->where('pserial','>',$pserial)->get()->toArray();
            if($roles){
                $output['statuscode'] = "TXN";
                $output['message'] = 'success';
                $output['data'] = $roles;
                return response()->json($output);
            }
            $output['statuscode'] = "ERR";
            $output['message'] = "Not Found";
            return response()->json($output);
        }
    
    public function sabPaisa(Request $post)
    {
        //dd($post->all());
        $encData=null;

        $clientCode='2IYZ0G';
        $username='info_6307';
        $password='2IYZ0G_SP6307';
        $authKey='YncJLSMG55waN1eb';
        $authIV='gKqJkJQP8MItedNU';
        
        $payerName='SOURAV MULLICK';
        $payerEmail='ksfsourav@gmail.com';
        $payerMobile='6296421747';
        $payerAddress='Noida';
        
        $clientTxnId=rand(1000,9999);
        $amount=$post->TXN_AMOUNT;
        $amountType='INR';
        $mcc=5137;
        $channelId='W';
        $callbackUrl='https://connect.finoviti.in/sabpaisa/SabPaisaPostPgResponse.php';
        
        $encData="?clientCode=".$clientCode."&transUserName=".$username."&transUserPassword=".$password."&amount=".$amount.
        "&amountType=".$amountType."&clientTxnId=".$clientTxnId."&payerName=".$payerName."&payerMobile=".$payerMobile.
        "&payerEmail=".$payerEmail."&mcc=".$mcc."&channelId=".$channelId."&callbackUrl=".$callbackUrl;
        				
        $AesCipher = new AesCipher(); 
        $data = $AesCipher->encrypt($authKey, $authIV, $encData);
        
         return response()->json(['status'=>"TXN","data"=>$data]);
    
    }
    public function checkBalance(Request $post,$id)
    {
        $user=User::where('id',$id)->first('mainwallet');
        return $user->mainwallet;
    }
}