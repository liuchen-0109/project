<?php

namespace App\Http\Controllers\tuantuan;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use EasyWeChat\Kernel\Messages\Text;
class MenuController extends Controller
{

    protected $app;

    function __construct()
    {
        $this->app = Factory::officialAccount(Config('project.tuantuan'));

    }

    function getMenu(){
        return  $this->app->menu->current();
    }

    function setMenu(){
        $buttons = [
            [
                "type" => "click",
                "name" => "今日歌曲",
                "key"  => "V1001_TODAY_MUSIC"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url"  =>  url("/posts")
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://v.qq.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];
        $this->app->menu->create($buttons);
    }

}
