<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

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
        //$taken = $request->session()->get('u_token');
        $taken = $_COOKIE['token'];
        if(!$taken){
            header("refresh:2;url='http://passport.larvel.com/userlogin'");
            echo '请先登录';exit;
        }else{
            $id = $_COOKIE['id'];
            $token = Redis::get("str:u_token_key$id");
            if($_COOKIE['token']==$token){
                $login = 1;
            }else{
                header("refresh:2;url='http://passport.larvel.com/userlogin'");
                echo '信息已过期,请重新登录';exit;
            }
        }
        $request->attributes->add(['login'=>$login]);
        return $next($request);
    }
}
