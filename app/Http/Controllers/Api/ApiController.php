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

    /**
     *接口API
     */
    public function reg(){
        $name = $_POST['name'];
        $pwd = $_POST['pwd'];
        $email = $_POST['email'];
        $data = [
            'name'  =>  $name,
            'pwd'   =>  $pwd,
            'email' =>  $email
        ];
        $ch = curl_init();
        $url = 'http://pslxy.miao629.com/user/reg';
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $res = curl_exec($ch);
        return $res;

    }
    public function login(){
       //echo json_encode($_POST);die;
        $name = $_POST['name'];
        $pwd = $_POST['pwd'];
        $data = [
            'name'  => $name,
            'pwd'   => $pwd,
        ];

        $url = "http://pslxy.miao629.com/user/login";

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);


        $res = curl_exec($ch);

        return $res;
       // print_r($res);


        //echo $pwd;die;
        //$data = ApiUser::where(['name'=>$name])->first();
        //print_r($data);die;
        /*if(!$data){
            $data = [
                'error' =>  504,
                'msg'   =>  '此用户不存在!'
            ];
        }else{
            if($pwd!=$data['pwd']){
                $data = [
                    'error' => 500,
                    'msg'   =>  '用户名或密码错误'
                ];
            }else{
                $data = [
                    'error' => 200,
                    'mag'   =>  'ok'
                ];
            }
        }
        echo json_encode($data);*/



    }
    public function token(){
//        echo json_encode($_POST);
        $token = $_POST['token'];
        $id = $_POST['id'];
        $data = [
            'token' => $token,
            'id'    =>  $id
        ];
        $url = "http://pslxy.miao629.com/user/token";

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        $res = curl_exec($ch);
        return $res;
    }
    public function quit(){
        //print_r($_POST);die;
        $id = $_POST['id'];
        $data = [
            'id'    =>  $id
        ];
        $url = "http://pslxy.miao629.com/user/quit";

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);//路由
        curl_setopt($ch,CURLOPT_HEADER,0);//头信息
        curl_setopt($ch,CURLOPT_POST,1);//post方式
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//发送数据
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//转化成json形式

        $arr = curl_exec($ch);
        return $arr;
    }
}
