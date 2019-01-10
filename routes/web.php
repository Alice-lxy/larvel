<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    echo date('Y-m-d H:i:s');
    return view('welcome');
});

Route::get('/adduser','User\UserController@add');

//路由跳转
Route::redirect('/hello1','/world1',301);
Route::get('/world1','Test\TestController@world1');

Route::get('hello2','Test\TestController@hello2');
Route::get('world2','Test\TestController@world2');


//路由参数
Route::get('/user/test','User\UserController@test');
Route::get('/user/{uid}','User\UserController@user');
Route::get('/month/{m}/date/{d}','Test\TestController@md');
Route::get('/name/{str?}','Test\TestController@showName');



// View视图路由
Route::view('/mvc','mvc');
Route::view('/error','error',['code'=>40300]);


// Query Builder
Route::get('/query/get','Test\TestController@query1');
Route::get('/query/where','Test\TestController@query2');


//Route::match(['get','post'],'/test/abc','Test\TestController@abc');
Route::any('/test/abc','Test\TestController@abc');

/*视图层的test*/
Route::get('/view/child','Test\TestController@viewChild');

/** 注册*/
Route::get('/userreg','User\UserController@reg');
Route::post('/userreg','User\UserController@doReg');
/** 登录*/
Route::get('/userlogin','User\UserController@login');
Route::post('/userlogin','User\UserController@doLogin');
Route::get('/usercenter','User\UserController@center');//

/** 模板引入静态文件*/
Route::get('/mvc/bst','Mvc\MvcController@bst');

/**  Test*/
Route::get('/test/check_cookie','Test\TestController@checkCookie')->middleware('check.cookie');//中间件测试

/** 购物车*/
Route::get('/cart','Cart\IndexController@index')->middleware('check.login');
Route::get('/cart/add/{goods_id}','Cart\IndexController@addGoods')->middleware('check.login');//添加商品进入购物车
Route::post('/cart/add2','Cart\IndexController@add2')->middleware('check.login');//添加商品进入购物车
Route::get('cart/del/{goods_id}','Cart\IndexController@delGoods')->middleware('check.login');//删除购物车内的商品
Route::get('cart/del2/{goods_id}','Cart\IndexController@del2')->middleware('check.login');//删除购物车内的商品

Route::get('/goods','Goods\IndexController@index');//商品展示
Route::get('/goods/detail/{goods_id}','Goods\IndexController@detail');//商品详情

Route::get('/order','Order\IndexController@index')->middleware('check.login');//结算
Route::get('/order/add','Order\IndexController@add')->middleware('check.login');//结算
Route::get('/order/list','Order\IndexController@orderlist')->middleware('check.login');//订单详情
