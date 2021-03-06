<?php

namespace App\Admin\Controllers;

use App\Model\WeixinMedia;
use App\Model\WeixinForMedia;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WeixinMediaController extends Controller
{
    use HasResourceActions;
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
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
     * @return Content */
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
        $grid = new Grid(new WeixinMedia);

        $grid->id('Id');
        $grid->openid('Openid');
        $grid->add_time('Add time');
        $grid->msg_type('Msg type');
        $grid->media_id('Media id');
        $grid->format('Format');
        $grid->msg_id('Msg id');
        $grid->local_file_name('Local file name')->display(function($picture){
            return '<img src="https://lxy.qianqianya.xyz/wx/images/'.$picture.'">';
        });
        $grid->local_file_path('Local file path');

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
        $show = new Show(WeixinMedia::findOrFail($id));

        $show->id('Id');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->msg_type('Msg type');
        $show->media_id('Media id');
        $show->format('Format');
        $show->msg_id('Msg id');
        $show->local_file_name('Local file name');
        $show->local_file_path('Local file path');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinMedia);

        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('msg_type', 'Msg type');
        $form->text('media_id', 'Media id');
        $form->text('format', 'Format');
        $form->text('msg_id', 'Msg id');
        $form->text('local_file_name', 'Local file name');
        $form->text('local_file_path', 'Local file path');

        return $form;
    }

    /**
     * 获取微信AccessToken
     */
    public function getWXAccessToken()
    {
        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;

    }

    /**
     * 上传素材
     */
    public function upMaterialTest($file_path,$file_name)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            'multipart' => [
                [
                    'name'     => 'media',
                    'contents' => fopen($file_path, 'r')
                ],
            ]
        ]);


        $body = $response->getBody();
        //echo $body;echo '<hr>';
        $d = json_decode($body,true);
        $d['file_name'] = $file_name;
        $d['add_time'] = time();
       /* echo '<pre>';print_r($d);echo '</pre>';die;
        $data = [
            'file_name' => $file_name,
            'add_time' => time(),
            'media_id' => $d['media_id'],
            'url' => $d['url'],
        ];

        echo '<pre>';print_r($data);echo '</pre>';*/
        $res = WeixinForMedia::insertGetId($d);
        if($res){
            echo "success";
        }else{
            echo "fail，请重试";echo '</br>';
        }

    }
    public function upShow(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->view());
       // return view('admin.up');
    }
    public function view(){
        $view = new Form(new WeixinForMedia());
        $view->file('media','media');
        return $view;
    }
    public function formTest(Request $request)
    {
        echo '<pre>';print_r($_POST);echo '</pre>';echo '<hr>';
        echo '<pre>';print_r($_FILES);echo '</pre>';echo '<hr>';
        //exit();
        //保存文件
        $img_file = $request->file('media');
        echo '<pre>';print_r($img_file);echo '</pre>';echo '<hr>';

        $img_origin_name = $img_file->getClientOriginalName();//图片原始名称
       // echo 'originName: '.$img_origin_name;echo '</br>';
        $file_ext = $img_file->getClientOriginalExtension(); //获取文件扩展名
        //echo 'ext: '.$file_ext;echo '</br>';

        //重命名
        $new_file_name = str_random(15). '.'.$file_ext;
      //  echo 'new_file_name: '.$new_file_name;echo '</br>';

        //文件保存路


        //保存文件
        $save_file_path = $request->media->storeAs('form_test',$new_file_name);       //返回保存成功之后的文件路径

       // echo 'save_file_path: '.$save_file_path;echo '<hr>';

        //上传至微信永久素材
        $this->upMaterialTest($save_file_path,$new_file_name);
    }
}
