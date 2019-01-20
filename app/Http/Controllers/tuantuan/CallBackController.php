<?php

namespace App\Http\Controllers\tuantuan;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;

class CallBackController extends Controller
{
    function oauth_callback()
    {
        $app = Factory::officialAccount(config('project.tuantuan'));
        $oauth = $app->oauth;
        $user = $oauth->user();

        $_SESSION['wechat_user'] = $user->toArray();

        $targetUrl = empty($_SESSION['target_url']) ? '/wechat' : $_SESSION['target_url'];
        header('location:' . $targetUrl);
    }

}
