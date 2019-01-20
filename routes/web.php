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
Route::get('/post', function(){
    return 'this is hello web';
});
Route::any('/oauth_callback', '\App\Http\Controllers\tuantuan\CallBackController@oauth_callback');
Route::any('/menu/get', '\App\Http\Controllers\tuantuan\MenuController@getMenu');
Route::any('/menu/set', '\App\Http\Controllers\tuantuan\MenuController@setMenu');
