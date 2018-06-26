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
                                    'image' => "/9j/4AAQSkZJRgABAQAASABIAAD/4QBYRXhpZgAATU0AKgAAAAgAAgESAAMAAAABAAEAAIdpAAQAAAABAAAAJgAAAAAAA6ABAAMAAAABAAEAAKACAAQAAAABAAAAeKADAAQAAAABAAAAQwAAAAD/7QA4UGhvdG9zaG9wIDMuMAA4QklNBAQAAAAAAAA4QklNBCUAAAAAABDUHYzZjwCyBOmACZjs+EJ+/8AAEQgAQwB4AwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/bAEMABAQEBAQEBgQEBgkGBgYJDAkJCQkMDwwMDAwMDxIPDw8PDw8SEhISEhISEhUVFRUVFRkZGRkZHBwcHBwcHBwcHP/bAEMBBAUFBwcHDAcHDB0UEBQdHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHR0dHf/dAAQACP/aAAwDAQACEQMRAD8A+UDp9+7KqWshJI6If8K65vhf4yc/PaRo2CQryoCcYyBz1G4D611i2oSaWWW5KCMZwDwPyqTT/Ec9urwTXYl811w8jMdgHUDjHNfVSxdRW5EjolgeeNzg4vA3iqzjluvsTq1vKIyykNtbnIwMg9DntxVi80m+aCSOSGRnC78lWySSDnp3r2W016S/uFeDYsTSLGI42baxX+LHTJq3JLpVxdTedIBOD5YXzfLfevGMZ6c8VlPGVN5CpYFPRdD54g0oWeoI0u8wOQGIU5Bxn0q/ews8108MZaMFtj46gttHH4V1OsRRWes20kbh4rtmdsnepIBBIPauRE+yS4jilwvyMBz0L5q5Tc/eYL9w/Z+o3TNFnd7qznjKtJArDcpByCe1c3b6XK0kyyIV2ZxngYHfJ4rt9Qv5Li6hkaVkfydu4ZBwM1WlGmyoUkZtrDawx1B4NaKvJI5vqqmrx1OUs9JvLllAj2FmCKH+XczcADOOp6V1ieAfFUr+SLIFh1G9P8a9O0rSNMl023u2aW+dRHMuZXyCDleC2PlPauT1Txdr1lq12tncLGu4KQ0ETHsOSUOelZTzCvKajRS+dxSy/kg5Te5i/wDCI67ol+LPXbU2MuFKpKVUtnpjJzzitDWPDnjkGGWzs5ltZUGxoynKkdQQc4xWxqWv6zr+sSXetXAuZUVQHKKp+UcZ2gcirSeNdVSW30p7lHRcIiGGNgoxgc7M8DPesXisU7ScY3672+QexUvdVzz/AFDQNSsjCbi3eOM7Y1yATuOTt4yecE/nVuDw/eSOsJjJkK7wBjO3GcnnjIPHrXo+r28jww3LXLyeTIJGEaBDwDkgoFOQCa4a71PTP7QkuY5Z5DKihHLsTsC8D5jkY7CopYirUVpbnVVh7JpOJBo6uJ5oysiF8KuVAB9zurpfsE/98fnHXJ6fr0yXX71jLGvC7gM8jH17V0X/AAkS/wDPIfl/9eorxnzaF0q3NG6R/9DK0bwVq3iiwlnsYvOBby5TGyDkDOMMwI61meIvBi+D7CI6xpsvmXEmyEPKnzEDJP7sk8cfnXIeCvihrHglLiOwgiuEumV284tkFRjjB712N5401L4iXdnqGtQxWsVlvWJI2OGL43MdxPTGBXvz9yPMzow+JnUqKPNoZVkVSeB47eG3dGAG3LYPpknr712mm+BdQ8YT3dxp95b2KhszpNAZG5HVSOD071x7iLzXRXAwx2jsR2FekfD7Xhp2qRW905iNzhCr8JICeAx7Efwt+decsS4yvU+E9WrSfs37Pc8Y8UeH5LC9WxiUFbIsr5GCztlSQBwOea09O+EvifUYxqOnaa1xazQx7JFdMNjGf4uueua7j4kpAPGN2sCltznCr8xDBRnKjnHvXL2fxb1v4fWY0fT7SC6t5WaYNIzZBbGQNpxjv+dejGq5VOWOz2PNx1NcirddLnk/iZJdIvZLaZPLuLRmt2U4JLDqDjjisDTJb3V9Us9MRVVrqeOEEA/xsF/rVLUtSn1XULnULhyz3ErykHnDOSxx7ZNeifCHSUv/AB/pk0xK22nbr6dv7qQKW/8AQsV6UYJK7PJlipKXJSdke2eK4/Dvg7W202JXWzgNusznayIbgkIOCCMYJwRnaM15lq3hfWb3xHdeF9F02SS72/aUI2gPBkYZWYgEAkCk8e3c934SGsXX+u8T6vPckHqIbVfLjHQHALGsWP4yeK5fEtj4lvFgklsbM2EMaqUQRkDknJYtkA5J61gsMoy5omlTGSlHkky74j8K+KvDFul5rtk9ks527nZCCwXJ+6xPQVwWm30lzr9lBp6NcTyTokeBjLMcYFd18TPGsvivTdKllmJkYs8qg4wwVVBI9wPzrK+D0EH/AAsDTr+4wYtOSe9YHp/o8LuP1ApQinFyaL9tKlJQha76+p6R4sstXi8F6hrMZKR6Zc/YpRgh95IVwMgDbz1zXg+mQx6pd29rC7faZ2WJIzgZZjgAE8dfU19N6g91d/ALW9WvW3S6lqvnEnJOWmTOPxFfJYuFtLqKaE4eF1kB91IIooUoqL5TbHYica0qc0mke3w/BH4kRzoy6Q7rtDSASw5U5OMjf0Nav/CnfiF/0BZf+/sX/wAXWxd/GfX9H1CDWo7ZJWvrRWKSlhH84XlMHJC42jt175pv/DSPiX/oF2f/AH1J/jSSnPVo5nNUfciz/9HxnxBo0NzcedpqRRwIM7048wHkHaOAR0rd8O2N6qLpoEcq9FyuTnOTWVLa3lvpjI8Tr5akHg4rufBKlj5w6r3+pA/rXs4hyVOzPVoU6KfNBasdcaNPpIE17Ak0anIxwqn1bH9eKa+oeZG2yEkEZVSVUZ7DGT/IV6W5LLcbvmx8wz6EAiuV0vRbO5u5PPi3hVyOvBJ9q8rn7npWOs074pHT9Gi8vQkOphQHlyEjPboAWzxyOn8q8C8VWn/CTXzySQxWrsWkIj6ZY5PHbPoK9o1nTLGHSikaCOQH5D3yOa8Av5vs+rywqePKDL9Cc130aspO19jlWGoq6ktzoj4P8G6h4fhImt9L1S3zEfmYiZ0AOX3E43ZHI4FdH4Yg8N+E/CGqx22oxXHiDVoxbmGIMfJiz84LjgnGc4J59a8tgLSebI/WRiR9OlPg0+/uGH2WItnIGO9ehGU7Wb3PHq0YObjThsd98Tk8OXXgPw0ui6na3E2mIUnt42xIplwWO0gE/N14rxKy8NzX1p9pSRQuSenIwe9a11oOuG5BewnwvQhCR+grr/C3h+STUxFrUdxbWMiPuKoQchTt5PAycV3SjUjTvZ/cc2HjRdZqstDz/UNB1aO2ja5m8yGLhFJ4UH0rrPh/ZrbR67qEzKph054kJYDLzukeB6nbuOPQV6HrPhbRJITb2/nzqZHJLOcoiwllxgYf58LjGTnHatS08F+BDqyxzPOtuttvUh3X96WwBkr1K9R/Kub2s7NOL+46Jxw1OvCdPZWe5fufEXhCX4Kjwo96h1VZjKbYBi2fOLDJxtHy89a+XLjTFvNXS0tv3QfjJHGa99h8G+FrSxje6maO6mkJcvOqhYyGxuBJYHcBk4PXpWLL4d8K3N7ezadqEcawMjWxadDu+UM3ucnI9jgZ5JWYVFFtakVkqlZ1JrSTv8rnn0PhO5jdYZbgv8mFzzhQeg54GTVv/hEZP+ew/KvZde0rwvBeWUeh33mP5B84sdw38dD09elZn9nx/wDP2n5f/Xp+1m9UetDDYOV7RP/S0NfjLaJfjnmB/wCVcX4GcC2vkIJKKhG0E4+YEk46AY616TqoaPTLl9udsZOPWvKtMuEiuZUZDH52SGU4OcYIzx1HFfcZ0udQXkedlMnSbPUkRjAjn/lrApP4ZqXQozH5kx48xuPoOlcZBrlxppEEqG4s1QBlZxvVR1CMf5HivZPD2s/CfV1iibUJrOZwAIrphEAT6MAUP/fVfJyw8kfVLHU7XZ5n4ruMTW9vnqxJ/MCvnbxUVh1qPHBRGQn8Tiva/ifLp8Xi0WWj3LrHaPtdw27eTjjI4OK8i125led4ng87d85Y9f0GK6sPScZq5hiMUnTUo9zn7DNyAg4KL1zXXeGrqeHXrOyB+RpQPzrmLSVVUoIcAns2CP8AGun8KhZvElorLnyiXDD2/wD1169G/tYrzR5M6z5Zzvq00e6dDjNammXCWF0l3InmqoYbeD1BA61m4y1Of+7X6DUSqQcJbM+HjHlfMdZJ4gsJoJ4o7Pa0pLBsLxkDg457dRUdzr9nNcrOtmq4wCCBjAbPGMdgK5pQypgDrUW3nk15kcsw8Xon97Ol1pvT9DjvF3jLR9G1DT4Z7FpJoE87ICbDkuMENk4zg/hzngDlbbxdoLWd6V07m8jMasEQDcURS5UHCszJkheOeOah+IMMf9swyttY/ZgAD1++34Vy2nvHHMsSxqy9cE+1fLY6jBV52ufXYL20qNO6Vl6D0jkW4SXyywUAZFav2gf88m/I100F7/ov2dNORgTnIz/hTfMf/oGj9f8ACuLQ9n6zUTfuL70f/9PtYwLx/s1z88UnysvTIPuMGtJPB/hxzlrMf99yf/FVnWX/AB9R/Wu2ir7qtq9RUNL2MM+CvDEyMJbLdkYOZJP/AIqoz8PfB+wL/Zwx/wBdJP8A4uuuT7jfSpW+6K5oxV9joqyai7M4i1+H/g+RZVfTlOyQqvzyZAHTndmrifDTwOIzjS04I/5aSf8AxddNY/8ALf8A66t/StRf9WfqK0cVbY5JN6HmqfDTwNn/AJBScnJ+eTt/wKo/+ED8JaZMbqw05IpVBwwdzj82Nd8v3h+NUL/7r/StbJO6ONvocnHptkeTH69z/jVq10nT5buKOSLKs6gjc3Qn61JF0/Ortj/x/Q/9dF/mK6vbVOV+8/vMI04X2IPEmj6dYXqwWkRjQoDjcx5P1JrDWwtD1T/x4/411/jD/kJJ/wBc1rnEow9ao6UW5P7y3ThfZGfJ4O8NaqwudQslmkUbAxZx8vXHDDvTl+Hvg6JvMj05VYEEESSf/F10ln/q/wAavt0NZT1d2d1OKUVZHLt4a0WNPkt8dvvuf5tUX/CP6P8A8+4/76b/ABropfufjVaseVdimkf/2Q==",
                                    'image_size' => '4222',
                                ]);
                                break;
                            case "语音":
                                $api->sendMsg($receiver->getFromUser(), [
                                    'voice' => "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",
                                    'voice_size' => '3800',
                                    'time' => '4000',
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
