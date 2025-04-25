<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Securedata;
use App\User;

class MobilelogoutController extends Controller
{
    public function index()
    {
        
        $data = \DB::table('securedatas')
                ->join('users','securedatas.user_id','users.id')
                ->select('securedatas.*','users.mobile')
                ->get();
        //dd($data);
        return view('token',compact('data'));
    }

   

    public function tokenDelete(Request $post)
    {
        $delete = Securedata::where('id', $post->id)->delete();
        return response()->json(['status'=>$delete], 200);
    }
}
