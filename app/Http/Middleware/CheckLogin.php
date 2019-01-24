<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $taken = $request->session()->get('u_token');
        if(!$taken){
            header("refresh:2;url='/userlogin'");
            echo '请先登录';exit;
        }
        return $next($request);
    }
}
