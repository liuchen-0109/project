<?php

namespace App\Http\Controllers\tuantuan;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use EasyWeChat\Kernel\Messages\Text;
class WeChatController extends Controller
{

    protected $app;

    function __construct()
    {
        $config = [
            'app_id' => 'wxd4a50070d4f44a26',
            'secret' => 'a5d16852f75c1852c73c75bd061dff56',
            'token' => 'liuchen',
            'response_type' => 'array',
            //...
        ];

        $this->app = Factory::officialAccount($config);
    }

    /**
     * 微信公众号接入
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    function serve()
    {
        $app = $this->app;
        $this->message();//消息处理
        $response = $app->server->serve();

        return $response;
    }

    function message()
    {
        $this->app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    file_put_contents('1.txt',json_encode($message));
                    return '你的openid是'.$message->FromUserName;
                    break;
                case 'text':
                    return new Text('你好欢迎关注公众号。');
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

            // ...
        });
    }

}
