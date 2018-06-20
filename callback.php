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
    $writeLog($receiver->getOriginStr());
    switch ($receiver->getEventType()) {
        case 'login_success':
            $loginInfo = $receiver->getLoginInfo();
            $writeLog('登录成功：' . json_decode($loginInfo, JSON_UNESCAPED_UNICODE));
            break;
        case 'push':
            switch ($receiver->getMsgType()) {
                case $receiver::MSG_TEXT://文本消息事件
                    $api->sendMsg($receiver->getFromUser(), $receiver->getContent());
                    break;
                case $receiver::MSG_IMAGE://图片消息
                    $api->sendMsg($receiver->getFromUser(), "收到图片消息");
                    break;
                case $receiver::MSG_VOICE://语音消息
                    $api->sendMsg($receiver->getFromUser(), "收到语音消息");
                    break;
                case $receiver::MSG_HEAD_BUFF://不晓得是啥
                    break;
                case $receiver::MSG_FRIEND_REQUEST:
                    break;
                case $receiver::MSG_SHARE_CARD://分享名片消息
                    $api->sendMsg($receiver->getFromUser(), "收到分享名片消息");
                    break;
                case $receiver::MSG_VIDEO://视频消息
                    $api->sendMsg($receiver->getFromUser(), "收到视频消息");
                    break;
                case $receiver::MSG_FACE://表情消息
                    break;
                case $receiver::MSG_LOCATION://定位消息
                    $api->sendMsg($receiver->getFromUser(), "收到定位分享消息");
                    break;
                case $receiver::MSG_APP_MSG://appmsg
                    $api->sendMsg($receiver->getFromUser(), "收到APPMSG消息");
                    break;
                case $receiver::MSG_CALL_PHONE://语音视频通话
                    $api->sendMsg($receiver->getFromUser(), "收到消息");
                    break;
                case $receiver::MSG_STATUS_PUSH:
                    $api->sendMsg($receiver->getFromUser(), "");
                    break;
                case $receiver::MSG_TELL_PUSH:
                    break;
                case $receiver::MSG_TELL_INVITE:
                    break;
                case $receiver::MSG_SMALL_VIDEO://小视频消息
                    $api->sendMsg($receiver->getFromUser(), "收到小视频消息");
                    break;
                case $receiver::MSG_TRANSFER://转账记录
                    $api->sendMsg($receiver->getFromUser(), "收到转账消息");
                    break;
                case $receiver::MSG_RED_PACKET://红包记录 群收款
                    if ($receiver->getMsgFromType() == 2) {
                        $scene_id = $receiver->getXmlParams()['scene_id'];
                        if ($scene_id == 1001) {
                            $api->sendMsg($receiver->getFromUser(), "收到群收款消息");
                        }
                        if ($scene_id == 1002) {
                            $api->sendMsg($receiver->getFromUser(), "收到群红包消息");
                        }
                    }
                    if ($receiver->getMsgFromType() == 1) {
                        $api->sendMsg($receiver->getFromUser(), "收到红包消息");
                    }
                    break;
                case $receiver::MSG_SHARE_LINK://分享链接
                    $api->sendMsg($receiver->getFromUser(), "收到链接分享消息");
                    break;
                case $receiver::MSG_SHARE_FILE://分享文件
                    $api->sendMsg($receiver->getFromUser(), "收到文件消息");
                    break;
                case $receiver::MSG_SHARE_COUPON://分享卡券
                    $api->sendMsg($receiver->getFromUser(), "收到卡券分享消息");
                    break;
                case $receiver::MSG_INVITE_USER://群里面进新人
                    $api->sendMsg($receiver->getFromUser(), "欢迎新人\"{$this->msg['invite_name']}\"加入群聊，群内禁止机器人测试,禁止广告开车。请自觉查看群公告信息");
                    break;
                case $receiver::MSG_INVITE_ROOM://
                    break;
                case $receiver::MSG_WECHAT_PUSH://系统
                    break;
                case $receiver::MSG_CALLBACK://通知
                    break;
            }
            break;
        default:
            $writeLog('异常通知：' . json_decode($receiver->getOriginStr(), JSON_UNESCAPED_UNICODE));
    }
} catch (\PadChat\RequestException $requestException) {
    $writeLog("错误日志：" . $requestException->getMessage());
}
