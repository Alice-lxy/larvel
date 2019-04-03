<?php

namespace App\Admin\Controllers;

use App\Model\Exam;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ExamController extends Controller
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
        $grid = new Grid(new Exam);

        $grid->id('Id');
        $grid->name('Name');
        $grid->card('Card');
        $grid->api('Api');
        $grid->app_num('App num');
        $grid->status('Status')->display(function($status){
            if($status==0){
                return '待审核';
            }elseif($status==1){
                return '已通过';
            }else{
                return '未通过';
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
        $show = new Show(Exam::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->card('Card');
        $show->api('Api');
        $show->app_num('App num');
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
        $form = new Form(new Exam);

        $form->text('name', 'Name');
        $form->text('card', 'Card');
        $form->text('api', 'Api');
        $form->text('status', 'Status');
        $form->switch('app_num', 'App num')->default(1);

        return $form;
    }
}
