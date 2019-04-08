<?php

namespace App\Admin\Controllers;

use App\Model\HBModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class HBUserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new HBModel);

        $grid->id('Id');
        $grid->name('Name');
        $grid->email('Email');
        $grid->tel('Tel');
        $grid->status('Status')->display(function($status){
            if($status==0){
                return '未登录';
            }elseif($status==1){
                return '手机登录';
            }elseif($status==3){
                return '电脑登录';
            }elseif($status==4){
                return '手机 电脑';
            }
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(HBModel::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->email('Email');
        $show->tel('Tel');
        $show->status('Status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new HBModel);

        $form->text('name', 'Name');
        $form->email('email', 'Email');
        $form->text('tel', 'Tel');
        $form->switch('status', 'Status');

        return $form;
    }
}
