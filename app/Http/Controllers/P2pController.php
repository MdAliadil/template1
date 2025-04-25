<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\P2p;
use App\Model\Otpdata;


class P2pController extends Controller
{
    public function index($type,$id=null)
    {
         switch ($type) {
             case 'connectaccount':
                $permission = "upip2p_manager";
                if (!\Myhelper::can($permission)) {
                    abort(403);
                }
                break;
                
         }
         
         $data['type'] = $type;
         $data['id'] = $id??\Auth::id();
        return view("p2p.".$type)->with($data);
    }
    
    public function update(Request $post)
    {
        //$post['status'] ='active';
        $action = P2p::updateOrCreate(['id'=> $post->id], $post->all());
        if ($action) {
            return response()->json(['status' => "success"], 200);
        }else{
            return response()->json(['status' => "Task Failed, please try again"], 200);
        }
    }
    public function getOtps(Request $request,$id)
    {
        if ($request->ajax()) {
                $query = Otpdata::query()->where('midid',$id)->orderByDesc('created_at');
                
             
        
           if ($request->dateto && $request->datefrom) {
                    $dateTo = $request->dateto . ' 23:59:59';
                    $dateFrom = $request->datefrom . ' 00:00:00';
                    $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                } elseif ($request->datefrom) {
                    $dateFrom = $request->datefrom . ' 00:00:00';
                    $query->where('created_at', '>=', $dateFrom);
                } elseif ($request->dateto) {
                    $dateTo = $request->dateto . ' 23:59:59';
                    $query->where('created_at', '<=', $dateTo);
                }
            if($request->statustext){
               $query->where('status', $request->statustext); 
            }
            $query->when($request->agentid, function ($q) use ($request) {
                return $q->where('user_id', $request->agentid);
            });
    
           /* $query->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            });*/

            $query->when($request->searchtext, function ($q) use ($request) {
                $search = $request->searchtext;
                return $q->where(function ($sq) use ($search) {
                    $sq->where('txnid', 'LIKE', "%{$search}%")
                       ->orWhere('aadhar', 'LIKE', "%{$search}%")
                       ->orWhere('mobile', 'LIKE', "%{$search}%")
                       ->orWhere('refno', 'LIKE', "%{$search}%")
                       ->orWhere('txnid', 'LIKE', "%{$search}%")
                       ->orWhere('payid', 'LIKE', "%{$search}%")
                       ->orWhere('mytxnid', 'LIKE', "%{$search}%");
                });
            });
    
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $total = $query->count();
            //dd($query->toSql());
            $results = $query->offset($start)
                             ->limit($length)
                             ->get();
    
            return response()->json([
                'data' => $results,
                'draw' => $request->input('draw'),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
            ]);
        }
    }
}
