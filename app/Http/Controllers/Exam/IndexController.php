<?php

namespace App\Http\Controllers\Exam;

use App\Model\HBModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{
    //login
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
                Redis::set($redis_token_key,$token);
                $last_time = Redis::expire($redis_token_key,300);
                //$a = Redis::ttl($redis_token_key);

                $response = [
                    'error' =>  0,
                    'msg'   =>  'ok',
                    'uid'   =>  $id,
                    'time'  =>  $last_time,
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
            return 1;
        }else{
            return 2;
        }
    }


    /*用户列表展示*/
    public function userlist(){
        $arr = HBModel::get()->toArray();

        echo json_encode($arr);
    }
}
