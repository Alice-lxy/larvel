<?php

namespace App\Http\Controllers\Api;

use App\Model\ApiUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    //接口测试
    public function test1(){
        /*echo '<pre>';print_r($_POST);echo '</pre>';
        echo '<pre>';print_r($_GET);echo '</pre>';
        echo '<pre>';print_r($_FILES);echo '</pre>';*/
        return view('api.test');
    }
    //
    public function int(){
      echo '<pre>';print_r($_POST);echo '</pre>';
        $data = $_POST['post_data'];
        $iv = $_POST['iv'];
        $key = $_POST['key'];
        $method = $_POST['method'];
        //验签
        $sign = base64_decode($_POST['sign']);
        //echo $sign;
        //加载公钥
        $pub_key = openssl_pkey_get_public(file_get_contents('./key/openssl_pub.key'));
        //验签
        $verify = openssl_verify($data,$sign,$pub_key,OPENSSL_ALGO_SHA1);
//        var_dump($verify);echo '<br/>';
        if($verify){
            echo '验签success';echo '<br/>';
        }

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
    //验签
    public function openssl(){
       // echo '<pre>';print_r($_POST);echo '</pre>';
        $sign = base64_decode($_POST['sign']);
        $openssl_data = 'this is a test';
        //加载公钥
        $pub_key = openssl_pkey_get_public(file_get_contents('./key/openssl_pub.key'));
        //print_r(file_get_contents('./key/openssl_pub.key'));
       // var_dump($pub_key);
        //生成摘要
        $digestAlgo = 'sha512';
        $digest = openssl_digest($openssl_data,$digestAlgo);
        //验签
        $verify = openssl_verify($digest,$sign,$pub_key,OPENSSL_ALGO_SHA1);
        if($verify){
            //error
            echo 'success';
        }
    }

    //HBuilder 接口测试
    public function hb(){
        echo json_encode($_POST);
    }
    //reg
    public function reg(){
        return view('api.reg');
    }
    //login
    public function login(){
        echo md5('12138');
        return view('api.login');
    }
    public function dologin(){
        echo '<pre>';print_r($_POST);echo '</pre>';//_token  name  pwd
        $name = $_POST['name'];
        $pwd = $_POST['pwd'];
        $data = ApiUser::where(['name'=>$name])->first()->toArray();
        print_r($data);die;
    }
}
