<?php

namespace App\Admin\Controllers;

use App\Model\WeixinMessage;
use App\Model\WeixinUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Symfony\Component\HttpFoundation\Request;

class WeixinController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
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
     * @param mixed   $id
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
     * @param mixed   $id
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
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinUser);

        $form->number('uid', 'Uid');
        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('nickname', 'Nickname');
        $form->switch('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');

        return $form;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WeixinUser);

        $grid->id('Id');
        $grid->uid('Uid');
        $grid->openid('Openid')->display(function($url){
            return '<a href="chat?openid='.$url.'"> '.$url.' </a>';
        });
        $grid->add_time('Add time');
        $grid->nickname('Nickname');
        $grid->sex('Sex');
        $grid->headimgurl('Headimgurl')->display(function ($img_url){
            return '<img src="'.$img_url.'">';
        });
        $grid->subscribe_time('Subscribe time');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WeixinUser::findOrFail($id));

        $show->id('Id');
        $show->uid('Uid');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');

        return $show;
    }

    /** 私聊*/
    public function chat(Content $content){
        $openid = $_GET['openid'];
       // print_r($openid);exit;
        $info = WeixinUser::where(['openid'=>$openid])->first();
        //print_r($info);die;

        $message_info = WeixinMessage::where(['openid'=>$openid])->get();

        $data = [
            'headimgurl' => $info['headimgurl'],
            'openid' => $info['openid'],
            'nickname' => $info['nickname'],
            'info' => $message_info,
        ];
       // print_r($data);die;

        return $content
            ->header($info['nickname'])
            ->description('私聊')
            ->body(view('admin.chat',$data));
    }
    public function dochat(Request $request){
        $text = $request->input('text');
        $openid = $request->input('openid');
        /*$data = [
            'message'   =>  $text,
            'openid'    =>$openid,
            'add_time'  => time()
        ];*/



    }

}
