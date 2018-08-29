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
                    if ($receiver->getMsgFromType() == 1) {
                        switch ($receiver->getContent()) {
                            case "文字":
                                $api->sendMsg($receiver->getFromUser(), "这是一条文字消息");
                                break;
                            case "图片":
                                $api->sendMsg($receiver->getFromUser(), [
                                    'image_file' => '图片全路径',
                                ]);
                                break;
                            case "语音":
                                $api->sendMsg($receiver->getFromUser(), [
                                    'voice_file' => '语音全路径',
                                    'time' => 1000,
                                ]);
                                break;
                            case "表情":
                                $api->sendMsg($receiver->getFromUser(), "[强]");
                                break;
                            case "链接":
                                $api->sendMsg($receiver->getFromUser(), [
                                    'title' => 'test',
                                    'desc' => 'test2',
                                    'url' => 'https://www.baidu.com',
                                    'img' => 'http://wx.qlogo.cn/mmhead/ver_1/KI3hyxHcWsoicWUzJWUrwVZS1iczNeYNNR0EQ9Hq2KPAgHjF8JP3kicC2wPMrHP5CSNV0s9nTh2vObG49aFvdc5wozZokXC9psVibArhKobPgCU/132'
                                ]);
                                break;
                            case "名片":
                                $api->sendMsg($receiver->getFromUser(), [
                                    'contact_wxid' => 'wxid_k9jdv2j4n8cf12',
                                    'contact_name' => 'ipadchat-api 周先生',
                                ]);
                                break;
                        }
                    }
                    if ($receiver->getMsgFromType() == 2 && $receiver->isAtMe() && in_array($receiver->getMsgParams()['send_wxid'], ['wxid_k9jdv2j4n8cf12'])) {

                        /** 发布公告 */
                        if (strpos($receiver->getContent(), '#发布公告') !== false) {
                            $str = strstr($receiver->getContent(), '#发布公告');
                            $str = str_replace(['#发布公告 ', '#发布公告'], '', $str);
                            $api->setRoomAnnouncement($receiver->getFromUser(), $str);
                        }

                        /** 删除成员 */
                        if (strpos($receiver->getContent(), '#踢出') !== false) {
                            $str = strstr($receiver->getContent(), '#踢出');//
                            $at_users = $receiver->getMsgParams()['at_users'];
                            $my_index = array_search('wxid_zizd4h0uzffg22', $at_users);
                            if ($my_index !== false) {
                                array_splice($at_users, $my_index, 1);
                            }
                            $memberInfo = $api->getContact($at_users[0]);
                            $api->deleteRoomMember($receiver->getFromUser(), $at_users[0]);
                            !empty($memberInfo['nick_name']) && $api->sendMsg($receiver->getFromUser(), '成功踢出"' . $memberInfo['nick_name'] . '"');
                        }
                    }
                    //$api->sendMsg($receiver->getFromUser(), $receiver->getContent());
                    break;
                case $receiver::MSG_IMAGE://图片消息
                    //$api->sendMsg($receiver->getFromUser(), "收到图片消息");
                    break;
                case $receiver::MSG_VOICE://语音消息
                    //$api->sendMsg($receiver->getFromUser(), "收到语音消息");
                    break;
                case $receiver::MSG_HEAD_BUFF://不晓得是啥
                    break;
                case $receiver::MSG_FRIEND_REQUEST://好友申请
                    $params = $receiver->getXmlParams();
                    if (in_array($params['content'], ['ipadchat-api'])) {
                        $api->acceptUser($params['encryptusername'], $params['ticket']);
                        $api->addRoomMember('5687620528@chatroom', $params['fromusername']);
                    }
                    break;
                case $receiver::MSG_SHARE_CARD://分享名片消息
                    //$api->sendMsg($receiver->getFromUser(), "收到分享名片消息");
                    break;
                case $receiver::MSG_VIDEO://视频消息
                    //$api->sendMsg($receiver->getFromUser(), "收到视频消息");
                    break;
                case $receiver::MSG_FACE://表情消息
                    break;
                case $receiver::MSG_LOCATION://定位消息
                    //$api->sendMsg($receiver->getFromUser(), "收到定位分享消息");
                    break;
                case $receiver::MSG_APP_MSG://appmsg
                    //$api->sendMsg($receiver->getFromUser(), "收到APPMSG消息");
                    break;
                case $receiver::MSG_CALL_PHONE://语音视频通话
                    //$api->sendMsg($receiver->getFromUser(), "收到消息");
                    break;
                case $receiver::MSG_STATUS_PUSH:
                    //$api->sendMsg($receiver->getFromUser(), "");
                    break;
                case $receiver::MSG_TELL_PUSH:
                    break;
                case $receiver::MSG_TELL_INVITE:
                    break;
                case $receiver::MSG_SMALL_VIDEO://小视频消息
                    //$api->sendMsg($receiver->getFromUser(), "收到小视频消息");
                    break;
                case $receiver::MSG_TRANSFER://转账记录
                    $msg = $receiver->getOriginMsg();
                    $ret = $api->acceptTransfer(json_encode($msg, JSON_UNESCAPED_UNICODE));
                    if (isset($ret['status']) && $ret['status'] === 0) {
                        $api->sendMsg($receiver->getFromUser(), "转账我已经领了，感谢慷慨相助");
                    }
                    break;
                case $receiver::MSG_RED_PACKET://红包记录 群收款
                    $msg = $receiver->getOriginMsg();
                    if ($receiver->getMsgFromType() == 2) {
                        $scene_id = $receiver->getXmlParams()['scene_id'];
                        if ($scene_id == 1001) {
                            $api->sendMsg($receiver->getFromUser(), "收到群收款消息");
                        }
                        if ($scene_id == 1002) {
                            $ret = $api->receiveRedPacket(json_encode($msg, JSON_UNESCAPED_UNICODE));
                            if (empty($ret['key'])) {
                                return;
                            }
                            $ret = $api->openRedPacket(json_encode($msg, JSON_UNESCAPED_UNICODE), $ret['key']);
                            if (isset($ret['status']) && $ret['status'] === 0) {
                                $api->sendMsg($receiver->getFromUser(), "红包我已经领了，感谢慷慨相助");
                            }
                            $api->sendMsg($receiver->getFromUser(), "收到群红包消息");
                        }
                    }
                    if ($receiver->getMsgFromType() == 1) {
                        $ret = $api->receiveRedPacket(json_encode($msg, JSON_UNESCAPED_UNICODE));
                        if (empty($ret['key'])) {
                            return;
                        }
                        $ret = $api->openRedPacket(json_encode($msg, JSON_UNESCAPED_UNICODE), $ret['key']);
                        if (isset($ret['status']) && $ret['status'] === 0) {
                            $api->sendMsg($receiver->getFromUser(), "红包我已经领了，感谢慷慨相助");
                        }
                        $api->sendMsg($receiver->getFromUser(), "收到红包消息");
                    }
                    break;
                case $receiver::MSG_SHARE_LINK://分享链接
                    //$api->sendMsg($receiver->getFromUser(), "收到链接分享消息");
                    break;
                case $receiver::MSG_SHARE_FILE://分享文件
                    //$api->sendMsg($receiver->getFromUser(), "收到文件消息");
                    break;
                case $receiver::MSG_SHARE_COUPON://分享卡券
                    //$api->sendMsg($receiver->getFromUser(), "收到卡券分享消息");
                    break;
                case $receiver::MSG_INVITE_USER://群里面进新人
                    $invite_name = $receiver->getMsgParams()['invite_name'];
                    $api->sendMsg($receiver->getFromUser(), "欢迎新人\"{$invite_name}\"加入群聊，群内禁止机器人测试,禁止广告开车。请自觉查看群公告信息");
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
