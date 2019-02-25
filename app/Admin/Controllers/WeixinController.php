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
use Illuminate\Support\Facades\Redis;
use GuzzleHttp;


class WeixinController extends Controller
{
    use HasResourceActions;
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token

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
        $message = $request->input('text');
        $openid = $request->input('openid');
        //1 获取access_token拼接请求接口
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getWXAccessToken();
        //2 请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);

        $data = [
            "touser"    => $openid,
            "msgtype"   => "text",
            "text"  => [
                'content'   =>$message
            ]
        ];
        $arr = $client->request('POST',$url,[
            'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        //3 解析微信接口返回信息
        $response_arr = json_decode($arr->getBody(),true);
        //print_r($response_arr);
        if($response_arr['errcode'] == 0){
            echo "success";
        }else{
            echo "fail，请重试";echo '</br>';
            echo $response_arr['errmsg'];
        }
    }

    /** 数据替换*/
    public function newmessage(Request $request){
        $openid = $request->input('openid');
        $info = WeixinUser::where(['openid'=>$openid])->first();
        $name = $info['nickname'];
        $data = WeixinMessage::where(['openid'=>$openid])->get();
        $arr['name'] = $name;
        $arr['data'] = $data;
        echo json_encode($arr);


    }


}
