<?php

namespace App\Admin\Controllers;

use App\Model\WeixinForMedia;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WeixinForMediaController extends Controller
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
        $grid = new Grid(new WeixinForMedia);

        $grid->id('Id');
        $grid->media_id('Media id');
        $grid->file_name('File name')->display(function($file_name){
            return '<img src="https://lxy.qianqianya.xyz/form_test/'.$file_name.'" width=50px;>';
        });
        $grid->url('Url');
        $grid->add_time('Add time');


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
        $show = new Show(WeixinForMedia::findOrFail($id));

        $show->id('Id');
        $show->add_time('Add time');
        $show->media_id('Media id');
        $show->url('Url');
        $show->file_name('File name');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinForMedia);

        $form->number('add_time', 'Add time');
        $form->text('media_id', 'Media id');
        $form->url('url', 'Url');
        $form->text('file_name', 'File name');

        return $form;
    }
}
