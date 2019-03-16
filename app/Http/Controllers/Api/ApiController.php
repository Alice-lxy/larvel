<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    //
    public function test1(){
        echo '<pre>';print_r($_POST);echo '</pre>';
        echo '<pre>';print_r($_GET);echo '</pre>';
        echo '<pre>';print_r($_FILES);echo '</pre>';

    }
}
