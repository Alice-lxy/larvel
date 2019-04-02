<?php

namespace App\Http\Controllers\Exam;

use App\Model\HBModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    //login
    public function login(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $res = HBModel::where(['name'=>$username])->first()->toArray();
        if($res){
            //todo 有用户
            if($res['password']==$password){
                //ok
                $token = substr(md5(time().rand(111,999)),5,10);
                $response = [
                    'error' =>  0,
                    'msg'   =>  'ok',
                    'uid'   =>  $res['id'],
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
    public function test(){
        echo __METHOD__;
    }
}
