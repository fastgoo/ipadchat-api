<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/6/11
 * Time: 下午10:44
 */
require "./vendor/autoload.php";

/**
 * @var $api
 * host 请求域名 默认域名https://wxapi.fastgoo.net/
 * timeout 请求超时时间
 * secret 请求key
 */
$api = new \PadChat\Api(['secret' => 'test','host'=>'']);

try {
    /** 初始化微信实例 */
    $res = $api->init('https://xxx.com');
    if (!$res) {
        exit("微信实例获取失败");
    }
    /** 设置微信实例 */
    $api->setWxHandle($res['wx_user']);
    /** 账号密码/账号手机号/token 登录 */

    /*$loginRes = $api->login([
        'username' => '你的账号',
        'password' => '你的密码',
        'wx_data' => '不填则安全验证登录'
    ]);
    var_dump($loginRes);*/

    /** 获取登录二维码 */
    $qrcode = $api->getLoginQrcode();
    if (!$qrcode) {
        exit("二维码链接获取失败");
    }
    echo "请在浏览器中打开 {$qrcode['url']}";

    /** 获取扫码状态 */

    /*while (true) {
        $qrcodeInfo = $api->getQrcodeStatus();
        if (!$qrcodeInfo) {
            exit();
        }
        var_dump($qrcodeInfo);
        sleep(1);
    }*/
} catch (\PadChat\RequestException $requestException) {
    var_dump($requestException->getMessage());
}