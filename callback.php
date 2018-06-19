<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/6/19
 * Time: 下午2:55
 */
require "./vendor/autoload.php";
$writeLog = function ($content = '') {
    file_put_contents('./callback.log', '[' . date('Y-m-d H:i:s') . '] ' . $content . PHP_EOL, FILE_APPEND);
};
try {
    $receiver = new \PadChat\Receiver();
    $api = new \PadChat\Api(['secret' => 'test']);
    $api->setWxHandle($receiver->getWxUser());
    $writeLog($receiver->getParams());
    switch ($receiver->getEventType()) {
        case 'login_success':
            $loginInfo = $receiver->getLoginInfo();
            $writeLog('登录成功：' . json_decode($loginInfo, JSON_UNESCAPED_UNICODE));
            break;
        case 'push':
            switch ($receiver->getMsgType()) {
                case $receiver::MSG_TEXT:
                    $api->sendMsg($receiver->getFromUser(), $receiver->getContent());
                    break;
                case $receiver::MSG_IMAGE:
                    $api->sendMsg($receiver->getFromUser(), "收到图片消息");
                    break;
                case $receiver::MSG_VOICE:
                    $api->sendMsg($receiver->getFromUser(), "收到语音消息");
                    break;
                case $receiver::MSG_HEAD_BUFF:
                    break;
                case $receiver::MSG_FRIEND_REQUEST:
                    break;
                case $receiver::MSG_SHARE_CARD:
                    $api->sendMsg($receiver->getFromUser(), "收到分享名片消息");
                    break;
                case $receiver::MSG_VIDEO:
                    $api->sendMsg($receiver->getFromUser(), "收到视频消息");
                    break;
                case $receiver::MSG_FACE:
                    break;
                case $receiver::MSG_LOCATION:
                    $api->sendMsg($receiver->getFromUser(), "收到定位分享消息");
                    break;
                case $receiver::MSG_APP_MSG:
                    $api->sendMsg($receiver->getFromUser(), "收到链接消息");
                    break;
                case $receiver::MSG_CALL_PHONE:
                    $api->sendMsg($receiver->getFromUser(), "收到消息");
                    break;
                case $receiver::MSG_STATUS_PUSH:
                    break;
                case $receiver::MSG_TELL_PUSH:
                    break;
                case $receiver::MSG_TELL_INVITE:
                    break;
                case $receiver::MSG_SMALL_VIDEO:
                    break;
                case $receiver::MSG_TRANSFER:
                    break;
                case $receiver::MSG_RED_PACKET:
                    break;
                case $receiver::MSG_INVITE_ROOM:
                    break;
                case $receiver::MSG_WECHAT_PUSH:
                    break;
                case $receiver::MSG_CALLBACK:
                    break;
            }
            break;
        default:
            $writeLog('异常通知：' . json_decode($receiver->getOriginStr(), JSON_UNESCAPED_UNICODE));
    }
} catch (\PadChat\RequestException $requestException) {
    $writeLog("错误日志：" . $requestException->getMessage());
}
