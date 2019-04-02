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
        $data = [
            'name'  =>  $username,
            'password'  =>  $password
        ];
        $info = HBModel::insertGetId($data);
        var_dump($info);
    }
    public function test(){
        echo __METHOD__;
    }
}
