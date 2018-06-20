<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/6/11
 * Time: 下午10:44
 */
require "./vendor/autoload.php";

$api = new \PadChat\Api(['secret' => 'test']);

try {
    /** 初始化微信实例 */
    $res = $api->init('https://webhook.fastgoo.net/callback.php');
    /** 设置微信实例 */
    $api->setWxHandle($res['data']['wx_user']);
    /** 账号密码/账号手机号/token 登录 */
    $loginRes = $api->login([
        'username' => '你的账号',
        'password' => '你的密码',
        'wx_data' => '不填则安全验证登录'
    ]);
    var_dump($loginRes);
    /** 获取登录二维码 */
    $qrcode = $api->getLoginQrcode();
    var_dump($qrcode['data']['url']);
    /** 获取扫码状态 */
    while (true) {
        $qrcodeInfo = $api->getQrcodeStatus();
        if ($qrcodeInfo['code'] == -1) {
            exit();
        }
        var_dump($qrcodeInfo);
        sleep(1);
    }
} catch (\PadChat\RequestException $requestException) {
    var_dump($requestException->getMessage());
}