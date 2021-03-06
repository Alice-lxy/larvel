<?php

namespace App\Http\Controllers\Test;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class TestController extends Controller
{
    //
    public function abc()
    {
        var_dump($_POST);echo '</br>';
        var_dump($_GET);echo '</br>';
    }

	public function world1()
	{
		echo __METHOD__;
	}


	public function hello2()
	{
		echo __METHOD__;
		header('Location:/world2');
	}

	public function world2()
	{
		echo __METHOD__;
	}

	public function md($m,$d)
	{
		echo 'm: '.$m;echo '<br>';
		echo 'd: '.$d;echo '<br>';
	}

	public function showName($name=null)
	{
		var_dump($name);
	}

	public function query1()
	{
		$list = DB::table('p_users')->get()->toArray();
		echo '<pre>';print_r($list);echo '</pre>';
	}

	public function query2()
	{
		$user = DB::table('p_users')->where('uid', 3)->first();
		echo '<pre>';print_r($user);echo '</pre>';echo '<hr>';
		$email = DB::table('p_users')->where('uid', 4)->value('email');
		var_dump($email);echo '<hr>';
		$info = DB::table('p_users')->pluck('age', 'name')->toArray();
		echo '<pre>';print_r($info);echo '</pre>';
	}

	public function viewChild(){
		$list = UserModel::all()->toArray();

		$data = [
			'title' => '巅峰币',
			'list' => $list
		];
		return view('test.child',$data);
	}

	/**	 中间件测试*/
	public function checkCookie(){
		echo __METHOD__;
	}

	/* *聊天 */
	public function view()
	{
		if (empty($_POST)) {
			return view('test.test');
		} else {
			print_r($_POST);
			echo '<br/>';
		}
	}
	public function chat(){
		return view('test.chat');
	}
	public function dochat(){
		echo __METHOD__;
	}
	}