<?php

namespace App\Http\Controllers\wen;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use EasyWeChat\Kernel\Messages\Text;
class WenController extends Controller
{

    protected $app;

    function __construct()
    {
        $this->app = Factory::miniProgram(Config('project.wen'));

    }

    function getOpenid(Request $request){
      $session = $this->app->auth->session($request['code']);
        return $session;
    }

}
