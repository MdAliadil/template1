<?php

namespace App\Http\Middleware;

use Closure;

class ApiCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if(\Request::is('api/*')){
           return response()->json(['statuscode'=>'ERR','status'=>'ERR','message'=> 'Invalid api base Url']); 
        }
        
        if(!\Request::is('api/getip') && !\Request::is('api/getbal/*') && !\Request::is('api/callback/*') && !\Request::is('api/virshan/callback/upi/*') && !\Request::is('api/upi/*') && !\Request::is('api/*') && !\Request::is('api/checkaeps/*') && !\Request::is('api/android/*')){
            if(!$post->has('token')){
                return response()->json(['statuscode'=>'ERR','status'=>'ERR','message'=> 'Invalid api token']);
            }
            
            
        }

        
        
        return $next($post);
    }
}