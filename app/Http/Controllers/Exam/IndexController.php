<?php

namespace App\Http\Controllers\Exam;

use App\Model\Exam;
use App\Model\HBModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{
    //B卷
    //电脑端
    public function loginlist(){
//        echo __METHOD__;DIE
        return view('exam.login');
    }
    public function doLogin(){
        $username = $_POST['username'];
        $password =md5($_POST['pwd']);
       // echo $name.$password;die;
        $arr = HBModel::where(['name'=>$username])->first();
        if($arr){
            if($password==$arr['password']){
                $token = substr(md5(time().rand(111,999)),5,10);

                $id = $arr['id'];
                $redis_pc_token_key = "str:pc_key_token".$id;
                Redis::del($redis_pc_token_key);
                Redis::set($redis_pc_token_key,$token);//存
                Redis::expire($redis_pc_token_key,3600);//过期时间 1小时
            }else{
                exit('please try again');
            }
        }else{
            exit('account not found');
        }
    }
    //手机端login
    public function login(){
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $res = HBModel::where(['name'=>$username])->first();
        if($res){
            //todo 有用户
            if($res['password']==$password){
                //ok
                $token = substr(md5(time().rand(111,999)),5,10);

                $id = $res['id'];
                $redis_token_key = "str:exam_key_token".$id;
                Redis::del($redis_token_key);
                Redis::set($redis_token_key,$token);//存
                Redis::expire($redis_token_key,3600);//过期时间 1小时
                //$a = Redis::ttl($redis_token_key);

                $response = [
                    'error' =>  0,
                    'msg'   =>  'ok',
                    'uid'   =>  $id,
                    'token' =>  $token
                ];
            }else{
                //密码错误
                $response = [
                    'error' =>  302,
                    'msg'   =>  'please try again',
                ];
            }
        }else{
            //TODO 无用户
            $response = [
                'error' =>  301,
                'mag'   =>  'this account not found',
            ];
        }
        echo json_encode($response);
    }
    //获取token
    public function token(){
        $token = $_POST['token'];
        $uid = $_POST['uid'];
        $redis_token_key = "str:exam_key_token".$uid;
        $new_token = Redis::get($redis_token_key);
        if($token==$new_token){
            echo json_encode(1);
        }else{
            echo json_encode(2);
        }
    }


    /*用户列表展示*/
    public function userlist(){
        $arr = HBModel::get()->toArray();

        echo json_encode($arr);
    }

    //A卷
    public function apply(){
        return view('exam.apply');
    }
    public function applylist(){
        $name = $_POST['name'];
        $card = $_POST['card'];
        $pic = $_FILES['picture'];
        $picture = $pic['tmp_name'];
        $api = $_POST['api'];
        $info = Exam::where(['card'=> $card])->first();
        if($info){
            $num = $info['app_num'];
            //print_r($num);
            $new_num = $num+1;
            $arr = Exam::where(['card'=>$card])->update(['app_num'=>$new_num]);
        }else{
            $data = [
                'name'  =>  $name,
                'card'  =>  $card,
                'picture'   =>  $picture,
                'api'   =>  $api
            ];
            $arr =Exam::insertGetId($data);
        }
        if($arr){
            return view('exam.wait');
        }else{
            exit('申请失败');
        }
    }
}
