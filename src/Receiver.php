<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/6/15
 * Time: 下午11:12
 */

namespace PadChat;

class Receiver
{
    /**
     * 响应数据
     */
    protected $response;

    protected $msg;

    protected $originMsg;

    /**
     * 微信实例
     */
    protected $wx_user;

    /**
     * 秘钥，签名验证用的
     */
    protected $app_secret;

    /**
     * 事件类型
     */
    protected $type;

    /**
     * 处理后的参数容器
     */
    protected $params = [];

    /**
     * 登录成功事件
     */
    const LOGIN_SUCCESS = 'login_success';

    /**
     * 微信通知推送事件
     */
    const PUSH_MESSAGE = 'push';

    //文字消息
    const MSG_TEXT = 1;
    //图片消息
    const MSG_IMAGE = 3;
    //语音消息
    const MSG_VOICE = 34;
    //布吉岛
    const MSG_HEAD_BUFF = 35;
    //好友请求
    const MSG_FRIEND_REQUEST = 37;
    //名片消息
    const MSG_SHARE_CARD = 42;
    //视频消息
    const MSG_VIDEO = 43;
    //表情消息
    const MSG_FACE = 47;
    //定位消息
    const MSG_LOCATION = 48;
    //app
    const MSG_APP_MSG = 49;
    const MSG_CALL_PHONE = 50;
    const MSG_STATUS_PUSH = 51;
    const MSG_TELL_PUSH = 52;
    const MSG_TELL_INVITE = 53;
    const MSG_SMALL_VIDEO = 62;
    const MSG_TRANSFER = 2000;
    const MSG_RED_PACKET = 2001;
    const MSG_SHARE_LINK = 2005;
    const MSG_SHARE_FILE = 2006;
    const MSG_SHARE_COUPON = 2016;
    const MSG_INVITE_ROOM = 3000;
    const MSG_SYSTEM = 9999;
    const MSG_WECHAT_PUSH = 10000;
    //邀请好友加入群聊
    const MSG_CALLBACK = 10002;
    const MSG_INVITE_USER = 10010;

    /**
     * 初始化，如果没有解析成功，则抛出异常
     * Receiver constructor.
     * @param array $config
     * @throws RequestException
     */
    public function __construct($config = [])
    {
        $this->response = json_decode(urldecode(file_get_contents('php://input')), true);
        if (!$this->response) {
            throw new RequestException("接收数据解析失败，可能是服务端事件通知推送异常了。" . file_get_contents('php://input'), -1);
        }
        !empty($config['secret']) && $this->app_secret = $config['secret'];
        if ($this->getEventType() == 'push') {
            if (!empty($this->response['data']) && is_array($this->response['data'])) {
                $arr = [];
                foreach ($this->response['data'] as $key => $val) {
                    $arr[lcfirst($key)] = $val;
                }
                $this->response['data'] = $arr;
            }
            $this->msg = $this->originMsg = $this->response['data'];
            $this->setParams();
        }
    }

    /**
     * 获取原始字符串
     * @return string
     */
    public function getOriginStr()
    {
        return file_get_contents('php://input');
    }

    /**
     * 获取原始的第一条消息
     * @return mixed
     */
    public function getOriginMsg()
    {
        return $this->originMsg;
    }

    /**
     * 获取响应数据
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 获取参数
     * @return array
     */
    public function getMsgParams()
    {
        return $this->msg;
    }

    /**
     * 获取当前事件推送的微信实例
     * @return mixed
     */
    public function getWxUser()
    {
        $this->wx_user = $this->response['wx_user'];
        return $this->wx_user;
    }

    /**
     * 获取登录成功的用户信息
     * @return array
     */
    public function getLoginInfo()
    {
        if ($this->response['event_type'] != 'login_success') {
            return [];
        }
        return $this->response['data'];
    }

    /**
     * 获取事件类型 push login_success
     * @return mixed
     */
    public function getEventType()
    {
        return !empty($this->response['event_type']) ? $this->response['event_type'] : '';
    }

    /**
     * 获取消息类型
     * @return bool
     */
    public function getMsgType()
    {
        return $this->msg['msgType'];
    }

