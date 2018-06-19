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

    const MSG_TEXT = 1;
    const MSG_IMAGE = 3;
    const MSG_VOICE = 34;
    const MSG_HEAD_BUFF = 35;
    const MSG_FRIEND_REQUEST = 37;
    const MSG_SHARE_CARD = 42;
    const MSG_VIDEO = 43;
    const MSG_FACE = 47;
    const MSG_LOCATION = 48;
    const MSG_APP_MSG = 49;
    const MSG_CALL_PHONE = 50;
    const MSG_STATUS_PUSH = 51;
    const MSG_TELL_PUSH = 52;
    const MSG_TELL_INVITE = 53;
    const MSG_SMALL_VIDEO = 62;
    const MSG_TRANSFER = 2000;
    const MSG_RED_PACKET = 2001;
    const MSG_INVITE_ROOM = 3000;
    const MSG_SYSTEM = 9999;
    const MSG_WECHAT_PUSH = 10000;
    const MSG_CALLBACK = 10002;

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
            throw new RequestException("接收数据解析失败，可能是服务端事件通知推送异常了", -1);
        }
        !empty($config['secret']) && $this->app_secret = $config['secret'];
        if ($this->getEventType() == 'push') {
            $this->msg = $this->response['data'][0];
            if (empty($this->msg['msg_id'])) {
                throw new RequestException("用户消息通知解析失败，可能是该用户有大量未读的消息", -1);
            }
            $this->setParams();
        }
    }

    /**
     * 获取原始字符串
     * @return string
     */
    public function getOriginStr()
    {
        return urldecode(file_get_contents('php://input'));
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
    public function getParams()
    {
        return $this->params;
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
        if ($this->response['event'] != 'login_success') {
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
        return !empty($this->response['event']) ? $this->response['event']: '';
    }

    /**
     * 获取消息类型
     * @return bool
     */
    public function getMsgType()
    {
        return $this->params['sub_type'];
    }

    /**
     * 获取消息内容
     * @return mixed
     */
    public function getContent()
    {
        return $this->params['content'];
    }

    /**
     * 获取消息来源类型 1好友 2群聊
     * @return bool|int
     */
    public function getMsgFromType()
    {
        return $this->params['from_type'];
    }

    /**
     * 获取xml参数
     * @return mixed
     */
    public function getXmlParams()
    {
        return $this->params['params'];
    }

    /**
     * 获取消息ID
     * @return mixed
     */
    public function getMsgId()
    {
        return $this->params['msg_id'];
    }

    /**
     * 获取消息来源
     * @return mixed
     */
    public function getFromUser()
    {
        return $this->params['from_user'];
    }

    /**
     * 是否是@我的消息
     * @return bool
     */
    public function isAtMe()
    {
        if (!empty($this->msg['description']) && strpos($this->msg['description'], "在群聊中@了你") !== false) {
            return true;
        }
        return false;
    }

    /**
     * 设置参数，处理些冗余的字符串处理
     */
    private function setParams()
    {
        $this->params['from_type'] = 1;
        if (!empty($this->msg['from_user'])) {
            /** 1好友 2群聊 3公众号 4微信 */
            strpos($this->msg['from_user'], "@chatroom") !== false && $this->params['from_type'] = 2;
            strpos($this->msg['from_user'], "gh_") !== false && $this->params['from_type'] = 3;
            strpos($this->msg['from_user'], "gh_") !== false && $this->params['from_type'] = 4;
        }
        $this->params['content'] = '';
        $this->params['send_wxid'] = '';
        $this->params['params'] = [];
        if (!empty($this->msg['content'])) {
            /** 分离发送消息的内容和微信ID */
            $send_wxid = strstr($this->msg['content'], ":\n", true);
            $content = strstr($this->msg['content'], ":\n", false);
            $this->params['content'] = $content;
            $this->params['send_wxid'] = $send_wxid;
            /** 处理消息中的xml数据 */
            if (strpos($content, "</") !== false) {
                $xml = str_replace(["\n", "\t"], '', $content);
                $this->params['params'] = json_decode(json_encode(simplexml_load_string($xml)));
            }
            /** 处理at_user数据 */
            if (strpos($this->msg['msg_source'], "atuserlist") !== false) {
                $at_user = strstr($this->msg['msg_source'], '<atuserlist>', false);
                $at_user = strstr($at_user, '</atuserlist>', true);
                $this->params['at_users'] = explode(',', $at_user);
            }
            /** 原始数据赋值 */
            $this->params['msg_id'] = $this->msg['msg_id'];
            $this->params['msg_source'] = $this->msg['msg_source'];
            $this->params['description'] = $this->msg['description'];
            $this->params['from_user'] = $this->msg['from_user'];
            $this->params['msg_type'] = $this->msg['msg_type'];
            $this->params['sub_type'] = $this->msg['sub_type'];
            $this->params['timestamp'] = $this->msg['timestamp'];
            $this->params['to_user'] = $this->msg['to_user'];
            $this->params['uin'] = $this->msg['uin'];
            $this->params['continue'] = $this->msg['continue'];
        }
    }

}