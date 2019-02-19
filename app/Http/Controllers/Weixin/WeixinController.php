<?php

namespace App\Http\Controllers\Weixin;

use App\Model\WeixinUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redis;
use GuzzleHttp;

class WeixinController extends Controller
{
    //

    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token

    public function test()
    {
        echo __METHOD__;
        echo 'Token:'.$this->getWXAccessToken();
        //$this->getUserInfo(1);
    }

    /**
     * 首次接入
     */
    public function validToken1()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        echo $_GET['echostr'];
    }



    /**
     * 接收微信服务器事件推送
     */
    public function wxEvent()
    {
        $data = file_get_contents("php://input");

        //解析XML
        $xml = simplexml_load_string($data);        //将 xml字符串 转换成对象

        $event = $xml->Event;                       //事件类型
        $openid = $xml->FromUserName;
        //var_dump($xml);echo '<hr>';


        //处理XML
        if(isset($xml->MsgType)){
            if($xml->MsgType=='text'){
                $msg = $xml->Content;
                $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. $msg. date('Y-m-d H:i:s') .']]></Content></xml>';
                echo $xml_response;exit();
            }
        }

        //判断事件类型
        if($event=='subscribe') {
            $sub_time = $xml->CreateTime;               //扫码关注时间


            echo 'openid: ' . $openid;echo '</br>';echo '$sub_time: ' . $sub_time;

            //获取用户信息
            $user_info = $this->getUserInfo($openid);
            echo '<pre>';print_r($user_info);echo '</pre>';

            //保存用户信息
            $u = WeixinUser::where(['openid'=>$openid])->first();

            if($u){
                echo '此用户已存在';
            }else{
                $user_data = [
                    'openid' => $openid,
                    'add_time' => time(),
                    'nickname' => $user_info['nickname'],
                    'sex' => $user_info['sex'],
                    'headimgurl' => $user_info['headimgurl'],
                    'subscribe_time' => $sub_time,
                ];
                $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                var_dump($id);
            }
        }elseif($event=='CLICK'){
            if($xml->EventKey=="kefu001"){
                $this->kefu001($openid,$xml->ToUserName);
            }
        }
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    public function kefu001($openid,$from)
    {
        // 文本消息
        $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$from.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '您好, 现在是北京时间'. date('Y-m-d H:i:s') .'.我们现已下班......有事请拨打110、119等.]]></Content></xml>';
        echo $xml_response;
    }

    /**
     * 接收事件推送
     */
    public function validToken()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        //echo $_GET['echostr'];
        $data = file_get_contents("php://input");
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
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
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
        //$openid = 'oLreB1jAnJFzV_8AGWUZlfuaoQto';
        $access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $data = json_decode(file_get_contents($url),true);
        return $data;
    }

    //创建服务号菜单
    public function createMenu(){
      //  echo __METHOD__;
        //1 获取access_token拼接请求接口
        //$access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getWXAccessToken();
        //echo $url;
        //2 请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);

        $data = [
            "button"    => [
                [
                    "type"  =>  "click",
                    "name"  =>  "客服SK",
                    "key"   =>  "kefu001"
                ],
                [
                    "name"  =>"菜单",
                    "sub_button"    => [
                        [
                            "type"  => "view",      // view类型 跳转指定 URL
                            "name"  => "百度",
                            "url"   => "https://www.baidu.com"
                        ],
                        [
                            "type"  => "view",      // view类型 跳转指定 URL
                            "name"  => "搜索",
                            "url"   => "https://www.soso.com"
                        ]
                    ]
                ],
                [
                    "name"  =>"shopping",
                    "sub_button"    => [
                        [
                            "type"  => "view",      // view类型 跳转指定 URL
                            "name"  => "淘宝",
                            "url"   => "https://www.taobao.com/"
                        ],
                        [
                            "type"  => "view",      // view类型 跳转指定 URL
                            "name"  => "天猫",
                            "url"   => "https://www.tmall.com"
                        ]
                    ]
                ]
            ]
        ];


        $r = $client->request('POST',$url,[
           'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);

        //3 解析微信接口返回信息
        $response_arr = json_decode($r->getBody(),true);
        //print_r($response_arr);

        if($response_arr['errcode'] == 0){
            echo "菜单创建成功";
        }else{
            echo "菜单创建失败，请重试";echo '</br>';
            echo $response_arr['errmsg'];
        }

    }
}