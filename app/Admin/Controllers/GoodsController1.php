<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Show;

use App\Model\Goods;

class GoodsController extends Controller
{
    use HasResourceActions;
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('商品列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Goods());

        $grid->model()->orderBy('goods_id','desc');     //倒序排序

        $grid->goods_id('商品ID');
        $grid->goods_name('商品名称');
        $grid->store('库存');
        $grid->price('价格');
        $grid->created_at('添加时间');
        $grid->paginate(5);


        /*$grid->c_time('添加时间')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });*/
        return $grid;
    }


    //创建
    public function create(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('添加')
            ->body($this->form());
    }


    //详情
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }
    public function detail($id){
        $show = new Show(Goods::findOrFail($id));

        $show->goods_id('goods_id','商品ID');
        $show->goods_name('goods_name','商品名称');
        $show->store('store','库存');
        $show->price('price','价格')->symbol('￥');

        return $show;
    }
    //修改
    public function edit($id, Content $content)
    {
        //echo __METHOD__;die;
        return $content
            ->header('商品管理')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }
    protected function form(){
        $form = new Form(new Goods());

         $form->text('goods_name','商品名称');
         $form->number('store','库存');
         $form->currency('price','价格')->symbol('￥');

         return $form;
     }
}
