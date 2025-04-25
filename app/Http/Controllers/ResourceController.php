<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Scheme;
use App\Model\Company;
use App\Model\Provider;
use App\Model\Commission;
use App\Model\Companydata;
use App\Model\Packagecommission;
use App\Model\Package;
use App\Model\Upiid;
use App\User;

class ResourceController extends Controller
{
    public function index($type)
    {
        switch ($type) {
            case 'scheme':
                $permission = "scheme_manager";
                $data['payoutOperator']    = Provider::where('type', 'payout')->where('status', "1")->get();
                $data['upiOperator']    = Provider::where('type', 'upi')->where('status', "1")->get();
                break;
                
                case 'upihandle':
                $permission = "upip2p_manager";
                if (!\Myhelper::can($permission)) {
                    abort(403);
                }
                break;

            case 'package':
                if($this->schememanager() != "all"){
                    abort(403);
                }
                $data['mobileOperator'] = Provider::where('type', 'mobile')->where('status', "1")->get();
                $data['dthOperator'] = Provider::where('type', 'dth')->where('status', "1")->get();
                $data['ebillOperator'] = Provider::where('type', 'electricity')->where('status', "1")->get();
                $data['pancardOperator'] = Provider::where('type', 'pancard')->where('status', "1")->get();
                $data['nsdlpanOperator'] = Provider::where('type', 'nsdlpan')->where('status', "1")->get();
                $data['dmtOperator'] = Provider::where('type', 'dmt')->where('status', "1")->get();
                $data['aepsOperator'] = Provider::where('type', 'aeps')->where('status', "1")->get();
                $data['upiOperator']    = Provider::where('type', 'upi')->where('status', "1")->get();
                $data['payoutOperator']    = Provider::where('type', 'payout')->where('status', "1")->get();
                break;

            case 'company':
                $permission = "company_manager";
                break;

            case 'companyprofile':
                $permission = "change_company_profile";
                $data['company'] = Company::where('id', \Auth::user()->company_id)->first();
                $data['companydata'] = Companydata::where('company_id', \Auth::user()->company_id)->first();
                break;
            
            case 'commission':
                $permission = "view_commission";
                $product = ['upi', 'payout'];

                if($this->schememanager() != "all"){
                    foreach ($product as $key) {
                        $data['commission'][$key] = Commission::where('scheme_id', \Auth::user()->scheme_id)->whereHas('provider', function ($q) use($key){
                            $q->where('type' , $key);
                        })->get();
                    }
                }else{
                    foreach ($product as $key) {
                        $data['commission'][$key] = Packagecommission::where('scheme_id', \Auth::user()->scheme_id)->whereHas('provider', function ($q) use($key){
                            $q->where('type' , $key);
                        })->get();
                    }
                }
                
                break;
            
            default:
                # code...
                break;
        }

        
        $data['type'] = $type;

        return view("resource.".$type)->with($data);
    }

