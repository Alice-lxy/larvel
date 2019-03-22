<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{
    public function index(){
//        echo '<pre>'; print_r($_SERVER);echo '</pre>';

        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        //echo $url;die;
        $data = [
            'login' =>  Redis::get('login'),
            'current_url'   =>  urlencode($url)
        ];
        return view('layout.bst',$data);
    }
}