    /**
     * 获取消息接受的用户，一般是自己
     * @return bool
     */
    public function getToUserName()
    {
        return $this->msg['toUserName'];
    }

    /**
     * 获取消息内容
     * @return mixed
     */
    public function getContent()
    {
        return $this->msg['content'];
    }

    /**
     * 获取消息来源类型 1好友 2群聊
     * @return bool|int
     */
    public function getMsgFromType()
    {
        return $this->msg['from_type'];
    }

    /**
     * 获取xml参数
     * @return mixed
     */
    public function getXmlParams()
    {
        return $this->msg['params'];
    }

    /**
     * 获取消息ID
     * @return mixed
     */
    public function getMsgId()
    {
        return $this->msg['msgId'];
    }

    /**
     * 获取消息来源
     * @return mixed
     */
    public function getFromUser()
    {
        return $this->msg['fromUserName'];
    }

    /**
     * 是否是@我的消息
     * @return bool
     */
    public function isAtMe()
    {
        if (!empty($this->msg['pushContent']) && strpos($this->msg['pushContent'], "在群聊中@了你") !== false) {
            return true;
        }
        return false;
    }

    /**
     * 设置参数，处理些冗余的字符串处理
     */
    private function setParams()
    {
        $this->msg['from_type'] = 1;
        $this->msg['send_wxid'] = '';
        $this->msg['params'] = [];
        $this->msg['at_users'] = [];

        /** 1好友 2群聊 3公众号 4微信 */
        if (!empty($this->msg['fromUserName'])) {
            strpos($this->msg['fromUserName'], "@chatroom") !== false && $this->msg['from_type'] = 2;
            strpos($this->msg['fromUserName'], "gh_") !== false && $this->msg['from_type'] = 3;
            strpos($this->msg['fromUserName'], "gh_") !== false && $this->msg['from_type'] = 4;
        }

        /** 处理消息内容 */
        if (!empty($this->msg['content'])) {
            if ($this->msg['from_type'] == 2 && strpos($this->msg['content'], ":\n") !== false) {
                /** 分离发送消息的内容和微信ID */
                $send_wxid = strstr($this->msg['content'], ":\n", true);
                $content = strstr($this->msg['content'], ":\n", false);
                $content = str_replace(":\n", '', $content);
            } else {
                $content = $this->msg['content'];
                $send_wxid = $this->msg['fromUserName'];
            }
            $this->msg['content'] = $content;
            $this->msg['send_wxid'] = $send_wxid;
            /** 处理at_user数据 */
            if (strpos($this->msg['msgSource'], "atuserlist") !== false) {
                $at_user = strstr($this->msg['msgSource'], '<atuserlist>', false);
                $at_user = strstr($at_user, '</atuserlist>', true);
                $at_user = str_replace(["<atuserlist>"], '', $at_user);
                $this->msg['at_users'] = explode(',', $at_user);
            }

            if (in_array($this->msg['msgType'], [$this::MSG_WECHAT_PUSH, $this::MSG_CALLBACK, 0])) {
                if (strpos($this->msg['content'], '加入了群聊') !== false) {
                    $this->msg['msgType'] = $this::MSG_INVITE_USER;//'邀请\"周先生??\"加入了群聊'
                    $str = strstr($this->msg['content'], '邀请"');
                    $str = strstr($str, "\"加入了群聊", true);
                    $this->msg['invite_name'] = str_replace('邀请"', '', $str);
                }
            }
        }
        $this->msg['params'] = $this->getTransferParams($this->msg['content']);
    }

