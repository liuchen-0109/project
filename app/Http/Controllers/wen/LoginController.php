<?php

namespace App\Http\Controllers\wen;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use EasyWeChat\Kernel\Messages\Text;
class LoginCOntroller extends Controller
{

    protected $app;

    function __construct()
    {
        $this->app = Factory::miniProgram(Config('project.wen'));

    }

    public function login(){
        return 1231312;
    }


}
