<?php

namespace App\Http\Controllers\Pay;

use App\Model\Goods;
use App\Model\Order;
use App\Model\UserModel;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public function index()
    {
        echo __METHOD__;
    }

    public function pay1(){
        $url='http://lxy.qianqianya.xyz';
        $client=new Client([
            'base_uri'=>$url,
            'timeout'=>2.0,
        ]);

        $response=$client->request('GET','index.php');
        echo $response->getBody();
    }

    public function pay($order_number){
        //查询订单账号
        $order_info = Order::where(['order_number'=>$order_number])->first();
        if(empty($order_info)){
            exit('无此订单号...请选择正确订单信息进行结算');
        }

        //检查此订单的订单状态
        if($order_info['pay_time'] > 0){
            exit('此订单已支付,请勿重复支出..,');
        }
        //支付宝支付


        //支付成功 修改
        $res = Order::where(['order_number'=>$order_number])->update(['pay_time'=>time(),'order_status'=>2,'pay_price'=>rand(111,222)]);
       // print_r($res);exit;
        header("refresh:0.1;url='/pay/alipay/test'");
        echo '正在跳往支付页面...';
        //增加消费积分
       /* if($res){
            $integral = UserModel::where(['id'=>session()->get('uid')])->value('integral');
            $pay_price = Order::where(['uid' => session()->get('uid')])->value('pay_price');
            $new_integral = $integral + $pay_price;
            UserModel::where(['id'=>session()->get('uid')])->update(['integral'=>$new_integral]);
            echo '支付成功';
            header("refresh:0.1;url='/pay/alipay/test'");
        }else{
            echo '支付失败';
        }*/
    }
}