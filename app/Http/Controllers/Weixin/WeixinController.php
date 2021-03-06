<?php

namespace App\Http\Controllers\Weixin;

use App\Model\UserModel;
use App\Model\WeixinMedia;
use App\Model\WeixinMessage;
use App\Model\WeixinUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redis;
use GuzzleHttp;
use Illuminate\Support\Facades\Storage;

class WeixinController extends Controller
{
    //

    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    protected $redis_weixin_jsapi_ticket = 'str:weixin_jsapi_ticket';       //微信 jsapi

    public function test()
    {
       // echo __METHOD__;
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

        //记录日志
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);

        $event = $xml->Event;                       //事件类型
        $openid = $xml->FromUserName;
        //var_dump($xml);echo '<hr>';


        //处理用户发送消息
        if(isset($xml->MsgType)){
            if($xml->MsgType=='text'){
                $msg = $xml->Content;
                $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. $msg. date('Y-m-d H:i:s') .']]></Content></xml>';
                echo $xml_response;

                //写入数据库
                $data = [
                    'openid'    =>  $openid,
                    'add_time'  =>time(),
                    'message'   => $msg,
                ];
                $info = WeixinMessage::insertGetId($data);
                var_dump($info);
                
            }elseif($xml->MsgType=='image'){       //用户发送图片信息
                //视业务需求是否需要下载保存图片
                if(1){  //下载图片素材
                    $file_name = $this->dlWxImg($xml->MediaId);
                    $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                    echo $xml_response;

                    //写入数据库
                    $data = [
                        'openid'    => $openid,
                        'add_time'  =>time(),
                        'msg_type'  =>  'img',
                        'media_id'  => $xml->MediaId,
                        'format'    => $xml->Format,
                        'msg_id'    => $xml->MsgId,
                        'local_file_name'   => $file_name
                    ];
                    $m_id = WeixinMedia::insertGetId($data);
                    var_dump($m_id);
                }
            }elseif($xml->MsgType=='voice'){//处理语音
                if(1){
                    $file_name = $this->dlVoice($xml->MediaId);
                    $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.'您好,请问有什么需要帮助的？？？'.date('Y-m-d H:i:s') .']]></Content></xml>';
                    echo $xml_response;

                    //写入数据库
                    $data = [
                        'openid'    => $openid,
                        'add_time'  =>time(),
                        'msg_type'  =>  'voice',
                        'media_id'  => $xml->MediaId,
                        'format'    => $xml->Format,
                        'msg_id'    => $xml->MsgId,
                        'local_file_name'   => $file_name
                    ];
                    $m_id = WeixinMedia::insertGetId($data);
                    var_dump($m_id);
                }
            }elseif($xml->MsgType=='video'){//处理视频
                if(1){
                    $this->dlVideo($xml->MediaId);
                    $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.'此视频不存在...'.date('Y-m-d H:i:s') .']]></Content></xml>';
                    echo $xml_response;
                }
            }elseif($xml->MsgType=='event'){//处理事件
                //判断事件类型
                if($event=='subscribe') {
                    $sub_time = $xml->CreateTime;               //扫码关注时间
                    //echo 'openid: ' . $openid;echo '</br>';echo '$sub_time: ' . $sub_time;
                    //获取用户信息
                    $user_info = $this->getUserInfo($openid);
                    //echo '<pre>';print_r($user_info);echo '</pre>';
                    //保存用户信息
                    $u = WeixinUser::where(['openid'=>$openid])->first();
                    if(!$u){
                        $user_data = [
                            'openid' => $openid,
                            'add_time' => time(),
                            'nickname' => $user_info['nickname'],
                            'sex' => $user_info['sex'],
                            'headimgurl' => $user_info['headimgurl'],
                            'subscribe_time' => $sub_time,
                        ];
                        WeixinUser::insertGetId($user_data);      //保存用户信息
                    }
                    $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.'欢迎进入此公众号'.date('Y-m-d H:i:s') .']]></Content></xml>';
                    echo $xml_response;

                }elseif($event=='CLICK'){
                    if($xml->EventKey=="kefu001"){
                        $this->kefu001($openid,$xml->ToUserName);
                    }
                }
            }
            //exit();
        }

    }

    /**
     * 客服处理
     * @param $openid   用户openid
     * @param $from     开发者公众号id 非 APPID
     */
    public function kefu001($openid,$from)
    {
        // 文本消息
        $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$from.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '您好, 现在是北京时间'. date('Y-m-d H:i:s') .'.我们现已下班......有事请拨打110、119等.]]></Content></xml>';
        echo $xml_response;
    }
    /**
     * 下载图片素材
     * @param $media_id
     */
    public function dlWxImg($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
        //echo $url;echo '</br>';

        //保存图片
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();

        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/images/'.$file_name;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
            echo 111;
        }else{      //保存失败
            echo 222;
        }
        return $file_name;

    }
    /** 回复语音*/
    public function dlVoice($media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;

        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();

        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/voice/'.$file_name;

        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
            echo 111;
        }else{      //保存失败
            echo 222;
        }
        return $file_name;
    }
    /** 视频*/
    public function dlVideo($media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;

        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();

        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/video/'.$file_name;

        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
            echo 111;
        }else{      //保存失败
            echo 222;
        }
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

    /**
     * 刷新access_token
     */
    public function refreshToken()
    {
        Redis::del($this->redis_weixin_access_token);
        echo $this->getWXAccessToken();
    }

    /*
     * 微信登录
     * */
    public function login(){
        //echo __METHOD__;
        return view('weixin.login');
    }
    /*
     * 接受code*/
    public function code(){
         echo __METHOD__;
        //1 回调拿到 code (用户确认登录后 微信会跳 redirect )
        print_r($_GET);echo '<hr/>';
        //获取code
        $code = $_GET['code'];
        //2 用code换取access_token 请求接口
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe24f70961302b5a5&secret=0f121743ff20a3a454e4a12aeecef4be&code='.$code.'&grant_type=authorization_code';
        $token_json = file_get_contents($token_url);
        $token_arr = json_decode($token_json,true);
        echo '<hr>';
        echo '<pre>';print_r($token_arr);echo '</pre>';

        $access_token = $token_arr['access_token'];
        $openid = $token_arr['openid'];

        // 3 携带token  获取用户信息
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_json = file_get_contents($user_info_url);

        $user_arr = json_decode($user_json,true);
        echo '<hr>';
        echo '<pre>';print_r($user_arr);echo '</pre>';
        return $this->info($user_arr);

    }
    public function info($user_arr){
        $unionid =  $user_arr['unionid'];
        $res = WeixinUser::where(['unionid'=>$unionid])->first();
        if($res){
            echo '登录成功';
        }else{
            //+
            $data = [
              'name'    => $user_arr['nickname'],
            ];
            $id = UserModel::insertGetId($data);
            if($id){
                $newdata = [
                    'uid'   => $id,
                    'openid'    => $user_arr['openid'],
                    'add_time'  =>time(),
                    'nickname'  =>  $user_arr['nickname'],
                    'sex'   => $user_arr['sex'],
                    'headimgurl'    => $user_arr['headimgurl'],
                    'unionid'   =>  $unionid
                ];
                //var_dump($newdata);
                $res = WeixinUser::insertGetId($newdata);
                //print_r($arr);
                if($res){
                    echo '登录成功';
                }else{
                    echo '登录失败';
                }
            }
        }
    }

    /** 微信jssdk调试*/
    public function jssdk(){
        //echo __METHOD__;
        $jsconfig = [
            'appid' =>  env('WEIXIN_APPID'),
            'timestamp' => time(),
            'noncestr'  => str_random(10),
//            'sign'  =>  $this->wxJsConfigSign()
        ];
        $sign = $this->wxJsConfigSign($jsconfig);
        $jsconfig['sign'] = $sign;
        $data = [
            'jsconfig'  => $jsconfig
        ];
        return view('weixin.jssdk',$data);
    }
    /*
     * 计算JSSDK sign*/
    public function wxJsConfigSign($param){
        $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; //域名/路由
        //print_r($url);die;
        $ticket = $this->getJsapiTicket();
        $str = 'jsapi_ticket='.$ticket.'&noncestr='.$param['noncestr'].'&timestamp='.$param['timestamp'].'&url='.$url;
       // var_dump($str);die;
        $signature = sha1($str);
        return $signature;
    }
    /*
     * 获取jsapi_ticket*/
    public function getJsapiTicket(){
        //是否有缓存
        $ticket = Redis::get($this->redis_weixin_jsapi_ticket);
        //echo $ticket;die;
        if(!$ticket){
            //无缓存
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->getWXAccessToken().'&type=jsapi';
            $ticket_info = file_get_contents($url);
            $ticket_arr = json_decode($ticket_info,true);

            if(isset($ticket_arr['ticket'])){
                $ticket = $ticket_arr['ticket'];
                Redis::set($this->redis_weixin_jsapi_ticket,$ticket);
                Redis::setTimeout($this->redis_weixin_jsapi_ticket,3600);
            }
        }
        return $ticket;
    }


}