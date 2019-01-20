<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::any('/wechat', '\App\Http\Controllers\tuantuan\WeChatController@serve');
Route::any('/wen', '\App\Http\Controllers\wen\WenController@getOpenid');



