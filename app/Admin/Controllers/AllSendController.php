<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Model\WeixinMedia;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\HasResourceActions;
use Illuminate\Support\Facades\Redis;

use Encore\Admin\Form;


use GuzzleHttp;
use Illuminate\Http\Request;


class AllSendController extends Controller
{
    use HasResourceActions;
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token


    public function index(Content $content)
    {
        return $content
            ->header('微信')
            ->description('群发')
            ->body(view('admin.sendall'));
       //return ;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinMedia());
        $form->textarea('text','text');
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
    /** 群发*/
    public function allSend(Request $request){
        $text = $request->input('text');
        //1 获取access_token拼接请求接口
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->getWXAccessToken();
        //2 请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);

        $data = [
            "filter" =>[
                "is_to_all" => true
            ],
            "text"  =>  [
                "content"   =>  $text
            ],
            "msgtype" =>   "text"
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

}
