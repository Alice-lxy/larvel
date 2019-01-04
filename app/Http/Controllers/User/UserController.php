<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\UserModel;

class UserController extends Controller
{
    //

	public function user($uid)
	{
		echo $uid;
	}

	public function test()
    {
        echo '<pre>';print_r($_GET);echo '</pre>';
    }

	public function add()
	{
		$data = [
			'name'      => str_random(5),
			'age'       => mt_rand(20,99),
			'email'     => str_random(6) . '@gmail.com',
			'reg_time'  => time()
		];

		$id = UserModel::insertGetId($data);
		var_dump($id);
	}

	/** 注册*/
	public function reg(){
		return view('users.reg');
	}
	public function doReg(Request $request){
		/*echo __METHOD__;
		echo '<pre>';print_r($_POST);echo '</pre>';*/
		//exit;
		$pwd = md5($request->input('pwd'));
		$data = [
			'name' => $request->input('name'),
			'pwd' => $pwd,
			'age' => $request->input('age'),
			'email' => $request->input('email')
		];
		$id = UserModel::insert($data);
		//var_dump($id);
		if($id){
			echo '注册成功';
			header("refresh:1;'/userlogin'");
		}else{
			echo '注册失败';
		}
	}

    /** 登录*/
    public function login(){
        return view('users.login');
    }
	public function doLogin(Request $request){
		//echo __METHOD__;
        $name = $request->input('name');
        $pwd = md5($request->input('pwd'));
        $where = [
            'name' => $name,
            'pwd' => $pwd
        ];
        $res = UserModel::where($where)->first();
        if($res){
            echo '登录成功';
        }else{
            echo '登录失败';
        }
	}
}
