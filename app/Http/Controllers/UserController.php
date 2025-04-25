<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Model\Pindata;
use App\Model\Circle;
use App\Model\Role;
use Illuminate\Validation\Rule;
class UserController extends Controller
{
     public function index()
    {
        //$data['state'] = Circle::all();
        $data['roles'] = Role::whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer'])->get();
        return view('welcome')->with($data);
    }
    
    // public function postdata(Request $post)
    // {
    //     dd($post->server()['REMOTE_ADDR']);
    // }
    
    public function registerpage()
    {
        
        $data['state'] = Circle::all();
        $data['roles'] = Role::whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer'])->get();
        return view('register')->with($data);
    }
    public function servicepage()
    {
        $data['state'] = Circle::all();
        $data['roles'] = Role::whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer'])->get();
        return view('services')->with($data);
    }
     public function contactpage()
    {
        $data['state'] = Circle::all();
        $data['roles'] = Role::whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer'])->get();
        return view('contact')->with($data);
    }
    public function login(Request $post)
    {    
        $user = User::where('email', $post->email)->first();
        //dd($user);
        if(!$user){
            return response()->json(['status' => "Your aren't registred with us." ], 400);
        }
        if($user->role->slug =="admin"){
            return response()->json(['status' => "You are not allowed to login,and we are watching on your acctivity" ], 400);
        }
         
   // $geodata = geoip($post->ip());

                $log['ip']           = $post->ip();
                $log['user_agent']   = $post->server('HTTP_USER_AGENT');
                $log['user_id']      = $user->id;
                //$log['geo_location'] = $geodata->lat."/".$geodata->lon;
                $log['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $log['parameters']   = 'portal';
                \DB::table('login_activitylogs')->insert($log);          
        $company = \App\Model\Company::where('id', $user->company_id)->first();
        $otprequired = \App\Model\PortalSetting::where('code', 'otplogin')->first();

        if(!\Auth::validate(['email' => $post->email, 'password' => $post->password])){
            $attempts = \DB::table('attempts')->where('mobile', $post->email)->first();
            
            if(!$attempts){
                \DB::table('attempts')->insert([
                    'mobile' => $post->email,
                    'login'  => 1,
                    'tpin'   => 0
                ]);
            }else{
                if($attempts->login < 2){
                    \DB::table('attempts')->where('mobile', $post->email)->increment('login', 1);
                }else{
                    User::where('email', $post->email)->update(['status' => "block"]);
                    \DB::table('attempts')->where('mobile', $post->email)->update(['login' =>  0]);
                    return response()->json(['status' => "Your Account is Blocked Please Use Forgot password Option & Set new password" ], 400);
                }
            }
            
            return response()->json(['status'=> 'Username or password is incorrect '], 400);
        }

        if (!\Auth::validate(['email' => $post->email, 'password' => $post->password,'status'=> "active"])) {
            return response()->json(['status' => 'Your account currently de-activated, please contact administrator'], 400);
        }

        if($otprequired->value == "yes" && $company->senderid){
            if($post->has('otp') && $post->otp == "resend"){
                if($user->otpresend < 3){
                    $otp = rand(111111, 999999);
                    $msg = "Dear Sahaj Money partner, your login otp is ".$otp;
                    $send = \Myhelper::sms($post->mobile, $msg);
                    if($send == 'success'){
                        User::where('mobile', $post->mobile)->update(['otpverify' => $otp, 'otpresend' => $user->otpresend+1]);
                        return response()->json(['status' => 'otpsent'], 200);
                    }else{
                        return response()->json(['status' => 'Please contact your service provider provider'], 400);
                    }
                }else{
                    return response()->json(['status' => 'Otp resend limit exceed, please contact your service provider'], 400);
                }
            }

            if($user->otpverify == "yes"){
                $otp  = rand(111111, 999999);
                $msg  = "Dear Sahaj Money partner, your login otp is ".$otp;
                
                $send = \Myhelper::sms($post->mobile, $msg);
                $otpmailid   = \App\Model\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Model\PortalSetting::where('code', 'otpsendmailname')->first();
                $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                if($send == 'success'){
                    User::where('mobile', $post->mobile)->update(['otpverify' => $otp]);
                    return response()->json(['status' => 'otpsent'], 200);
                }else{
                    return response()->json(['status' => 'Please contact your service provider provider'], 400);
                }
            }else{
                if(!$post->has('otp')){
                    return response()->json(['status' => 'preotp'], 200);
                }
            }

            if (\Auth::attempt(['mobile' =>$post->mobile, 'password' =>$post->password, 'otpverify' =>$post->otp, 'status'=>"active"])){
                return response()->json(['status' => 'Login'], 200);
            }else{
                return response()->json(['status' => 'Please provide correct otp'], 400);
            }

        }else{
            if (\Auth::attempt(['email' =>$post->email, 'password' =>$post->password, 'status'=> "active"])) {
                return response()->json(['status' => 'Login'], 200);
            }else{
                return response()->json(['status' => 'Something went wrong, please contact administrator'], 400);
            }
        }
    }
    
    
    public function adminLogin(Request $post)
    {
        $user = User::where('mobile', $post->mobile)->first();
        if(!$user){
            return response()->json(['status' => "Your aren't registred with us." ], 400);
        }
        if($user->role->slug !="admin"){
            return response()->json(['status' => "You are not allowed to login,and we are watching on your acctivity" ], 400);
        }
       
    $geodata = geoip($post->ip());
                $log['ip']           = $post->ip();
                $log['user_agent']   = $post->server('HTTP_USER_AGENT');
                $log['user_id']      = $user->id;
                $log['geo_location'] = $geodata->lat."/".$geodata->lon;
                $log['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $log['parameters']   = 'portal';
                \DB::table('login_activitylogs')->insert($log);          
        $company = \App\Model\Company::where('id', $user->company_id)->first();
        $otprequired = \App\Model\PortalSetting::where('code', 'otplogin')->first();

        if(!\Auth::validate(['mobile' => $post->mobile, 'password' => $post->password])){
            $attempts = \DB::table('attempts')->where('mobile', $post->email)->first();
            
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
                    return response()->json(['status' => "Your Account is Blocked Please Use Forgot password Option & Set new password" ], 400);
                }
            }
            
            return response()->json(['status'=> 'Username or password is incorrect '], 400);
        }

        if (!\Auth::validate(['mobile' => $post->mobile, 'password' => $post->password,'status'=> "active"])) {
            return response()->json(['status' => 'Your account currently de-activated, please contact administrator'], 400);
        }

        if($otprequired->value == "yes" && $company->senderid){
            if($post->has('otp') && $post->otp == "resend"){
                if($user->otpresend < 3){
                    $otp = rand(111111, 999999);
                    $msg = "Dear Sahaj Money partner, your login otp is ".$otp;
                    $send = \Myhelper::sms($post->mobile, $msg);
                    if($send == 'success'){
                        User::where('mobile', $post->mobile)->update(['otpverify' => $otp, 'otpresend' => $user->otpresend+1]);
                        return response()->json(['status' => 'otpsent'], 200);
                    }else{
                        return response()->json(['status' => 'Please contact your service provider provider'], 400);
                    }
                }else{
                    return response()->json(['status' => 'Otp resend limit exceed, please contact your service provider'], 400);
                }
            }

            if($user->otpverify == "yes"){
                $otp  = rand(111111, 999999);
                $msg  = "Dear Sahaj Money partner, your login otp is ".$otp;
                
                $send = \Myhelper::sms($post->mobile, $msg);
                $otpmailid   = \App\Model\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Model\PortalSetting::where('code', 'otpsendmailname')->first();
                $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                if($send == 'success'){
                    User::where('mobile', $post->mobile)->update(['otpverify' => $otp]);
                    return response()->json(['status' => 'otpsent'], 200);
                }else{
                    return response()->json(['status' => 'Please contact your service provider provider'], 400);
                }
            }else{
                if(!$post->has('otp')){
                    return response()->json(['status' => 'preotp'], 200);
                }
            }

            if (\Auth::attempt(['mobile' =>$post->mobile, 'password' =>$post->password, 'otpverify' =>$post->otp, 'status'=>"active"])){
                return response()->json(['status' => 'Login'], 200);
            }else{
                return response()->json(['status' => 'Please provide correct otp'], 400);
            }

        }else{
            if (\Auth::attempt(['mobile' =>$post->mobile, 'password' =>$post->password, 'status'=> "active"])) {
                return response()->json(['status' => 'Login'], 200);
            }else{
                return response()->json(['status' => 'Something went wrong, please contact administrator'], 400);
            }
        }
    }

