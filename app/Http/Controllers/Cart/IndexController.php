<?php

namespace App\Http\Controllers\Cart;

use App\Model\CartModel;
use App\Model\Goods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public $uid;
    public function __construct()
    {
        $this->middleware(function($request,$next){
           $this->uid = session()->get('uid');
            return $next($request);
        });
    }
    //
    public function index(Request $request)
    {
        /*SESSION
         * $goods = session()->get('cart_goods');
        //print_r($goods);echo '<br/>';
        if(empty($goods)){
            exit('cart 空空如也...');
        }else{
            $arr = [];
            foreach($goods as $k=>$v){
                //echo 'Goods ID: '.$v;echo '</br>';
                $res = Goods::where(['goods_id' => $v])->first()->toArray();
                //echo '<pre>';print_r($res);echo '<pre>';
                $arr[]=$res;
            }
            return view('cart.cartlist',['arr'=>$arr]);
        }*/
        //$uid = session()->get('uid');
        $cart_goods = CartModel::where(['uid'=>$this->uid])->get()->toArray();
//        print_r($cart_goods);exit;
        if(empty($cart_goods)){
            header("refresh:3;url='/goods'");
            exit('cart 空空如也...正在进入商品页面...');
        }else{
            foreach($cart_goods as $v){
                $goods_info = Goods::where(['goods_id'=>$v['goods_id']])->first()->toArray();
                $goods_info['num']  = $v['num'];
                //echo '<pre>';print_r($goods_info);echo '</pre>';
                $list[] = $goods_info;
            }
        }
        $arr = [
            'arr' => $list,
        ];
        return view('cart.cartlist',$arr);
    }
    /** 添加商品进入购物车内*/
    public function addGoods($goods_id){
        $cart_goods = session()->get('cart_goods');

        //是否在购物车内
        if(!empty($cart_goods)){
            if(in_array($goods_id,$cart_goods)){
                exit('此商品已在购物车内');
            }
        }
        session()->push('cart_goods',$goods_id);
        //减库存
        $where = [
            'goods_id' => $goods_id,
        ];
        $store = Goods::where($where)->value('store');
        if($store<=0){
            exit('此商品已无库存...');
        }
        $res = Goods::where($where)->decrement('store');
        if($res){
            echo '添加成功';
        }
    }

    public function add2(Request $request){
        $goods_id = $request->input('goods_id');
        $num = $request->input('num');
        //检查库存
        $store = Goods::where(['goods_id'=>$goods_id])->value('store');
        //print_r($store);die;
        if($store<=0||$num>$store){
            $response = [
                'errno' => 5001,
                'msg'   => '库存不足'
            ];
            return $response;
        }
        //商品加入购物车的唯一性
        $cart_goods = CartModel::where(['uid'=>$this->uid])->get()->toArray();
        if($cart_goods){
            $goods_arr = array_column($cart_goods,'goods_id');
            if(in_array($goods_id,$goods_arr)){
                $response = [
                    'errno' => 5002,
                    'msg' => '此商品已存在'
                ];
                return $response;
            }
        }

        //写入购物车表内
        $data = [
            'goods_id' => $goods_id,
            'num' => $num,
            'add_time' => time(),
            'uid' =>session()->get('uid'),
            'session_token' => session()->get('u_token')
        ];
       // var_dump($data);exit;
        $cid = CartModel::insertGetId($data);

        if(!$cid){
            $response = [
                'errno' => 5002,
                'msg' => '添加购物车失败,请重试',
            ];
            return $response;
        }

        $response = [
            'error' => 0,
            'msg' => '添加成功',
        ];
        return $response;
    }

    /**  删除session*/
    public function delGoods($goods_id){
        //是否有该商品
        $goods = session()->get('cart_goods');
        //print_r($goods);echo '<br/>';
        if(!empty($goods)){
            if(in_array($goods_id,$goods)){
                //执行删除
                foreach($goods as $k=>$v){
                    //echo $v;echo '<br/>';
                    if($goods_id == $v){
                        session()->pull('cart_goods.'.$k);
                    }
                }
                echo '删除成功';
            }else{
                exit('该商品不在购物车内...');
            }
        }
    }

    /*
     * 2019年1月9日15:58:02
     * 商品删除
     * */
    public function del2($goods_id){
        $res = CartModel::where(['uid'=>$this->uid,'goods_id'=>$goods_id])->delete();
        if($res){
            echo 'successly';
            header("refresh:1;url='/cart'");
        }else{
            echo 'fail';
        }
    }
}