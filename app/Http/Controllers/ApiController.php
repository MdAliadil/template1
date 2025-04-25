<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Apitoken;
use App\User;

class ApiController extends Controller
{
    public function index($type)
    {
        $data['type'] = $type;
        return view("apitools.".$type)->with($data);
    }

    public function update(Request $post)
    {
        if (\Myhelper::hasNotRole('whitelable')) {
            return response()->json(['status' => "Permission Not Allowed1"], 400);
        }

        switch ($post->type) {
            case 'apitoken':
                return response()->json(['statuscode'=>"ERR",'status'=>"Not Allowed"]);
                $rules = array(
                    'ip'  => 'required|ip'
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                do {
                    $post['token'] = str_random(30);
                } while (Apitoken::where("token", "=", $post->token)->first() instanceof Apitoken);

                $post['user_id'] = \Auth::id();
                $action = Apitoken::updateOrCreate(['id'=> $post->id], $post->all());
                break;
            
            case 'callback':
                $rules = array(
                    'id'  => 'required',
                    'callbackurl'  => 'required',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $callback['product'] = "test";
                $callback['status']  = "test";
                $callback['refno']   = "test";
                $callback['txnid']   = "test";
                $query = http_build_query($callback);
                $url = $post->callbackurl."?".$query;

                $result = \Myhelper::curl($url, "GET", "", [], "no", "", "");
                if($result['code'] != "200"){
                    return response()->json(['status' => "Callback user is not valid"], 400);
                }
                $action = User::where('id', $post->id)->update(['callbackurl'=> $post->callbackurl]);
                break;

            case 'companycode':
                $rules = array(
                    'id'  => 'required',
                    'companycode'  => 'required',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                
                $action = User::where('id', $post->id)->update(['companycode'=> $post->companycode]);
                break;
        }

        if ($action) {
            return response()->json(['status' => "success"], 200);
        }else{
            return response()->json(['status' => "Task Failed, please try again"], 200);
        }
    }

    public function tokenDelete(Request $post)
    {
        $delete = Apitoken::where('id', $post->id)->where('user_id', \Auth::id())->delete();
        return response()->json(['status'=>$delete], 200);
    }
}