    public function logout(Request $request)
    {
        \Auth::guard()->logout();
        $request->session()->invalidate();
        return redirect('/');
    }

    public function passwordReset(Request $post)
    {
        $rules = array(
            'type' => 'required',
            'mobile'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        if($post->type == "request" ){
            $user = \App\User::where('mobile', $post->mobile)->first();
            if($user){
                $company = \App\Model\Company::where('id', $user->company_id)->first();
                $otp     = rand(11111111, 99999999);
                if($company->senderid){
                    $content = "Dear Sahaj Money partner, your password reset token is ".$otp."-Sahaj Money";
                    $sms     = \Myhelper::sms($post->mobile, $content);
                    //dd($sms);
                }else{
                    $sms = false;
                }
                $otpmailid   = \App\Model\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Model\PortalSetting::where('code', 'otpsendmailname')->first();
                 $mail = \Myhelper::mail('mail.password', ["token" => $otp, "name" => $user->name], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Reset Password");
                // dd($mail);
                try {
                    $mail = \Myhelper::mail('mail.password', ["token" => $otp, "name" => $user->name], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Reset Password");
                } catch (\Exception $e) {
                    return response()->json(['status' => 'ERR', 'message' => "Something went wrong1"], 400);
                }
                //dd($mail);
                if($sms == "success" || $mail == "success"){
                    \App\User::where('mobile', $post->mobile)->update(['remember_token'=> $otp]);
                    return response()->json(['status' => 'TXN', 'message' => "Password reset token sent successfully"], 200);
                }else{
                    return response()->json(['status' => 'ERR', 'message' => "Something went wrong2"], 400);
                }
            }else{
                return response()->json(['status' => 'ERR', 'message' => "You aren't registered with us"], 400);
            }
        }else{
            $user = \App\User::where('mobile', $post->mobile)->where('remember_token' , $post->token)->get();
            if($user->count() == 1){
                $update = \App\User::where('mobile', $post->mobile)->update(['password' => bcrypt($post->password), 'passwordold' => $post->password, 'status' => 'active']);
                if($update){
                    return response()->json(['status' => "TXN", 'message' => "Password reset successfully"], 200);
                }else{
                    return response()->json(['status' => 'ERR', 'message' => "Something went wrong3"], 400);
                }
            }else{
                return response()->json(['status' => 'ERR', 'message' => "Please enter valid token"], 400);
            }
        }  
    }
     public function getotp(Request $post)
    {
        //dd("test");
        $rules = array(
            'mobile'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = \App\User::where('mobile', $post->mobile)->first();
        //dd($user);
        if($user){
            $otp = rand(111111, 999999);
            //dd($otp);
            $content = "Dear Sahaj Money partner, your TPIN reset otp is ".$otp." -SSRD";
             	
                $sms = \Myhelper::sms($post->mobile, $content);
            
                $otpmailid   = \App\Model\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Model\PortalSetting::where('code', 'otpsendmailname')->first();
                try {
                    $mail = \Myhelper::mail('mail.password', ["token" => $otp, "name" => $user->name], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Tpin Otp");
                } catch (\Exception $e) {
                    return response()->json(['status' => 'ERR', 'message' => "Something went wrong"], 400);
                }
            
            if($sms == "success"|| $mail=="success"){
                $user = \DB::table('password_resets')->insert([
                    'mobile' => $post->mobile,
                    'token' => \Myhelper::encrypt($otp, "sdsada7657hgfh$$&7678"),
                    'last_activity' => time()
                ]);
            
                return response()->json(['status' => 'TXN', 'message' => "Pin generate token sent successfully"], 200);
            }else{
                return response()->json(['status' => 'ERR', 'message' => "Something went wrong"], 400);
            }
        }else{
            return response()->json(['status' => 'ERR', 'message' => "You aren't registered with us"], 400);
        }  
    }
    
    public function setpin(Request $post)
    {
        //dd(\Myhelper::encrypt($post->otp, "a6e028f0c683"));
        $rules = array(
            'id'  =>'required|numeric',
            'otp'  =>'required|numeric',
            'pin'  =>'required|numeric|confirmed',
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
                    'pin' => \Myhelper::encrypt($post->pin, "sdsada7657hgfh$$&7678"),
                    'user_id'  => $post->id
                ]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'ERR', 'message' => 'Try Again']);
            }
            
            if($apptoken){
                \DB::table('password_resets')->where('mobile', $post->mobile)->where('token' , \Myhelper::encrypt($post->otp, "sdsada7657hgfh$$&7678"))->delete();
                return response()->json(['status' => "success"], 200);
            }else{
                return response()->json(['status' => "Something went wrong"], 400);
            }
        }else{
            return response()->json(['status' => "Please enter valid otp"], 400);
        }  
    }
   
    public function registration(Request $post)
    {
        $rules = array(
            'name'       => 'required',
            'mobile'     => 'required|numeric|digits:10|unique:users,mobile',
            'email'      => 'required|email|unique:users,email',
            'shopname'   => 'required|unique:users,shopname',
            'pancard'    => 'required|unique:users,pancard',
            'aadharcard' => 'required|numeric|unique:users,aadharcard|digits:12',
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

            // try {
            //     $tmpid="1207161685683573618";
            //     $content = "Dear Partner,Your Login details are Uid-".$post->mobile.". & password ".$post->mobile.".Our Representative will contact you soon for id Activation Regards IYDA Payments";
                
            //     \Myhelper::sms($post->mobile, $content,$tmpid);

            //     $otpmailid   = \App\Model\PortalSetting::where('code', 'otpsendmailid')->first();
            //     $otpmailname = \App\Model\PortalSetting::where('code', 'otpsendmailname')->first();

            //     $mail = \Myhelper::mail('mail.member', ["username" => $post->mobile, "password" => "12345678", "name" => $post->name], $post->email, $post->name, $otpmailid, $otpmailname, "Member Registration");
            // } catch (\Exception $e) {}

            return response()->json(['status' => "TXN", 'message' => "Success"], 200);
        }else{
            return response()->json(['status' => 'ERR', 'message' => "Something went wrong, please try again"], 400);
        }
    }
     
    public function createVanAll(Request $post)
    {
        $users = User::all();
        foreach($users as $user){
           $insert = [
             'vanAccount'=>'ZGROSC2'.$user->mobile,
             'user_id'=>$user->id
            ];
            
           $create  = \DB::table('apiusersVan')->insert($insert);  
            
        }
        
    }
    
    public function paySprintRBLuat(Request $post,$type)
    {
        $url = "https://uatnxtgen.sprintnxt.in/api/v1/payout/PAYOUT";
        switch($type){
            case 'activebank':
                $param = [
                    "apiId"=>"30001"
                    ];
                break;
            
            case 'allbank':
                $param = [
                    "apiId"=>"30009"
                    ];
                break;
            
            case 'activeaccount':
                $param = [
                    "apiId"=>"30002",
                    "bank_id"=>"5",
                    "type"=>"payout"
                    ];
                break;
            
            case 'gettxnstatus':
                $param = [
                    "apiId"=>"30011",
                    "bankId"=>"5",
                    "transferId"=>"4791",
                    "sprintnxtTxnId"=>""
                    ];
                break;
            
            case 'fetchaccbal':
                $param = [
                    "apiId"=>"30003",
                    "bankId"=>"5",
                    "acctNumber"=>"409002136531"
                    ];
                break;
            
            case 'accstatement':
                $param = [
                   /* {
                    "apiId":"30012",//required
                    "bankId":"5",//required
                    "acctNumber":"409002136531",//required
                    "fromDate":"25-05-2024",//required
                    "toDate":"29-05-2024",//required
                    //"numberOfTxn":"10",//required only in case of CANARA
                    "transType":"D" //required only in case of RBL  
                    }*/

                    "apiId"=>"30012",
                    "bankId"=>"5",
                    "acctNumber"=>"409002136531",
                    "fromDate"=>"25-05-2024",
                    "toDate"=>"28-06-2024",
                    "transType"=>"D",
                    ];
                break;
            
            case 'intpayout':
                $param = [
                    "apiId"=>"30008",
                    "bankId"=>"5",
                    "acctNumber"=>"409002136531",
                    "beneAcctNumber"=>"4166451441216238",
                    "amount"=>"1",//required transaction amount 
                    "purpose"=>"TESTING",//required
                    "mode"=>"neft",//required
                    "type"=>1,//required
                    "name"=>"SOURAV",//required
                    "mobile"=>"6296421747",//required
                    "ifsc"=>"KKBK0000958",//required
                    "bankname"=>"Kotak",//required
                    "branchname"=>"Delhi",//required
                    "beneaddress"=>"Delhi",//required
                    "transferId"=>rand(1111,9999),//required
  
 
                    ];
                break;
        }
        
        $jsonParam = json_encode($param);
        $payLoadKey = $this->encData($param);
        //dd($payLoadKey);
        $body=[
            "body" => [
                "payload"=>$payLoadKey['payload'],
                "key"=>$payLoadKey['key'],
                "partnerId"=>"NlRJUE5OUk",
                "clientid"=>"U1BSX05YVF91YXRfOTc3YThmYmJiY2VmNjU4Nw=="
                ]
            ];
            
        $header = array(
                'accept: application/json',
                'content-type: application/hal+json',
                'client-id:U1BSX05YVF91YXRfOTc3YThmYmJiY2VmNjU4Nw==',
                'key:'.$payLoadKey['key'],
                'partnerId:NlRJUE5OUk',
            ); 
         
        $result = \Myhelper::curlReq($url, "POST",json_encode($body), $header, "no");
        //dd($result);
        //$jsonBody = json_encode($body);
        //dd($result['response']);
        $doc = json_decode($result['response']);
        //dd($doc);
        $rsp=$this->decData($result['headers'],$doc->body);
        
        //dd(["URL"=>$url, "Method"=>"POST","RawJsonBody"=>json_encode($body),"ReqHeader"=>$header,"Response"=>$doc,"DecodeKey"=>$payLoadKey['key'],"DecryptBody"=>$doc->body,"DecRes"=>$rsp,"Response"=>$doc]);
        //dd(["URL"=>$url,"encBody"=>json_encode($body),"Header"=>$header,"method"=>"POST","Response"=>$result['response']]);
        
        return response()->json(["URL"=>$url, "Method"=>"POST","RawJsonBody"=>json_encode($body),"ReqHeader"=>$header,"Response"=>$doc,"DecodeKey"=>$payLoadKey['key'],"DecryptBody"=>$doc->body,"DecRes"=>$rsp,"Response"=>$doc]);
        
    }
    
    
    public static function encData($data,$passphrase = ""){
        $key = openssl_random_pseudo_bytes(32);
        $encrypteddata = openssl_encrypt(json_encode($data), 'AES-256-ECB', $key,OPENSSL_RAW_DATA);
        openssl_public_encrypt($key, $encryptedKey, file_get_contents(storage_path('app/sprintnxt_public.key')));
        $encodedData = base64_encode($encrypteddata);
        $encodedKey = base64_encode($encryptedKey);
        return [
            'payload'=>$encodedData,
            'key'=>$encodedKey
        ];
    }
    
    public  function decData($key,$token){
        try {
            $encryptedData = base64_decode($token);
            $encryptedKey = base64_decode($key);
           // dd([$encryptedData,$encryptedKey]);
            $check=openssl_private_decrypt($encryptedKey, $decryptedKey, file_get_contents(storage_path('app/partner_private.key')));
            if(!$check){
                throw new \Exception('Private key decryption failed: ' . openssl_error_string());
            }
            $decryptedData = openssl_decrypt($encryptedData, 'AES-256-ECB', $decryptedKey,OPENSSL_RAW_DATA);
            //dd($decryptedData);
            $t = json_decode($decryptedData);
            //dd($t);
            return $t;
        } catch (\Throwable $th) {
            return json_encode(['message' => $th->getMessage()]);
        }
    }
}