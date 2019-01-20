<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;
use Illuminate\Http\Request;

class WeChatController extends Controller
{

    function serve(){
        $config = [
            'app_id' => 'wxd4a50070d4f44a26',
            'secret' => 'a5d16852f75c1852c73c75bd061dff56',
            'token' => 'liuchen',
            'response_type' => 'array',
            //...
        ];

        $app = Factory::officialAccount($config);

        $response = $app->server->serve();

        return $response;
    }
}
