<?php

namespace App\Http\Middleware;

use Closure;

class Activity
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function handle($post, Closure $next)
    {
        $geodata = geoip($post->ip());
        $log['ip'] = $post->ip();
        $log['user_agent'] = $post->server('HTTP_USER_AGENT');
        $log['user_id'] = $post->mobile;
        $log['geo_location'] = $geodata->lat."/".$geodata->lon;
        $log['parameters'] = json_encode($post->except(['_token', 'password']));

        \DB::table('login_activitylogs')->insert($log);
        return $next($post);
    }
}
