<?php

namespace App\Http\Controllers\tuantuan;

class EventController extends WeChatController
{
    public function handelEvent($message){

        switch($message->Event){
            case 'subscribe';
                return '你的openid是'.$message->FromUserName;
            break;
            case 'unsubscribe';
                return '已取消关注';
            break;
        }
    }


}
