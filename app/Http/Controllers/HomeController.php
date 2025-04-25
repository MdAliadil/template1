<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Circle;
use App\User;
use App\Model\Report;
use App\Model\Aepsreport;
use App\Model\Api;
use App\Model\Upirorder;
use App\Model\Upireport;
use Carbon\Carbon;
use DataTables;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['checkcommission']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       
        
        
        if(!\Myhelper::getParents(\Auth::id())){
            session(['parentData' => \Myhelper::getParents(\Auth::id())]);
        }
        
       
        $data['state'] = Circle::all();
        $roles = ['whitelable', 'md', 'distributor', 'retailer', 'apiuser', 'other'];

        $data['bank_account'] = '';
      
        return view('dashboard/home')->with($data);
    }

    public function getbalance()
    {
        $data['apibalance'] = 0;
        $api = Api::where('code', 'recharge1')->first();
        $url = "http://login.securepayments.co.in/api/getbal/".$api->username;
        $result = \Myhelper::curl($url, "GET", "", [], "no");
        if(!$result['error'] && $result['response'] != ''){
            $response = json_decode($result['response']);
            if(isset($response->balance)){
                $data['apibalance'] = $response->balance;
            }
        }
        $data['downlinebalance'] = round(User::whereIn('id', array_diff(session('parentData'), array(\Auth::id())))->sum('mainwallet'), 2);
        $data['mainwallet'] = \Auth::user()->mainwallet;
        $data['microatmbalance'] = \Auth::user()->microatmbalance;
        $data['lockedamount'] = \Auth::user()->lockedamount;
        //dd($data);

        return response()->json($data);
    }
    
    public function getdatas(Request $post)
    {
       
        /*$fromDate = $post->formDate;
        $toDate = $post->toDate;
        
        $todayStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : Carbon::today()->startOfDay();
        $todayEnd = $toDate ? Carbon::parse($toDate)->endOfDay() : Carbon::today()->endOfDay();
        $thisMonthStart = $fromDate ? Carbon::parse($fromDate)->startOfMonth() : Carbon::now()->startOfMonth();
        $lastMonthStart = $fromDate ? Carbon::parse($fromDate)->subMonthNoOverflow()->startOfMonth() : Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $toDate ? Carbon::parse($toDate)->subMonthNoOverflow()->endOfMonth() : Carbon::now()->subMonthNoOverflow()->endOfMonth();

        */
        
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonthNoOverflow()->endOfMonth();
        
        $userIds = \Myhelper::getParents(\Auth::id());
       // dd([Carbon::today(), Carbon::today()->endOfDay()]);
       $results = \DB::table('upireports')
                ->selectRaw("
                    SUM(case when created_at >= ? and created_at < ? then amount else 0 end) as todayAmount,
                    SUM(case when created_at >= ? and created_at < ? then amount else 0 end) as thisMonthAmount,
                    SUM(case when created_at >= ? and created_at <= ? then amount else 0 end) as lastMonthAmount,
                    COUNT(case when created_at >= ? and created_at < ? then 1 else null end) as todayTxnCount,
                    COUNT(case when created_at >= ? and created_at < ? then 1 else null end) as thisMonthTxnCount,
                    COUNT(case when created_at >= ? and created_at <= ? then 1 else null end) as lastMonthTxnCount
                ")
                ->where('status', 'success')
                ->where('product', 'upicollect')
                ->whereIn('user_id', $userIds)
                ->addBinding([
                    Carbon::today()->startOfDay(), Carbon::today()->endOfDay(),
                    Carbon::now()->startOfMonth(), Carbon::now(), 
                    Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth(),
                    Carbon::today(), Carbon::today()->endOfDay(), 
                    Carbon::now()->startOfMonth(), Carbon::now(), 
                    Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth(), 
                ], 'select') 
                ->get();
        //dd([$todayStart, $todayEnd]);
       $topUsers = \DB::table('upireports')
                    ->join('users', 'upireports.user_id', '=', 'users.id')  // Joining with the users table on user_id
                    ->select('upireports.user_id', 'users.name', \DB::raw('SUM(upireports.amount) as total_amount'))  // Selecting user ID, user name, and sum of amount
                    ->where('upireports.status', 'success')  // Filtering records with status as 'success'
                    ->whereBetween('upireports.created_at', [$todayStart, $todayEnd])  // Filtering records within today's date range
                    ->where('product', 'upicollect')
                    ->groupBy('upireports.user_id', 'users.name')  // Grouping results by user_id and user name
                    ->orderBy('total_amount', 'desc')  // Ordering by the sum of amount in descending order
                    ->limit(10)  // Limiting results to top 10
                    ->get();
        
        $payoutresults = \DB::table('reports')
                ->selectRaw("
                    SUM(case when created_at >= ? and created_at < ? then amount else 0 end) as todayAmount,
                    SUM(case when created_at >= ? and created_at < ? then amount else 0 end) as thisMonthAmount,
                    SUM(case when created_at >= ? and created_at <= ? then amount else 0 end) as lastMonthAmount,
                    COUNT(case when created_at >= ? and created_at < ? then 1 else null end) as todayTxnCount,
                    COUNT(case when created_at >= ? and created_at < ? then 1 else null end) as thisMonthTxnCount,
                    COUNT(case when created_at >= ? and created_at <= ? then 1 else null end) as lastMonthTxnCount
                ")
                ->where('status', 'success')
                ->where('product', 'payout')
                ->whereIn('user_id', $userIds)
                ->addBinding([
                    Carbon::today(), Carbon::today()->endOfDay(),
                    Carbon::now()->startOfMonth(), Carbon::now(), 
                    Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth(),
                    Carbon::today(), Carbon::today()->endOfDay(), 
                    Carbon::now()->startOfMonth(), Carbon::now(), 
                    Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth(), 
                ], 'select') 
                ->get();
        //dd([$todayStart, $todayEnd]);
        $topPayoutUsers = \DB::table('reports')
                ->join('users', 'reports.user_id', '=', 'users.id') // Assuming 'id' is the primary key in 'users'
                ->select('reports.user_id', 'users.name', \DB::raw('SUM(reports.amount) as total_amount'))
                ->where('reports.status', 'success')
                ->where('reports.product', 'payout')
                ->whereBetween('reports.created_at', [$todayStart, $todayEnd])
                ->groupBy('reports.user_id', 'users.name') // Group by user_id and name to aggregate properly
                ->orderBy('total_amount', 'desc')
                ->limit(10)
                ->get();           
        $datas=[
            "payin"=>$results,
            "toppayinusers"=>$topUsers,
            "payout"=>$payoutresults,
            "toppayoutusers"=>$topPayoutUsers,
            ];
        return response()->json(["datas"=>$datas]);
    }
    
    
     public function getfiltreddatas(Request $post)
    {
       
        /*$fromDate = $post->formDate;
        $toDate = $post->toDate;
        
        $todayStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : Carbon::today()->startOfDay();
        $todayEnd = $toDate ? Carbon::parse($toDate)->endOfDay() : Carbon::today()->endOfDay();
        $thisMonthStart = $fromDate ? Carbon::parse($fromDate)->startOfMonth() : Carbon::now()->startOfMonth();
        $lastMonthStart = $fromDate ? Carbon::parse($fromDate)->subMonthNoOverflow()->startOfMonth() : Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $toDate ? Carbon::parse($toDate)->subMonthNoOverflow()->endOfMonth() : Carbon::now()->subMonthNoOverflow()->endOfMonth();

        */
        
        $todayStart = Carbon::parse($post->input('fromDate'))->startOfDay();
        $todayEnd = Carbon::parse($post->input('toDate'))->endOfDay();
        $thisMonthStart = Carbon::parse($post->input('fromDate'))->startOfMonth();
        $lastMonthStart = Carbon::parse($post->input('fromDate'))->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = Carbon::parse($post->input('fromDate'))->subMonthNoOverflow()->endOfMonth();
        
        $userIds = \Myhelper::getParents(\Auth::id());
       // dd([Carbon::today(), Carbon::today()->endOfDay()]);
       $results = \DB::table('upireports')
                ->selectRaw("
                    SUM(case when created_at >= ? and created_at < ? then amount else 0 end) as todayAmount,
                    SUM(case when created_at >= ? and created_at < ? then amount else 0 end) as thisMonthAmount,
                    SUM(case when created_at >= ? and created_at <= ? then amount else 0 end) as lastMonthAmount,
                    COUNT(case when created_at >= ? and created_at < ? then 1 else null end) as todayTxnCount,
                    COUNT(case when created_at >= ? and created_at < ? then 1 else null end) as thisMonthTxnCount,
                    COUNT(case when created_at >= ? and created_at <= ? then 1 else null end) as lastMonthTxnCount
                ")
                ->where('status', 'success')
                ->where('product', 'upicollect')
                ->whereIn('user_id', $userIds)
                ->addBinding([
                    Carbon::parse($post->input('fromDate'))->startOfDay(), Carbon::parse($post->input('toDate'))->endOfDay(),
                    Carbon::parse($post->input('fromDate'))->startOfMonth(), Carbon::parse($post->input('toDate')), 
                    Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth(),
                    Carbon::parse($post->input('fromDate')), Carbon::parse($post->input('toDate'))->endOfDay(), 
                    Carbon::now()->startOfMonth(), Carbon::now(), 
                    Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth(), 
                ], 'select') 
                ->get();
        //dd([$todayStart, $todayEnd]);
       $topUsers = \DB::table('upireports')
                    ->join('users', 'upireports.user_id', '=', 'users.id')  // Joining with the users table on user_id
                    ->select('upireports.user_id', 'users.name', \DB::raw('SUM(upireports.amount) as total_amount'))  // Selecting user ID, user name, and sum of amount
                    ->where('upireports.status', 'success')  // Filtering records with status as 'success'
                    ->whereBetween('upireports.created_at', [$todayStart, $todayEnd])  // Filtering records within today's date range
                    ->where('product', 'upicollect')
                    ->groupBy('upireports.user_id', 'users.name')  // Grouping results by user_id and user name
                    ->orderBy('total_amount', 'desc')  // Ordering by the sum of amount in descending order
                    ->limit(10)  // Limiting results to top 10
                    ->get();
        
        $payoutresults = \DB::table('reports')
                ->selectRaw("
                    SUM(case when created_at >= ? and created_at < ? then amount else 0 end) as todayAmount,
                    SUM(case when created_at >= ? and created_at < ? then amount else 0 end) as thisMonthAmount,
                    SUM(case when created_at >= ? and created_at <= ? then amount else 0 end) as lastMonthAmount,
                    COUNT(case when created_at >= ? and created_at < ? then 1 else null end) as todayTxnCount,
                    COUNT(case when created_at >= ? and created_at < ? then 1 else null end) as thisMonthTxnCount,
                    COUNT(case when created_at >= ? and created_at <= ? then 1 else null end) as lastMonthTxnCount
                ")
                ->where('status', 'success')
                ->where('product', 'payout')
                ->whereIn('user_id', $userIds)
                ->addBinding([
                    Carbon::parse($post->input('fromDate'))->startOfDay(), Carbon::parse($post->input('toDate'))->endOfDay(),
                    Carbon::parse($post->input('fromDate'))->startOfMonth(), Carbon::parse($post->input('toDate')), 
                    Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth(),
                    Carbon::parse($post->input('fromDate')), Carbon::parse($post->input('toDate'))->endOfDay(), 
                    Carbon::now()->startOfMonth(), Carbon::now(), 
                    Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth(),  
                ], 'select') 
                ->get();
        //dd([$todayStart, $todayEnd]);
        $topPayoutUsers = \DB::table('reports')
                ->join('users', 'reports.user_id', '=', 'users.id') // Assuming 'id' is the primary key in 'users'
                ->select('reports.user_id', 'users.name', \DB::raw('SUM(reports.amount) as total_amount'))
                ->where('reports.status', 'success')
                ->where('reports.product', 'payout')
                ->whereBetween('reports.created_at', [$todayStart, $todayEnd])
                ->groupBy('reports.user_id', 'users.name') // Group by user_id and name to aggregate properly
                ->orderBy('total_amount', 'desc')
                ->limit(10)
                ->get();           
        $datas=[
            "payin"=>$results,
            "toppayinusers"=>$topUsers,
            "payout"=>$payoutresults,
            "toppayoutusers"=>$topPayoutUsers,
            ];
        return response()->json(["datas"=>$datas]);
    }

    public function getmysendip()
    {
        $url = "http://login.securepayments.co.in/api/getip";
        $result = \Myhelper::curl($url, "GET", "", [], "no");
        dd($result);
    }

    public function setpermissions()
    {
        $users = User::whereHas('role', function($q){ $q->where('slug', '!=' ,'admin'); })->get();

        foreach ($users as $user) {
            $inserts = [];
            $insert = [];
            $permissions = \DB::table('default_permissions')->where('type', 'permission')->where('role_id', $user->role_id)->get();

            if(sizeof($permissions) > 0){
                \DB::table('user_permissions')->where('user_id', $user->id)->delete();
                foreach ($permissions as $permission) {
                    $insert = array('user_id'=> $user->id , 'permission_id'=> $permission->permission_id);
                    $inserts[] = $insert;
                }
                \DB::table('user_permissions')->insert($inserts);
            }
        }
    }

    public function setscheme()
    {
        // $users = User::whereHas('role', function($q){ $q->where('slug', '!=' ,'admin'); })->get();

        // foreach ($users as $user) {
        //     $inserts = [];
        //     $insert = [];
        //     $scheme = \DB::table('default_permissions')->where('type', 'scheme')->where('role_id', $user->role_id)->first();
        //     if ($scheme) {
        //         User::where('id', $user->id)->update(['scheme_id' => $scheme->permission_id]);
        //     }
        // }

        $bcids = App\Model\Mahaagent::get(['phone1', 'id']);

        foreach ($bcids as $user) {
            $userdata = User::where('mobile', $user->phone1)->first(['id']);
            if($userdata){
                App\Model\Mahaagent::where('id', $user->id)->update(['user_id' => $userdata->id]);
            }
        }
    }

    public function mydata()
    {
             
    }

    public function bulkSms()
    {
        $content = "Welcome to Webtalk, Username-9971702408,Password-12345678, Web: http://b2b.webtalkatmmini.com/, App: http://bit.ly/webtalkapplication Thanks-Webtalk Team";
        \Myhelper::sms("9971702308", $content);    
        // $user = User::get(['id', 'mobile']);

        // foreach ($user as $value) {
        //     $content = "Welcome to Webtalk, Username-".$user->mobile.",Password-12345678, Web: http://b2b.webtalkatmmini.com/, App: http://bit.ly/webtalkapplication Thanks-Webtalk Team";
        //     \Myhelper::sms("9971702308", $content);        
        // }   
    }

    public function checkcommission(Request $post)
    {
        // $total = "6000";

        // $amount = $total;
        // for ($i=1; $i < 6; $i++) { 
        //     if(5000*($i-1) <= $amount  && $amount <= 5000*$i){
        //         if($amount == 5000*$i){
        //             $n = $i;
        //         }else{
        //             $n = $i-1;
        //             $x = $amount - $n*5000;
        //         }
        //         break;
        //     }
        // }

        // $amounts = array_fill(0,$n,5000);
        // if(isset($x)){
        //     array_push($amounts , $x);
        // }

        // //dd($amounts);

        // foreach($amounts as $value){
        //     echo $value."<br>";
        //     continue;
        //     echo "total - ".$total."<br>";
        //     $total = $total - $value;
        // }

        $report = \App\Model\Report::where('id', "40969")->first();
    }
    
    public function getUpiOrders(Request $request)
    {
        if ($request->ajax()) {
                $query = Upirorder::query()->where('status', 'initiated')->orderByDesc('created_at');
                if (\Auth::user()->role->slug != "admin" && \Auth::user()->role->slug != "employee") {
                    $query->where('user_id', \Auth::id());
            }
        
           if ($request->dateto) {
                $dateTo = $request->dateto . ' 23:59:59';
                $query->where('created_at', '<=', $dateTo);
                
                $dateFrom = $request->datefrom . ' 00:00:00';
                $query->where('created_at', '>=', $dateFrom);
            }
            $query->when($request->agentid, function ($q) use ($request) {
                return $q->where('user_id', $request->agentid);
            });
    
            $query->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            });

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
    
    public function getUpiOrdersSuccess(Request $request)
    {
        if ($request->ajax()) {
                $query = Upireport::query()->orderByDesc('created_at');
                //dd(\Auth::user()->role->slug);
                if ((\Auth::user()->role->slug != "admin") && (\Auth::user()->role->slug != "employee")) {
                   //dd("ghghgh");
                    $query->where('user_id', \Auth::id());
            }
        
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