<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\Model\Mahaagent;
use App\Model\Mahastate;
use App\Model\Report;
use App\Model\Commission;
use App\Model\Aepsreport;
use App\Model\Provider;
use App\Model\Api;

class AepsController extends Controller
{
    protected $api, $billapi, $kapi;
    public function __construct()
    {
        $this->api  = Api::where('code', 'aeps')->first();
        $this->kapi = Api::where('code', 'kaeps')->first();
        $this->billapi = Api::where('code', 'mhbill')->first();
    }

    public function index(Request $post)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('aeps_service')) {
            abort(403);
        }

        if(!$this->api || $this->api->status == 0){
            abort(405);
        }

        $data['agent'] = Mahaagent::where('user_id', \Auth::id())->first();
        $data['mahastate'] = Mahastate::get();
        if(!$data['agent']){
            $data['mahastate'] = Mahastate::get();
        }
        return view('service.aeps')->with($data);
    }

    public function initiate(Request $post)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('aeps_service')) {
            abort(403);
        }

        if(!$this->api || $this->api->status == 0){
            abort(405);
        }

        $agent = Mahaagent::where('user_id', \Auth::id())->first();
        $data['agent'] = $agent;

        $datas["bc_id"] = $agent->bc_id;
        $datas["userid"] = \Auth::id();
        $datas["phone1"] = $agent->phone1;
        $datas["saltkey"] = $this->api->username;
        $datas["secretkey"] = $this->api->password;
        $datas['ip'] = $post->ip();

        $url = $this->api->url."AEPS/BCInitiate";
        $header = array("Content-Type: application/json");

        if (env('APP_ENV') == "server") {
            $result = \Myhelper::curl($url, "POST", json_encode($datas), $header, "no");
        }else{
            $result['error']    = true;
            $result['response'] ='';
        }

        if($result['response'] != ''){
            $datas = json_decode($result['response']);
            if($datas[0]->Message == "Success"){
                return \Redirect::away("https://icici.bankmitra.org/Location.aspx?text=".$datas[0]->Result);
            }else{
                $data['error'] = isset($datas[0]->Message) ? $datas[0]->Message : "Something went wrong please contact administrator";
                return view('service.aeps')->with($data);
            }
        }else{
            $data['error'] = "Something went wrong please contact administrator";
            return view('service.aeps')->with($data);
        }
    }

    public function kaeps(Request $post)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('kaeps_service')) {
            abort(403);
        }

        if(!$this->kapi || $this->kapi->status == 0){
            abort(405);
        }

        $agent = Mahaagent::where('user_id', \Auth::id())->first();
        $data['mahastate'] = Mahastate::get();
        if(!$agent){
            return view('service.aeps')->with($data);
        }

        $data["bc_id"] = $agent->bc_id;
        $data["userid"] = \Auth::id();
        $data["phone1"] = $agent->phone1;
        $data["saltkey"] = $this->kapi->username;
        $data["secretkey"] = $this->kapi->password;
        $data['ip'] = $post->ip();

        $url = $this->kapi->url."AEPS/BCInitiate";
        $header = array("Content-Type: application/json");
        $result = \Myhelper::curl($url, "POST", json_encode($data), $header, "no");
        
        if($result['response'] != ''){
            $datas = json_decode($result['response']);
            if($datas[0]->Message == "Success"){
                return \Redirect::away("https://kotak.bankmitra.org/Location.aspx?text=".$datas[0]->Result);
            }
        }else{
            return redirect(url('dashboard'));
        }
    }

    public function registration(Request $post)
    {
        $data["bc_f_name"] = $post->bc_f_name;
        $data["bc_m_name"] = "";
        $data["bc_l_name"] = $post->bc_l_name;
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
        $data['kyc1'] = "";
        $data['kyc2'] = "";
        $data['kyc3'] = "";
        $data['kyc4'] = "";

        $url = $this->api->url."AEPS/APIBCRegistration";
        $header = array("Content-Type: application/json");
        $result = \Myhelper::curl($url, "POST", json_encode($data), $header, "no");
        if($result['response'] != ''){
            $response = json_decode($result['response']);
           // dd([$url,$data,$response]);
            if($response[0]->Message == "Success"){
                $data['bc_id'] = $response[0]->bc_id;
                $data['user_id'] = \Auth::id();
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

                    if($bbpsresult['response'] != ''){
                        $response = json_decode($bbpsresult['response']);
                        if(isset($response->Data)){
                            $datas = $response->Data;
                            if(!empty($datas)){
                                $data['bbps_agent_id'] = $datas[0]->agentid;
                            }
                        }
                    }
                } catch (\Exception $e) {}
                
                return response()->json(['statuscode'=>'TXN', 'status'=>'Transaction Successfull', 'message'=> "Kyc Submitted"]);
            }else{
                return response()->json(['statuscode'=>'TXF', 'status'=>'Transaction Failed', 'message'=> $response[0]->Message]);
            }
        }else{
            return response()->json(['statuscode'=>'TXF', 'status'=>'Transaction Failed', 'message'=> "Something went wrong"]);
        }
    }

    public function aepsaudit(Request $post)
    {
        if (\Myhelper::hasNotRole('admin')) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
        }

        $rules = array(
            'user_id'    => 'required|unique:mahaagents,user_id',
            'bc_id'    => 'required|unique:mahaagents,bc_id',
            'bc_f_name'    => 'required',
            'bc_l_name'    => 'required',
            'emailid'    => 'required',
            'phone1'    => 'required',
        );
        
        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(['status' => $error], 400);
        }

        $user = Mahaagent::updateOrCreate(['id'=> $post->id], $post->all());
        if($user){
            return response()->json(['status'=>'success', 'message'=> "Kyc Submitted"]);
        }else{
            return response()->json(['status'=>'TXF', 'message'=> "Something went wrong"]);
        }
    }

    public function iciciaepslog(Request $post)
    {
        if(!$this->api || $this->api->status == 0){
            $output['TRANSACTION_ID'] = date('Ymdhis');
            $output['VENDOR_ID'] = date('Ymdhis');
            $output['STATUS'] = "FAILED";
            $output['MESSAGE'] = "Service Down";
            return response()->json($output);
        }

        $agent = Mahaagent::where('bc_id', $post->BcId)->first();
        $user = User::where('id', $agent->user_id)->first();

        if(!$agent){
            $output['TRANSACTION_ID'] = date('Ymdhis');
            $output['VENDOR_ID'] = $agent->user_id.date('Ymdhis');
            $output['STATUS'] = "FAILED";
            $output['MESSAGE'] = "Service Down";
            return response()->json($output);
        }
        
        // if($post->Txntype == "CD" && !\Myhelper::can('cash_deposit', $agent->user_id)){
        //     $output['TRANSACTION_ID'] = date('Ymdhis');
        //     $output['VENDOR_ID'] = $agent->user_id.date('Ymdhis');
        //     $output['STATUS']    = "FAILED";
        //     $output['MESSAGE']   = "Permission Not Allowed";
        //     return response()->json($output);
        // }

        $post['provider_id'] = '0';

        $insert = [
            "mobile" => $post->EndCustMobile,
            "aadhar" => $post->BcId,
            "txnid"  => $post->TransactionId,
            "amount" => $post->Amount,
            "bank"   => $post->BankIIN,
            "user_id"=> $user->id,
            "balance" => $user->aepsbalance,
            'aepstype'=> $post->Txntype,
            'status'  => 'pending',
            'authcode'=> $post->Timestamp,
            'payid'=> $post->TerminalId,
            'TxnMedium'=> $post->TxnMedium,
            'credited_by' => $user->id,
            'type' => 'credit',
            'balance' => $user->aepsbalance,
            'provider_id' => $post->provider_id
        ];

        if(isset($post->RouteType) && $post->RouteType == "3"){
            $insert['api_id'] = $this->kapi->id;
        }else{
            $insert['api_id'] = $this->api->id;
        }

        do {
            $post['mytxnid'] = $this->transcode().rand(111111111, 999999999);
        } while (Aepsreport::where("mytxnid", "=", $post->mytxnid)->first() instanceof Report);
        $insert['mytxnid'] = $post->mytxnid;

        do {
            $post['terminalid'] = rand(11111111, 99999999);
        } while (Aepsreport::where("terminalid", "=", $post->terminalid)->first() instanceof Report);
        $insert['terminalid'] = $post->terminalid;
        
        if($post->Txntype == "CD"){
            if($user->aepsbalance < $post->Amount){
                $output['TRANSACTION_ID'] = date('Ymdhis');
                $output['VENDOR_ID'] = date('Ymdhis');
                $output['STATUS'] = "FAILED";
                $output['MESSAGE'] = "Insufficient Wallet Balance";
                return response()->json($output);
            }

            //$provider = Provider::where('recharge1', 'cashdeposit')->first();
            // if($post->Amount > 100 && $post->Amount <= 499){
            //     $provider = Provider::where('recharge1', 'cashdeposit1')->first();
            // }elseif($post->Amount>500 && $post->Amount<=1000){
            //     $provider = Provider::where('recharge1', 'cashdeposit2')->first();
            // }elseif($post->Amount>1001 && $post->Amount<=1500){
            //     $provider = Provider::where('recharge1', 'cashdeposit3')->first();
            // }elseif($post->Amount>1501 && $post->Amount<=2000){
            //     $provider = Provider::where('recharge1', 'cashdeposit4')->first();
            // }elseif($post->Amount>2001 && $post->Amount<=2500){
            //     $provider = Provider::where('recharge1', 'cashdeposit5')->first();
            // }elseif($post->Amount>2501 && $post->Amount<=3000){
            //     $provider = Provider::where('recharge1', 'cashdeposit6')->first();
            // }
            // elseif($post->Amount>3001 && $post->Amount<=4000){
            //     $provider = Provider::where('recharge1', 'cashdeposit7')->first();
            // }
            
            
            if($post->Amount > 99 && $post->Amount <= 499){
                    $provider = Provider::where('recharge1', 'aeps1')->first();
                }elseif($post->Amount>499 && $post->Amount<=1000){
                    $provider = Provider::where('recharge1', 'aeps2')->first();
                }elseif($post->Amount>1000 && $post->Amount<=1500){
                    $provider = Provider::where('recharge1', 'aeps3')->first();
                }elseif($post->Amount>1500 && $post->Amount<=2000){
                    $provider = Provider::where('recharge1', 'aeps4')->first();
                }elseif($post->Amount>2000 && $post->Amount<=2500){
                    $provider = Provider::where('recharge1', 'aeps5')->first();
                }elseif($post->Amount>2500 && $post->Amount<=3000){
                    $provider = Provider::where('recharge1', 'aeps6')->first();
                }elseif($post->Amount>3000 && $post->Amount<=4000){
                    $provider = Provider::where('recharge1', 'aeps7')->first();
                }elseif($post->Amount>4000 && $post->Amount<=5000){
                    $provider = Provider::where('recharge1', 'aeps8')->first();
                }elseif($post->Amount>5000 && $post->Amount<=7000){
                    $provider = Provider::where('recharge1', 'aeps8')->first();
                }elseif($post->Amount>7000 && $post->Amount<=10000){
                    $provider = Provider::where('recharge1', 'aeps8')->first();
                }
            
            $post['provider_id'] = $provider->id;
            $usercommission = \Myhelper::getCommission($post->Amount, $user->scheme_id, $post->provider_id, $user->role->slug);
            
            if($usercommission == 0){
                $output['TRANSACTION_ID'] = date('Ymdhis');
                $output['VENDOR_ID'] = date('Ymdhis');
                $output['STATUS'] = "FAILED";
                $output['MESSAGE'] = "Charge Not Set";
                return response()->json($output);
            }
            
            $insert['provider_id'] = $provider->id;
            $insert['charge'] = $usercommission;

            $debit = User::where('id', $user->id)->decrement('aepsbalance', $post->Amount - $usercommission);
            if($debit){
                Aepsreport::create($insert);
                
                $output['TRANSACTION_ID'] = $post->mytxnid;
                $output['VENDOR_ID'] = $post->terminalid;
                $output['STATUS'] = "SUCCESS";
                $output['MESSAGE'] = "Success";
                return response()->json($output);
        
            }else{
                $output['TRANSACTION_ID'] = date('Ymdhis');
                $output['VENDOR_ID'] = date('Ymdhis');
                $output['STATUS'] = "FAILED";
                $output['MESSAGE'] = "Transaction Failed";
                return response()->json($output);
            }
        }else{
            Aepsreport::create($insert);
            $output['TRANSACTION_ID'] = $post->mytxnid;
            $output['VENDOR_ID'] = $post->terminalid;
            $output['STATUS'] = "SUCCESS";
            $output['MESSAGE'] = "Success";
            return response()->json($output);
        
        }
    }

    public function iciciaepslogupdate(Request $post)
    {
        \DB::table('microlog')->insert(['response' => json_encode($post->all())]);
        $report = Aepsreport::where('mytxnid', $post->TransactionId)->where('terminalid', $post->VenderId)->where('aadhar', $post->BcCode)->first();

        if(!$report){
            $output['STATUS'] = "FAILED";
            $output['MESSAGE'] = "Report Not Found";
            return response()->json($output);
        }

        $user = User::where('id', $report->user_id)->first();
        
        if(isset($post->Status) && strtolower($post->Status) == "success" && $report->status == "pending"){
            $usercommission = 0;
            if($report->aepstype == "CW"){
                if($report->amount > 99 && $report->amount <= 499){
                    $provider = Provider::where('recharge1', 'aeps1')->first();
                }elseif($report->amount>499 && $report->amount<=1000){
                    $provider = Provider::where('recharge1', 'aeps2')->first();
                }elseif($report->amount>1000 && $report->amount<=1500){
                    $provider = Provider::where('recharge1', 'aeps3')->first();
                }elseif($report->amount>1500 && $report->amount<=2000){
                    $provider = Provider::where('recharge1', 'aeps4')->first();
                }elseif($report->amount>2000 && $report->amount<=2500){
                    $provider = Provider::where('recharge1', 'aeps5')->first();
                }elseif($report->amount>2500 && $report->amount<=3000){
                    $provider = Provider::where('recharge1', 'aeps6')->first();
                }elseif($report->amount>3000 && $report->amount<=4000){
                    $provider = Provider::where('recharge1', 'aeps7')->first();
                }elseif($report->amount>4000 && $report->amount<=5000){
                    $provider = Provider::where('recharge1', 'aeps8')->first();
                }elseif($report->amount>5000 && $report->amount<=7000){
                    $provider = Provider::where('recharge1', 'aeps8')->first();
                }elseif($report->amount>7000 && $report->amount<=10000){
                    $provider = Provider::where('recharge1', 'aeps8')->first();
                }
                
                $post['provider_id'] = $provider->id;
                if($report->amount > 99){
                    $usercommission = \Myhelper::getCommission($report->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                }else{
                    $usercommission = 0;
                }
                User::where('id', $report->user_id)->increment('aepsbalance', $report->amount+$usercommission);
            }elseif($report->aepstype == "AP"){
                $provider = Provider::where('recharge1', 'aadharpay')->first();		
                $post['provider_id'] = $provider->id;		
                $usercommission = \Myhelper::getCommission($report->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                User::where('id', $report->user_id)->increment('aepsbalance', $report->amount - $usercommission);	
            }elseif($report->aepstype == "CD"){
                $usercommission = $report->charge;
                $post['provider_id'] = $report->provider_id;
            }

            Aepsreport::where('id', $report->id)->update([
                'status' => "success",
                "refno"  => $post->rrn,
                "balance"=> $user->aepsbalance,
                'charge' => $usercommission,
                'provider_id' => $post->provider_id
            ]);

            try {
                if(($report->aepstype == "CW" || $report->aepstype == "CD") && $report->amount > 99){
                    $report = Aepsreport::where('id', $report->id)->first();		
                    \Myhelper::commission($report);		
                }
                
                if($report->aepstype == "AP" && $report->amount > 99){
                    $report = Aepsreport::where('id', $report->id)->first();
                    \Myhelper::commission($report);
                }
            } catch (\Exception $th) {}
            
        }else{
            Aepsreport::where('id', $report->id)->update([
                'status' => "failed",
                "refno"  => $post->bankmessage
            ]);
        }
        $output['STATUS'] = "SUCCESS";
        $output['MESSAGE'] = "Success";
        return response()->json($output);

    }
}
