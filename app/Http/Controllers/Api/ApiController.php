<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    //
    public function test1(){
        /*echo '<pre>';print_r($_POST);echo '</pre>';
        echo '<pre>';print_r($_GET);echo '</pre>';
        echo '<pre>';print_r($_FILES);echo '</pre>';*/
        return view('api.test');
    }
    public function int(){
//      echo '<pre>';print_r($_POST);echo '</pre>';
        $data = $_POST['post_data'];
        $iv = $_POST['iv'];
        $key = $_POST['key'];
        $method = $_POST['method'];
        //解密
        $info = base64_decode($data);
        $dec_str = openssl_decrypt($info,$method,$key,OPENSSL_RAW_DATA,$iv);
        $a = json_decode($dec_str);
//        var_dump($a);die;
        if(!empty($a)){
            $response = [
                'error'   =>  0,
                'msg'   =>  'ok',
                'data'  =>  'this is a screat'
            ];
//            print_r($response);die;

            $time = time();
            $iv1 = substr(md5($time.'salt'),2,16);
//            echo $iv1;echo '<br/>';
            $new1 = json_encode($response);
            //echo $new1;echo '<hr/>';
            $new_str = openssl_encrypt($new1,$method,$key,OPENSSL_RAW_DATA,$iv1);
            //print_r($new_str);echo '<hr/>';
            $arr = [
                'iv'    => $iv1,
                't'     => $time,
                'new_str'=> base64_encode($new_str),
            ];
            echo json_encode($arr);
        }
    }
}
