<?php

namespace App\Http\Controllers\Goods;

use App\Model\Goods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    //全商品展示
    public function index()
    {
        $arr = Goods::all()->toArray();
//        print_r($arr);exit;
        return view('goods.list',['arr'=>$arr]);
    }
    /** 商品详情*/
    public function detail($goods_id){
        $goods = Goods::where(['goods_id'=>$goods_id])->first();
//        print_r($goods);exit;
        //此商品不存在
        if(!$goods){
            header("refresh:1;url='/goods'");
            exit('此商品不存在,请重新选择商品');
        }
        $data = [
            'goods' => $goods,
        ];
        return view('goods.detail',$data);
    }
}
