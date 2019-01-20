<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;

class WeChatBaseController extends Controller
{
     private   $tuantuan_config = [
         'app_id' => 'wxd4a50070d4f44a26',
         'secret' => 'a5d16852f75c1852c73c75bd061dff56',
         'token' => 'liuchen',
         'response_type' => 'array',
     ];

     private $app;
     static private $instance;

     private function __construct($project)
     {
         switch($project){
             case 'tuantuan':
                 $config = $this->tuantuan_config;
                 break;
             default :
                 $config = $this->tuantuan_config;
         }
         $this->app = Factory::officialAccount($config);
     }

     private function __clone()
     {
         // TODO: Implement __clone() method.
     }

     static public function getInstance($project){
         if(!self::$instance){
             self::$instance = new WeChatBaseController($project);
         }
         return self::$instance;
     }

     public function getApp(){
         return $this->app;
     }
}
