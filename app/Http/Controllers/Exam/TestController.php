<?php

namespace App\Http\Controllers\Exam;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /** 登录*/
    public function login(){
        return view('exam.login');
    }
    public function doLogin(Request $request){
        $name = $request->input('name');
        $where = ['name' => $name];
        $res = UserModel::where($where)->first();
        if($res){
            if($request->input('pwd')==$res['pwd']){
                $token = substr(md5(time(),mt_rand(1,999)),10,10);
                setcookie('id',$res['id'],time()+60,'/','larvel.com',false,true);
                setcookie('token',$token,time()+60,'/','','false','true');

                $request->session()->put('u_token',$token);
                $request->session()->put('id',$res['id']);
                echo 'success';
                header("refresh:1;url='/exam/center'");
            }else{
                exit('密码错误');
            }
        }else{
            exit('此用户不存在');
        }
    }
    /** 修改密码*/
    public function updatePwd(){
        return view('exam.pwd');
    }
    public function doPwd(Request $request){
        $name = $request->input('name');
        $where = ['name' => $name];
        $res = UserModel::where($where)->first();
        if($res){
            if($request->input('pwd')!=$res['pwd']){
                echo 111;
                $pwd1 = $request->input('pwd1');
                $pwd2 = $request->input('pwd2');
                if($pwd1!=$pwd2){
                    exit('新密码与确认密码保持一致');
                }
                $dataInfo = ['pwd' => $pwd1];
                $res = UserModel::where($where)->update($dataInfo);
                if($res===false){
                    exit('修改失败');
                }else{
                    echo '修改成功,请重新登录';
                    header("refresh:1;url='/exam/login'");
                }
            }else{
                exit('此密码与原密码一致,不可修改');
            }
        }else{
            exit('此用户名不存在,无法修改');
        }
    }
    /** 中心*/
    public function center(Request $request){
        //echo $_COOKIE['id'];exit;
        if(!empty($_COOKIE['token'])){
            if($_COOKIE['token']!=$request->session()->get('u_token')){
                exit("非法请求");
            }else{
                echo '正常请求';
            }
        }
//		echo 'u_token: '.$request->session()->get('u_token'); echo '</br>';
        if(empty($_COOKIE['id'])){
            //echo $_COOKIE['id'];exit;
            echo '请先登录';
            header("refresh:2,url='/exam/login'");exit;
        }else{
            //echo 222;exit;
            $where = [
                'id' => $_COOKIE['id'],
            ];
            //var_dump($where);exit;
            $res = UserModel::where($where)->first();
            //print_r($res);exit;
            echo 'ID:'.$_COOKIE['id'].'欢迎回来';
        }
    }

}
