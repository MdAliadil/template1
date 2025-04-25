<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Model\Circle;
use App\Model\Role;

class SettingController extends Controller
{
    public function index(Request $get, $id=0)
    {
        $data = [];
        $data['tab'] = $get->tab;
        if($id != 0){
            $data['user'] = User::find($id);
        }else{
            $data['user'] = \Auth::user();
        }

        if(\Myhelper::hasRole('admin')){
            $data['parents'] = User::whereHas('role', function ($q){
                $q->where('slug', '!=', 'retailer');
            })->get(['id', 'name', 'role_id', 'mobile']);

            $data['roles']   = Role::where('slug' , '!=' , 'admin')->get();
        }else{
            $data['parents'] = [];
            $data['roles']   = [];
        }

        $data['state'] = Circle::all(['state']);
        return view('profile.index')->with($data);
    }

    public function certificate()
    {
        return view('certificate');
    }

    public function profileUpdate(\App\Http\Requests\Member $post)
    {
        $dtata=session('parentData',[]);
        if(\Myhelper::hasNotRole('admin') && (\Auth::id() != $post->id) && !in_array($post->id,  $dtata)){
            return response()->json(['status' => "Permission Not Alloweds"], 400);
        }

        switch ($post->actiontype) {
            case 'password':
                /*if(!\Myhelper::hasNotRole('admin') || ($post->id != \Auth::id())){
                    return response()->json(['status' => "Permission Not Allowed1"], 400);
                }*/

                /*if(($post->id == \Auth::id()) ){
                    return response()->json(['status' => "Permission Not Allowed"], 400);
                }*/

                if(\Myhelper::hasNotRole('admin')){
                    $credentials = [
                        'mobile' => \Auth::user()->mobile,
                        'password' => $post->oldpassword
                    ];
            
                    if(!\Auth::validate($credentials)){
                        return response()->json(['errors' =>  ['oldpassword'=>'Please enter corret old password']], 422);
                    }
                }

                // $post['passwordold'] = $post->password;
                $post['password'] = bcrypt($post->password);
                $post['resetpwd'] = "changed";

                break;
            
            case 'profile':
                if(!\Myhelper::hasRole('admin')){
                    return response()->json(['status' => "Permission Not Allowed"], 400);
                }

                /*if(\Myhelper::hasRole('admin') || ($post->id == \Auth::id()) && !\Myhelper::can('profile_edit')){
                    return response()->json(['status' => "Permission Not Allowed3"], 400);
                }*/
                break;
            case 'updatemerchantcharge' :
                $post['updatemerchantcharge'] = $post->upicharge;
            break ;    
            
            case 'wstock' :
            case 'mstock' :
            case 'dstock' :
            case 'rstock' :
                if(!\Myhelper::can('member_stock_manager')){
                    return response()->json(['status' => "Permission Not Allowed"], 400);
                }

                if(\Myhelper::hasNotRole(['admin'])){
                    if($post->wstock > 0 && \Auth::user()->wstock < $post->wstock){
                        return response()->json(['status'=>'Low id stock'], 400);
                    }
                    
                    if($post->mstock > 0 && \Auth::user()->mstock < $post->mstock){
                        return response()->json(['status'=>'Low id stock'], 400);
                    }

                    if($post->dstock > 0 && \Auth::user()->dstock < $post->dstock){
                        return response()->json(['status'=>'Low id stock'], 400);
                    }
        
                    if($post->rstock > 0 && \Auth::user()->rstock < $post->rstock){
                        return response()->json(['status'=>'Low id stock'], 400);
                    }
                }
                
                if($post->wstock != ''){
                    User::where('id', \Auth::id())->decrement('wstock', $post->wstock);
                    $response = User::where('id', $post->id)->increment('wstock', $post->wstock);
                }
                
                if($post->mstock != ''){
                    User::where('id', \Auth::id())->decrement('mstock', $post->mstock);
                    $response = User::where('id', $post->id)->increment('mstock', $post->mstock);
                }

                if($post->dstock != ''){
                    User::where('id', \Auth::id())->decrement('dstock', $post->dstock);
                    $response = User::where('id', $post->id)->increment('dstock', $post->dstock);
                }

                if($post->rstock != ''){
                    User::where('id', \Auth::id())->decrement('rstock', $post->rstock);
                    $response = User::where('id', $post->id)->increment('rstock', $post->rstock);
                }

                if($response){
                    return response()->json(['status'=>'success'], 200);
                }else{
                    return response()->json(['status'=>'fail'], 400);
                }

                break;

            case 'bankdata':
                if(\Myhelper::hasNotRole('admin')){
                    return response()->json(['status' => "Permission Not Allowed"], 400);
                }
                break;

            case 'mapping':
                if(\Myhelper::hasNotRole('admin')){
                    return response()->json(['status' => "Permission Not Allowed"], 400);
                }
                $user = User::find($post->id);
                $parent = User::find($post->parent_id);

                if($parent->role->slug == "retailer"){
                    return response()->json(['status' => "Invalid mapping member"], 400);
                }

                switch ($user->role->slug) {
                    case 'retailer':
                        $roles = Role::where('id', $parent->role_id)->whereIn('slug', ['admin','distributor', 'md', 'whitelable'])->count();
                        break;

                    case 'distributor':
                        $roles = Role::where('id', $parent->role_id)->whereIn('slug', ['admin','md', 'whitelable'])->count();
                        break;
                    
                    case 'md':
                        $roles = Role::where('id', $parent->role_id)->whereIn('slug', ['admin','whitelable'])->count();
                        break;

                    case 'reseller':
                        //$roles = Role::where('id', $parent->role_id)->whereIn('slug', ['admin','distributor', 'md', 'whitelable'])->count();
                        return response()->json(['status' => "Invalid mapping member"], 400);
                        break;
                    
                    case 'whitelable':
                       $roles = Role::where('id', $parent->role_id)->whereIn('slug', ['admin','distributor','reseller', 'md', 'whitelable'])->count();
                       // return response()->json(['status' => "Invalid mapping membe2r"], 400);
                        break;
                }

                if(!$roles){
                    return response()->json(['status' => "Invalid mapping member"], 400);
                }
                break;

            case 'rolemanager':
                if(\Myhelper::hasNotRole('admin')){
                    return response()->json(['status' => "Permission Not Allowed"], 400);
                }

                $roles = Role::where('id', $post->role_id)->where('slug', 'admin')->count();
                if($roles){
                    return response()->json(['status' => "Invalid member role"], 400);
                }

                $user = User::find($post->id);
                switch ($user->role->slug) {
                    case 'retailer':
                        $roles = Role::where('id', $post->role_id)->whereIn('slug', ['distributor', 'md', 'whitelable','apiuser'])->count();
                        break;

                    case 'distributor':
                        $roles = Role::where('id', $post->role_id)->whereIn('slug', ['md', 'whitelable'])->count();
                        break;
                    
                    case 'md':
                        $roles = Role::where('id', $post->role_id)->whereIn('slug', ['whitelable'])->count();
                        break;

                    case 'whitelable':
                        return response()->json(['status' => "Invalid member role"], 400);
                        break;
                }

                if(!$roles){
                    return response()->json(['status' => "Invalid member role"], 400);
                }
                break;

            case 'scheme':
                if($this->schememanager() == "admin" && \Myhelper::hasNotRole('admin')){
                    return response()->json(['status' => "Permission Not Allowed"], 400);
                }

                if($this->schememanager() == "all" && \Myhelper::hasRole('retailer')){
                    return response()->json(['status' => "Permission Not Allowed"], 400);
                }

                if($this->schememanager() == "admin"){
                    $users = \Myhelper::getParents($post->id);
                    User::whereIn('id', $users)->where('id', '!=', $post->id)->update(['scheme_id' => $post->scheme_id]);
                }
                break;
        }

        $response = User::where('id', $post->id)->updateOrCreate(['id'=> $post->id], $post->all());
        if($response){
            return response()->json(['status'=>'success'], 200);
        }else{
            return response()->json(['status'=>'fail'], 400);
        }
    }
}