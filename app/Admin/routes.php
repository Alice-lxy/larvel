<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->resource('/goods',GoodsController::class);
    $router->resource('/user',UserController::class);
    $router->resource('/weixin',WeixinController::class);
    $router->resource('/weixinmedia',WeixinMediaController::class);

    $router->resource('/allsend',AllSendController::class);
    $router->post('/send','AllSendController@allSend');//群发

    $router->get('/material','WeixinMediaController@upShow');//获取永久素材
    $router->post('/material','WeixinMediaController@formTest');


});
