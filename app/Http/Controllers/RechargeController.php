<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Provider;
use App\Model\Report;
use App\User;
use Carbon\Carbon;

class RechargeController extends Controller
{
    public function index($type)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('recharge_service')) {
            abort(403);
        }
        $data['type'] = $type;
        $data['providers'] = Provider::where('type', $type)->where('status', "1")->orderBy('name')->get();
        return view('service.recharge')->with($data);
    }

    public function payment(\App\Http\Requests\Recharge $post)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('recharge_service')) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
        }
        
        $user = \Auth::user();
        $post['user_id'] = $user->id;
        if($user->status != "active"){
            return response()->json(['status' => "Your account has been blocked."], 400);
        }

        $provider = Provider::where('id', $post->provider_id)->first();

        if(!$provider){
            return response()->json(['status' => "Operator Not Found"], 400);
        }

        if($provider->status == 0){
            return response()->json(['status' => "Operator Currently Down."], 400);
        }

        if(!$provider->api || $provider->api->status == 0){
            return response()->json(['status' => "Recharge Service Currently Down."], 400);
        }
        
          if ($this->pinCheck($post) == "fail") {
            return response()->json(['status' => "Transaction Pin is incorrect"], 400);
        }

        if($user->mainwallet < $post->amount){
            return response()->json(['status'=> 'Low Balance, Kindly recharge your wallet.'], 400);
        }

        $previousrecharge = Report::where('number', $post->number)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
        if($previousrecharge > 0){
            return response()->json(['status'=> 'Same Transaction allowed after 2 min.'], 400);
        }
        
        switch ($provider->api->code) {
            case 'recharge1':
                do {
                    $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
                $url = $provider->api->url."/pay?token=".$provider->api->username."&number=".$post->number."&operator=".$provider->recharge1."&amount=".$post->amount."&apitxnid=".$post->txnid;
                break;
        }

        $post['profit'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
        $debit = User::where('id', $user->id)->decrement('mainwallet', $post->amount - $post->profit);
        if($debit){
            $insert = [
                'number' => $post->number,
                'mobile' => $user->mobile,
                'provider_id' => $provider->id,
                'api_id' => $provider->api->id,
                'amount' => $post->amount,
                'profit' => $post->profit,
                'txnid'  => $post->txnid,
                'status' => 'pending',
                'user_id'=> $user->id,
                'credit_by' => $user->id,
                'rtype' => 'main',
                'via'   => 'portal',
                'balance' => $user->mainwallet,
                'trans_type'  => 'debit',
                'product'     => 'recharge',
                'create_time' => Carbon::now()->format('Y-m-d H:i:s')
            ];

            try {
                $report = Report::create($insert);
            } catch (\Exception $e) {
                User::where('id', $user->id)->increment('mainwallet', $post->amount - $post->profit);
                return response()->json(['status' => "failed", "description" => "Something went wrong"], 200);
            }
            

            if (env('APP_ENV') == "server") {
                $result = \Myhelper::curl($url, "GET", "", [], "yes", "App\Model\Report", $post->txnid);
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
                $update['description'] = "recharge pending";
            }else{
                switch ($provider->api->code) {
                    case 'recharge1':
                        $doc = json_decode($result['response']);
                        if(isset($doc->status)){
                            if($doc->status == "TXN" || $doc->status == "TUP"){
                                $update['status'] = "success";
                                $update['payid'] = $doc->payid;
                                $update['refno'] = $doc->refno;
                                $update['description'] = "Recharge Accepted";
                            }elseif($doc->status == "TXF"){
                                $update['status'] = "failed";
                                $update['payid'] = $doc->payid;
                                $update['refno'] = $doc->refno;
                                $update['description'] = (isset($doc->message)) ? $doc->message : "failed";
                            }else{
                                $update['status'] = "failed";
                                if(isset($doc->message) && $doc->message == "Insufficient Wallet Balance"){
                                    $update['description'] = "Service down for sometime.";
                                }else{
                                    $update['description'] = (isset($doc->message)) ? $doc->message : "failed";
                                }
                            }
                        }else{
                            $update['status'] = "pending";
                            $update['payid'] = "pending";
                            $update['refno'] = "pending";
                            $update['description'] = "recharge pending";
                        }
                        break;

                    case 'recharge2':
                        $doc = json_decode($result['response']);
                        if(isset($doc->Status)){
                            if(strtolower($doc->Status) == "success" || strtolower($doc->Status) == "pending"){
                                $update['status'] = "success";
                                $update['payid'] = $doc->ApiTransID;
                                $update['refno'] = $doc->OperatorRef;
                                $update['description'] = "Recharge Accepted";
                            }elseif(strtolower($doc->Status) == "failed" || strtolower($doc->Status) == "failure" || strtolower($doc->Status) == "refund"){
                                $update['status'] = "failed";
                                $update['payid'] = $doc->ApiTransID;
                                $update['refno'] = (isset($doc->ErrorMessage)) ? $doc->ErrorMessage : "failed";
                                $update['description'] = (isset($doc->ErrorMessage)) ? $doc->ErrorMessage : "failed";
                            }else{
                                $update['status'] = "pending";
                                $update['payid'] = (isset($doc->ApiTransID)) ? $doc->ApiTransID : "pending";
                                $update['refno'] = (isset($doc->OperatorRef)) ? $doc->OperatorRef : "pending";
                                $update['description'] = (isset($doc->ErrorMessage)) ? $doc->ErrorMessage : "failed";
                            }
                        }else{
                            $update['status'] = "pending";
                            $update['payid'] = "pending";
                            $update['refno'] = "pending";
                            $update['description'] = "recharge pending";
                        }
                        break;
                }
            }

            if($update['status'] == "success" || $update['status'] == "pending"){
                Report::where('id', $report->id)->update($update);
                \Myhelper::commission($report);
            }else{
                User::where('id', $user->id)->increment('mainwallet', $post->amount - $post->profit);
                Report::where('id', $report->id)->update($update);
            }
            return response()->json($update, 200);
        }else{
            return response()->json(['status' => "failed", "description" => "Something went wrong"], 200);
        }
    }
     public function getplan(Request $post)
    {
        $provider = Provider::where('id', $post->operator)->first();

        if(!$provider){
            return response()->json(['status' => "Operator Not Found"], 400);
        }

        $url = "http://securepayments.net.in/api/recharge/getplan?token=".$provider->api->username."&operator=".$provider->recharge1;

        $result = \Myhelper::curl($url, "GET", "", [], "no");
        //dd($result);
        if($result['response'] != ''){
            $response = json_decode($result['response']);

            if(!isset($response->statuscode)){
                return response()->json(['status' => "success", "data" => $response], 200);
            }

            return response()->json(['status' => "failed", "message" => "Something went wrong"]);
        }else{
            return response()->json(['status' => "failed", "message" => "Something went wrongs"]);
        }
    }
    
    
}
