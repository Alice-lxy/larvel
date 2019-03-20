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
Route::get('/usercenter','User\UserController@center')->middleware('check.login');//
/** 退出*/
Route::get('/userquit','User\UserController@quit');

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
Route::get('/goods/detail/{goods_id}','Goods\IndexController@detail')->middleware('check.login');//商品详情

Route::get('/order','Order\IndexController@index')->middleware('check.login');//结算
Route::get('/order/add','Order\IndexController@add')->middleware('check.login');//结算
Route::get('/order/list','Order\IndexController@orderlist')->middleware('check.login');//订单详情
Route::get('/order/del/{order_number}','Order\IndexController@del')->middleware('check.login');//删除订单

Route::get('/pay/{order_number}','Pay\IndexController@pay')->middleware('check.login');//支付订单

Route::get('/pay','Pay\IndexController@pay1');

Route::get('/pay/alipay/test/{order_number}','Pay\AlipayController@test')->middleware('check.login');
//Route::get('/pay/{id}','Pay\AlipayController@pay')->middleware('check.login');
Route::post('/pay/alipay/notify','Pay\AlipayController@aliNotify');        //支付宝支付 通知回调
Route::get('/pay/alipay/return','Pay\AlipayController@aliReturn');        //支付宝支付 同步通知回调

Route::get('/pay/alipay/orderdel','Pay\AlipayController@orderDel');

///上传文件
Route::get('/upload','Goods\IndexController@uploadIndex');
Route::post('/upload/pdf','Goods\IndexController@uploadPDF');
//微信
Route::get('/weixin/test','Weixin\WeixinController@test');
Route::get('/weixin/valid','Weixin\WeixinController@validToken');
Route::get('/weixin/valid1','Weixin\WeixinController@validToken1');
Route::post('/weixin/valid1','Weixin\WeixinController@wxEvent');        //接收微信服务器事件推送
Route::post('/weixin/valid','Weixin\WeixinController@validToken');
//创建菜单
Route::get('/weixin/create_menu','Weixin\WeixinController@createMenu');
Route::get('/weixin/refresh','Weixin\WeixinController@refreshToken');//刷新access_token
//微信支付
Route::get('/weixin/notice/test/{order_number}','Weixin\PayController@test');
Route::get('/view/{url}','Weixin\PayController@url');//二维码
Route::post('/weixin/pay/notice','Weixin\PayController@notice');//微信通知回调
Route::post('/weixin/success','Weixin\PayController@success');
Route::get('/weixin/success/aaa','Weixin\PayController@last');
//微信登录
Route::get('/weixin/login','Weixin\WeixinController@login');
Route::get('/weixin/getcode','Weixin\WeixinController@code');
//微信 JSSDK
Route::get('/weixin/jssdk/test','Weixin\WeixinController@jssdk');


//群聊

Route::get('/fasong','Test\TestController@view');
Route::post('/fasong','Test\TestController@view');

Route::get('/fasong','Test\TestController@chat');
Route::post('/fasong','Test\TestController@dochat');

//接口测试
Route::any('/curl/test1','Api\ApiController@test1');
Route::any('/curl/int','Api\ApiController@int');
Route::post('/curl/openssl','Api\ApiController@openssl');

Route::post('/curl/hd','Api\ApiController@hb');
Route::post('/curl/login','Api\ApiController@login');
Route::post('/curl/dologin','Api\ApiController@dologin');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

