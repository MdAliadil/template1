<?php

namespace App\Http\Middleware;

use Closure;

class TransactionActivity
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function handle($post, Closure $next, $type="none")
    {
        //dd($post->all());
        $ifsc = substr($post->ifsc, 0, 4);
        $ifsc2 = substr($post->ifsc2, 0, 4);
        $ifsc3 = substr($post->ifsc3, 0, 4);
       // dd($ifsc);
      /*  if($ifsc == "PYTM" || $ifsc =="AIRP" || $ifsc =="NSPB" ||$ifsc2 == "PYTM"|| $ifsc2 =="AIRP" || $ifsc2 =="NSPB" || $ifsc3 == "PYTM"|| $ifsc3=="AIRP"||$ifsc3 =="NSPB" ){
          return response()->json(['status'=>'Your bank A/c Not accepted','statuscode'=>'ERR','message'=> 'Your bank A/c Not accepted']);  
        } */
        //  $t = ($post->all());
        //   dd($t->old());
        $geodata   = geoip($post->ip());
        $log['ip'] = $post->ip();
        $log['user_agent']   = $post->server('HTTP_USER_AGENT');
        if(\Auth::check()){
            $log['user_id']  = \Auth::id();
        }else{
            $log['user_id']  = $post->user_id;
        }
        $log['geo_location'] = $geodata->lat."/".$geodata->lon;
        $log['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $log['oldpayload'] = '';
        $log['request'] = base64_encode(json_encode($post->all()));
        $log['parameters']   = $type;
      
        \DB::table('transaction_activitylogs')->insert($log);
        return $next($post);
    }
}