    public function update(Request $post)
    {
        switch ($post->actiontype) {
            case 'scheme':
            case 'commission':
                $permission = "scheme_manager";
                break;
            
            case 'upiid':
                $permission = "upip2p_manager";
                break;
            
            case 'company':
                $permission = ["company_manager", "change_company_profile"];
                break;

            case 'companydata':
                $permission = "change_company_profile";
                break;
        }

        

        switch ($post->actiontype) {
            case 'scheme':
                $rules = array(
                    'name'    => 'sometimes|required|unique:schemes,name' 
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $post['user_id'] = \Auth::id();
                $action = Scheme::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
                
            case 'upiid':
                $rules = array(
                    'vpa' => 'sometimes|required|unique:upiids,vpa',

                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $post['user_id'] = \Auth::id();
                $action = Upiid::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'package':
                $rules = array(
                    'name'    => 'sometimes|required|unique:packages,name' 
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $post['user_id'] = \Auth::id();
                $action = Package::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'company':
                $rules = array(
                    'companyname'    => 'sometimes|required'
                );

                if($post->file('logos')){
                    $rules['logos'] = 'sometimes|required|mimes:jpg,JPG,jpeg,png|max:500';
                }
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                if($post->id != 'new'){
                    $company = Company::find($post->id);
                }
                
                if($post->hasFile('logos')){
                    try {
                        unlink(public_path('logos/').$company->logo);
                    } catch (\Exception $e) {
                    }
                    $filename ='logo'.$post->id.".".$post->file('logos')->guessExtension();
                    $post->file('logos')->move(public_path('logos/'), $filename);
                    $post['logo'] = $filename;
                }

                $action = Company::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'companydata':
                $rules = array(
                    'company_id'    => 'required'
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $action = Companydata::updateOrCreate(['company_id'=> $post->company_id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            
            case 'commission':
                $rules = array(
                    'scheme_id'    => 'sometimes|required|numeric' 
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                foreach ($post->slab as $key => $value) {
                    $update[$value] = Commission::updateOrCreate([
                        'scheme_id' => $post->scheme_id,
                        'slab'      => $post->slab[$key]
                    ],[
                        'scheme_id' => $post->scheme_id,
                        'slab'      => $post->slab[$key],
                        'type'      => $post->type[$key],
                        'whitelable'=> $post->whitelable[$key],
                        'reseller'        => $post->reseller[$key],
                        
                    ]);
                }
                return response()->json(['status'=>$update], 200);
                break;

            case 'packagecommission':
                $rules = array(
                    'scheme_id'    => 'sometimes|required|numeric' 
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                foreach ($post->slab as $key => $value) {
                    $data     = Packagecommission::where('scheme_id',\Auth::user()->scheme_id)->where('slab', $value)->first();
                    $provider = Provider::where('id', $value)->first();
                    $pass = true;

                    if(\Myhelper::hasNotRole('admin') && $data){
                        if($data->provider->type == "dmt"){
                            if($post->type[$key] == "flat" && $post->value[$key] > 50 ){
                                $pass = false;
                                $update[$post->slab[$key]] = "value shouldn't be greater than 50";
                            }

                            if($post->type[$key] == "percent" && $post->value[$key] > 1 ){
                                $pass = false;
                                $update[$post->slab[$key]] = "value shouldn't be greater than 1";
                            }
                        }
                    }

                    if($post->value[$key] < 0 ){
                        $pass = false;
                        $update[$post->slab[$key]] = "value should be greater than 0";
                    }

                    if(\Myhelper::hasNotRole('admin') && !$data){
                        $pass = false;
                        $update[$post->slab[$key]] = "Your commission not set by parent";
                    }

                    if(\Myhelper::hasNotRole('admin') && $data){
                        if(
                            $provider->type == "mobile" || 
                            $provider->type == "electricity"|| 
                            $provider->type == "dth"  || 
                            $provider->type == "pancard" || 
                            $provider->type == "aeps" ||
                            $provider->type == "upi"
                        ){
                            if($data->value < $post->value[$key]){
                                $pass = false;
                                $update[$post->slab[$key]] = "value shouldn't be greater than ".$data->value;
                            }
                        }

                        if(($provider->type == "dmt" && $provider->recharge1 != "dmt1accverify") || $provider->type == "nsdlpan"){
                            if($data->value > $post->value[$key]){
                                $pass = false;
                                $update[$post->slab[$key]] = "value shouldn't be less than ".$data->value;
                            }
                        }
                    }

                    if(\Myhelper::hasNotRole('admin') && $data){
                        $slabtype = $data->type;
                    }else{
                        $slabtype = $post->type[$key];
                    }
                    if($pass){
                        $update[$value] = Packagecommission::updateOrCreate(
                            [
                                'scheme_id' => $post->scheme_id,
                                'slab'      => $post->slab[$key],
                            ],
                            [
                                'scheme_id' => $post->scheme_id,
                                'slab'      => $post->slab[$key],
                                'type'      => $slabtype,
                                'value'     => $post->value[$key]
                            ]
                        );
                    }
                }
                return response()->json(['status'=>$update], 200);
                break;
            
            default:
                # code...
                break;
        }
    }

    public function getCommission(Request $post , $type)
    {
        return Commission::where('scheme_id', $post->scheme_id)->get()->toJson();
    }

    public function getPackageCommission(Request $post , $type)
    {
        return Packagecommission::where('scheme_id', $post->scheme_id)->get()->toJson();
    }

    public function mycommission(Type $var = null)
    {
        # code...
    }
}
