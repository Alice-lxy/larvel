<?php

namespace App\Http\Controllers\Order;

use App\Model\CartModel;
use App\Model\Goods;
use App\Model\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index(){
        echo __METHOD__;
    }
    public function add(){
        //查询购物车的商品
        $cart_goods = CartModel::where(['uid'=>session()->get('uid')])->get()->toArray();
        //print_r($cart_goods);exit;
        if(empty($cart_goods)){
            exit('暂无商品!请先加购');
        }
        //购买商品总金额
        $order_amount = 0;
        foreach($cart_goods as $v){
            $goods_info = Goods::where(['goods_id'=>$v['goods_id']])->first()->toArray();

            $goods_info['num'] = $v['num'];
            $list[] = $goods_info;
            //计算总价格 = 数量*单价
            $order_amount += $goods_info['price'] * $v['num'];
        }
       // print_r($order_amount);exit;
        //生成订单号
        $order_number = Order::generateOrderSN();
        //echo $order_number;exit;
        $data = [
            'order_number' => $order_number,
            'order_amount' => $order_amount,
            'uid' => session()->get('uid'),
            'add_time' => time(),
        ];
        $res = Order::insert($data);
        if(!$res){
            echo '生成订单失败';
        }else{
            echo '生成订单成功';
            //清空购物车
            CartModel::where(['uid' => session()->get('uid')])->delete();
            header("refresh:2;url='/order/list'");
        }
    }
    public function orderlist(){
        $order_data = Order::where(['uid'=>session()->get('uid')])->get()->toArray();
        if(empty($order_data)){
            exit('无订单信息...请选择商品结算');
        }else{
            $arr = [
               'order_data' => $order_data
            ];
//            print_r($arr);exit;
            return view('order.orderlist',$arr);
        }
    }
    public function del(){

    }
}