<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\WeChatBaseController as WeChat;
class WeChatController extends Controller
{
    private $app;
    function __construct()
    {
        $wechat = WeChat::getInstance('tuantuan');
        $this->app = $wechat->getApp();
    }

    function serve(){

        $response = $this->app->get->server->serve();

        return $response;
    }
}
