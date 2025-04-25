<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Fundbank;
use App\Model\Api;
use App\Model\Provider;
use App\Model\PortalSetting;
use App\Model\Complaintsubject;
use App\Model\Link;
use Illuminate\Validation\Rule;

class SetupController extends Controller
{
    public function index($type)
    {
        switch ($type) {
            case 'api':
                $permission = "setup_api";
                break;

            case 'bank':
                $permission = "setup_bank";
                break;

            case 'operator':
                $permission = "setup_operator";
                $data['apis'] = Api::whereIn('type', ['recharge', 'bill', 'pancard', 'money','fund'])->where('status', '1')->get(['id', 'product']);
                break;
            
            case 'complaintsub':
                $permission = "complaint_subject";
                break;

            case 'portalsetting':
                $data['settlementtype'] = PortalSetting::where('code', 'settlementtype')->first();
                $data['banksettlementtype'] = PortalSetting::where('code', 'banksettlementtype')->first();
                $data['otplogin'] = PortalSetting::where('code', 'otplogin')->first();
                $data['otpsendmailid']   = PortalSetting::where('code', 'otpsendmailid')->first();
                $data['otpsendmailname'] = PortalSetting::where('code', 'otpsendmailname')->first();
                $data['bcid']   = \App\Model\PortalSetting::where('code', 'bcid')->first();
                $data['cpid']   = \App\Model\PortalSetting::where('code', 'cpid')->first();
                $data['transactioncode']   = \App\Model\PortalSetting::where('code', 'transactioncode')->first();
                $data['mainlockedamount']   = \App\Model\PortalSetting::where('code', 'mainlockedamount')->first();
                $data['aepslockedamount']   = \App\Model\PortalSetting::where('code', 'aepslockedamount')->first();
                $data['settlementcharge']   = \App\Model\PortalSetting::where('code', 'settlementcharge')->first();
                $data['impschargeupto25']   = \App\Model\PortalSetting::where('code', 'impschargeupto25')->first();
                $data['impschargeabove25']   = \App\Model\PortalSetting::where('code', 'impschargeabove25')->first();
                $data['aepsslabtime']   = \App\Model\PortalSetting::where('code', 'aepsslabtime')->first();
                $data['bankpayoutapi']  = \App\Model\PortalSetting::where('code', 'bankpayoutapi')->first();
                $data['bankupiapi']  = \App\Model\PortalSetting::where('code', 'bankupiapi')->first();
                $permission = "portal_setting";
                break;

            case 'links':
                $permission = "setup_links";
                break;
            
            default:
                abort(404);
                break;
        }

        if (!\Myhelper::can($permission)) {
            abort(403);
        }
        $data['type'] = $type;

        return view("setup.".$type)->with($data);
    }

    public function update(Request $post)
    {
        switch ($post->actiontype) {
            case 'api':
                $permission = "setup_api";
                break;

            case 'bank':
                $permission = "setup_bank";
                break;

            case 'operator':
                $permission = "setup_operator";
                break;

            case 'complaintsub':
                $permission = "complaint_subject";
                break;

            case 'portalsetting':
                $permission = "portal_setting";
                break;

            case 'links':
                $permission = "setup_links";
                break;
        }

        if (isset($permission) && !\Myhelper::can($permission)) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
        }

        switch ($post->actiontype) {
            case 'bank':
                $rules = array(
                    'name'    => 'sometimes|required',
                    'account'    => 'sometimes|required|numeric|unique:fundbanks,account'.($post->id != "new" ? ",".$post->id : ''),
                    'ifsc'    => 'sometimes|required',
                    'branch'    => 'sometimes|required'  
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $post['user_id'] = \Auth::id();
                $action = Fundbank::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            
            case 'api':
                $rules = array(
                    'product'    => 'sometimes|required',
                    'name'    => 'sometimes|required',
                    'code'    => 'sometimes|required|unique:apis,code'.($post->id != "new" ? ",".$post->id : ''),
                    'type' => ['sometimes', 'required', Rule::In(['recharge', 'bill', 'money', 'pancard', 'fund'])],
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $action = Api::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'operator':

                $rules = array(
                    'name'    => 'sometimes|required',
                    'recharge1'    => 'sometimes|required',
                    'recharge2'    => 'sometimes|required',
                    'type' => ['sometimes', 'required', Rule::In(['mobile','dth','electricity','pancard','dmt','aeps','fund','nsdlpan'])],
                    'api_id'    => 'sometimes|required|numeric',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $action = Provider::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'complaintsub':
                $rules = array(
                    'subject'    => 'sometimes|required',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $action = Complaintsubject::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'portalsetting':
                $rules = array(
                    'value'    => 'required',
                    'name'     => 'required',
                    'code'     => 'required',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = PortalSetting::updateOrCreate(['code'=> $post->code], $post->all());;
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'links':
                $rules = array(
                    'name'    => 'required',
                    'value'    => 'required|url',
                );
                
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = Link::updateOrCreate(['id'=> $post->id], $post->all());;
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;
            
            default:
                # code...
                break;
        }
    }
}
