<?php

namespace App\Http\Controllers\wen;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use App\model\WenUserModel as User;

class LoginCOntroller extends Controller
{



    protected $app;

    function __construct()
    {
        $this->app = Factory::miniProgram(Config('project.wen'));

    }


    public function login(){
        $result = $this->login_main();

        if ($result['loginState'] === config('wechat.wen.S_AUTH')) {
            return json_encode([
                'code' => 0,
                'data' => $result['userinfo']
            ]);
        } else {
            return json_encode([
                'code' => -1,
                'error' => $result['error']
            ]);
        }
    }
    public function login_main(){
        try {
            $code = $this->getHttpHeader(config('wechat.wen.WX_HEADER_CODE'));
            $encryptedData = $this->getHttpHeader(config('wechat.wen.WX_HEADER_ENCRYPTED_DATA'));
            $iv = $this->getHttpHeader(config('wechat.wen.WX_HEADER_IV'));

            if (!$code) {
                throw new \Exception("请求头未包含 code，请配合客户端 SDK 登录后再进行请求");
            }

            return $this->login_2($code, $encryptedData, $iv);
        } catch (\Exception $e) {
            return [
                'loginState' => config('wechat.wen.E_AUTH'),
                'error' => $e->getMessage()
            ];
        }
    }


    public function getSessionkey($code){
        $appId = env('WEN_APPID');
        $appSecret = env('WEN_APPSECRET');
        return $this->getSessionKeyDirectly($appId, $appSecret, $code);
    }

    public  function login_2($code, $encryptData, $iv) {
        // 1. 获取 session key
        list($session_key, $openid) = array_values($this->getSessionKey($code));

        // 2. 生成 3rd key (skey)
        $skey = sha1($session_key . mt_rand());

        // 如果只提供了 code
        // 就用 code 解出来的 openid 去查数据库
        if ($code && !$encryptData && !$iv) {
            $userInfo = User::where(['open_id'=>$openid])->first();
            $wxUserInfo = json_decode($userInfo->user_info);

            echo  1;
            // 更新登录态
            $this->storeUserInfo($wxUserInfo, $skey, $session_key);

            return [
                'loginState' => config('wechat.wen.S_AUTH'),
                'userinfo' => [
                    'userinfo' => $wxUserInfo,
                    'skey' => $skey
                ]
            ];
        }

        /**
         * 3. 解密数据
         * 由于官方的解密方法不兼容 PHP 7.1+ 的版本
         * 这里弃用微信官方的解密方法
         * 采用推荐的 openssl_decrypt 方法（支持 >= 5.3.0 的 PHP）
         * @see http://php.net/manual/zh/function.openssl-decrypt.php
         */
        $decryptData = \openssl_decrypt(
            base64_decode($encryptData),
            'AES-128-CBC',
            base64_decode($session_key),
            OPENSSL_RAW_DATA,
            base64_decode($iv)
        );
        $userinfo = json_decode($decryptData);
        var_dump($decryptData);
        // 4. 储存到数据库中
        $this->storeUserInfo($userinfo, $skey, $session_key);

        return [
            'loginState' => config('wechat.wen.S_AUTH'),
            'userinfo' => compact('userinfo', 'skey')
        ];
    }

    /**
     * 获取http头信息
     * @param $headerKey
     * @return string
     */
    public  function getHttpHeader($headerKey) {
        $headerKey = strtoupper($headerKey);
        $headerKey = str_replace('-', '_', $headerKey);
        $headerKey = 'HTTP_' . $headerKey;
        return isset($_SERVER[$headerKey]) ? $_SERVER[$headerKey] : '';
    }
    private  function getSessionKeyDirectly ($appId, $appSecret, $code) {
        $requestParams = [
            'appid' => $appId,
            'secret' => $appSecret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];


        list($status, $body) = array_values( $this->get([
            'url' => 'https://api.weixin.qq.com/sns/jscode2session?' . http_build_query($requestParams),
            'timeout' => 3000,
        ]));

        if ($status !== 200 || !$body || isset($body['errcode'])) {
            throw new \Exception(config('wechat.wen.E_PROXY_LOGIN_FAILED') . ': ' . json_encode($body));
        }

        return $body;
    }
    public static function get($options) {
        $options['method'] = 'GET';
        return self::send($options);
    }
    public static function send($options) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);
        curl_setopt($ch, CURLOPT_URL, $options['url']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if (isset($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        if (isset($options['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $options['timeout']);
        }

        if (isset($options['data'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['data']);
        }

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $body = json_decode($result, TRUE);
        if ($body === NULL) {
            $body = $result;
        }

        curl_close($ch);
        return compact('status', 'body');
    }
    public  function storeUserInfo ($userinfo, $skey, $session_key) {
        $uuid = bin2hex(openssl_random_pseudo_bytes(16));
        $create_time = date('Y-m-d H:i:s');
        $last_visit_time = $create_time;
        $open_id = $userinfo->open_id;
        $user_info = json_encode($userinfo);

        $res = User::where(['open_id'=>$open_id])->first();
        if (!$res) {
            $data = [];
            $data['uuid'] = $uuid;
            $data['skey'] = $skey;
            $data['create_time'] = $create_time;
            $data['last_visit_time'] = $last_visit_time;
            $data['open_id'] = $open_id;
            $data['session_key'] = $session_key;
            $data['user_info'] = $user_info;
            User::create($data);
        } else {
            $data = [];
            $data['skey'] = $skey;
            $data['user_info'] = $user_info;
            $data['last_visit_time'] = $last_visit_time;

            User::where(['open_id'=>$open_id])->update($data);
        }
    }
}