    /**
     * XML数据处理
     * @param $content
     * @return array
     */
    private function getTransferParams($content)
    {
        if ($this->getMsgType() == $this::MSG_SHARE_CARD) {
            $xmlArr = json_decode(json_encode(simplexml_load_string($content)), true);
            return $xmlArr['@attributes'];
        }
        if ($this->getMsgType() == $this::MSG_FRIEND_REQUEST) {
            $xmlArr = json_decode(json_encode(simplexml_load_string($content)), true);
            return $xmlArr['@attributes'];
        }
        if ($this->getMsgType() == $this::MSG_LOCATION) {
            $xmlArr = json_decode(json_encode(simplexml_load_string($content)), true);
            return $xmlArr['location']['@attributes'];
        }
        if ($this->getMsgType() == $this::MSG_APP_MSG) {
            $p = xml_parser_create();
            xml_parse_into_struct($p, $content, $vals, $index);
            xml_parser_free($p);

            $type = $vals[$index['TYPE'][0]]['value'];
            $this->msg['msgType'] = $type;
            if ($type == 2000) {//转账
                $desc = $vals[$index['PAY_MEMO'][0]]['value'];
                $amount = $vals[$index['FEEDESC'][0]]['value'];
                $title = $vals[$index['TITLE'][0]]['value'];
                $content = $vals[$index['CONTENT'][0]]['value'];
                $url = $vals[$index['URL'][0]]['value'];
                $pay_sub_type = $vals[$index['PAYSUBTYPE'][0]]['value'];
                $transcation_id = $vals[$index['TRANSCATIONID'][0]]['value'];
                $transfer_id = $vals[$index['TRANSFERID'][0]]['value'];
                $invalid_time = $vals[$index['INVALIDTIME'][0]]['value'];
                $transfer_time = $vals[$index['BEGINTRANSFERTIME'][0]]['value'];
                return compact('title', 'content', 'url', 'desc', 'amount', 'pay_sub_type', 'type', 'transcation_id', 'transfer_id', 'transfer_time', 'invalid_time');
            }
            if ($type == 2001) {//红包 (群)收款
                $img = $vals[$index['THUMBURL'][0]]['value'];
                $title = $vals[$index['TITLE'][0]]['value'];
                $content = $vals[$index['RECEIVERTITLE'][0]]['value'];
                $url = $vals[$index['URL'][0]]['value'];
                $template_id = $vals[$index['TEMPLATEID'][0]]['value'];
                $native_url = $vals[$index['NATIVEURL'][0]]['value'];
                /** 1001转账场景 1002红包消息场景 (针对群里面的状态区分) */
                $scene_id = $vals[$index['SCENEID'][0]]['value'];
                $pay_msg_id = $vals[$index['PAYMSGID'][0]]['value'];
                $invalid_time = $vals[$index['INVALIDTIME'][0]]['value'];
                return compact('title', 'content', 'url', 'img', 'template_id', 'native_url', 'scene_id', 'pay_msg_id', 'invalid_time');
            }
            if ($type == 5) {//链接
                $this->msg['msgType'] = $this::MSG_SHARE_LINK;
                $img = $vals[$index['THUMBURL'][0]]['value'];
                $show_type = $vals[$index['SHOWTYPE'][0]]['value'];
                $title = $vals[$index['TITLE'][0]]['value'];
                $content = $vals[$index['DES'][0]]['value'];
                $url = $vals[$index['URL'][0]]['value'];
                return compact('title', 'content', 'img', 'url', 'show_type');
            }
            if ($type == 6) {//文件
                $this->msg['msgType'] = $this::MSG_SHARE_FILE;
                $img = $vals[$index['THUMBURL'][0]]['value'];
                $show_type = $vals[$index['SHOWTYPE'][0]]['value'];
                $title = $vals[$index['TITLE'][0]]['value'];
                $content = $vals[$index['DES'][0]]['value'];
                $url = $vals[$index['URL'][0]]['value'];
                return compact('title', 'content', 'img', 'url', 'show_type');
            }
            if ($type == 16) {//卡券消息
                $this->msg['msgType'] = $this::MSG_SHARE_COUPON;
                $img = $vals[$index['THUMBURL'][0]]['value'];
                $show_type = $vals[$index['SHOWTYPE'][0]]['value'];
                $title = $vals[$index['TITLE'][0]]['value'];
                $content = $vals[$index['DES'][0]]['value'];
                $url = $vals[$index['URL'][0]]['value'];
                return compact('title', 'content', 'img', 'url', 'show_type');
            }
        }
    }

}