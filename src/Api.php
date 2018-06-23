<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/6/11
 * Time: 下午10:09
 */

namespace PadChat;

use GuzzleHttp\Client as HttpClient;

class Api
{
    /**
     * 默认请求URI
     * @var string
     */
    protected $base_uri = "https://wxapi.fastgoo.net/";

    /**
     * 请求超时时间
     * @var float|int
     */
    protected $timeout = 30.0;

    /**
     * HTTP类
     * @var HttpClient|null
     */
    protected $client = null;

    protected $config = [];

    /**
     * 初始化，如果本地化部署需要用户自己填写IP
     * Api constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        !empty($config['host']) && $this->base_uri = $config['host'];
        !empty($config['timeout']) && $this->timeout = $config['timeout'];
        $this->config = $config;
        $this->client = new HttpClient();
    }

    /**
     *
     * @param $wx_user
     */
    public function setWxHandle($wx_user)
    {
        $this->config['wx_user'] = $wx_user;
    }

    /**
     * 初始化微信实例，传入回调地址
     * @param $callback_url
     * @return mixed
     * @throws RequestException
     */
    public function init($callback_url)
    {
        return $this->post(__FUNCTION__, compact('callback_url'));
    }

    /**
     * 获取登录二维码
     * @return mixed
     * @throws RequestException
     */
    public function getLoginQrcode()
    {
        $res = $this->post(__FUNCTION__);
        !empty($res['data']['url']) && $res['data']['url'] = $this->base_uri . $res['data']['url'];
        return $res;
    }

    /**
     * 获取二维码的扫描状态
     * @return mixed
     * @throws RequestException
     */
    public function getQrcodeStatus()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 发送消息
     * 文本消息 $content 为字符串文字
     * 图片消息 $content['image'](base64编码) $content['image_size'](图片大小)
     * 语音消息 $content['voice'](base64编码) $content['voice_size'](语音大小)   silk格式
     * 分享名片 $content['contact_wxid']   $content['contact_name']
     * app消息 $content['app_msg'] xml格式字符串，主要用于发送链接
     * @param $user
     * @param $content
     * @param array $at_user
     * @return mixed
     * @throws RequestException
     */
    public function sendMsg($user, $content, $at_user = [])
    {
        if (!$user && !$content) {
            throw new RequestException('user或content不能为空', -1);
        }
        if (!empty($content['title']) && !empty($content['desc']) && !empty($content['url']) && !empty($content['img'])) {
            $content['app_msg'] = sprintf("<appmsg appid='' sdkver=''><title>%s</title><des>%s</des><action>view</action><type>5</type><showtype>0</showtype><content></content><url>%s</url><thumburl>%s</thumburl></appmsg>", $content['title'], $content['desc'], $content['url'], $content['img']);
        }
        $req = [];
        if (is_array($content)) {
            $req = $content;
        } else {
            $req['content'] = $content;
        }
        foreach ($req as $key => &$val) {
            if (in_array($key, ['content', 'contact_name'])) {
                $val = urlencode($val);
            }
        }
        $req['user'] = $user;
        $req['at_user'] = $at_user;
        return $this->post(__FUNCTION__, $req);
    }

    /**
     * 群发消息
     * @param $users
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function massMsg($users, $content)
    {
        $users = json_encode($users);
        $content = urlencode($content);
        return $this->post(__FUNCTION__, compact('users', 'content'));
    }

    /**
     * 获取已登录的微信用户信息
     * @return mixed
     * @throws RequestException
     */
    public function getMyInfo()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 获取登录token
     * @return mixed
     * @throws RequestException
     */
    public function getLoginToken()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 获取wx_data数据
     * @return mixed
     * @throws RequestException
     */
    public function getWxData()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 获取消息图片
     * @param $image 消息字符串
     * @return mixed
     * @throws RequestException
     */
    public function getMsgImage($image)
    {
        return $this->post(__FUNCTION__, compact('image'));
    }

    /**
     * 获取消息视频
     * @param $video 消息字符串
     * @return mixed
     * @throws RequestException
     */
    public function getMsgVideo($video)
    {
        return $this->post(__FUNCTION__, compact('video'));
    }

    /**
     * 获取消息语音
     * @param $voice 消息字符串
     * @return mixed
     * @throws RequestException
     */
    public function getMsgVoice($voice)
    {
        return $this->post(__FUNCTION__, compact('voice'));
    }

    /**
     * 登录（token + wx_data） (username + password + wx_data) (phone + password + wx_data)
     * @param $params
     * @return mixed
     * @throws RequestException
     */
    public function login($params)
    {
        return $this->post(__FUNCTION__, $params);
    }

    /**
     * 退出登录实例
     * @return mixed
     * @throws RequestException
     */
    public function logout()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 关闭微信实例
     * @return mixed
     * @throws RequestException
     */
    public function close()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 同步通讯录
     * 获取continue字段为0则不需要再同步，需要过滤掉非通讯录数据
     * @return mixed
     * @throws RequestException
     */
    public function syncContact()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 通过wxid获取联系人信息 (联系人相关)
     * @param $wx_id
     * @return mixed
     * @throws RequestException
     */
    public function getContact($wx_id)
    {
        return $this->post(__FUNCTION__, ['user' => $wx_id]);
    }

    /**
     * 通过wxid搜索联系人信息，未加好友可以获取信息 (联系人相关)
     * @param $wx_id
     * @return mixed
     * @throws RequestException
     */
    public function searchContact($wx_id)
    {
        return $this->post(__FUNCTION__, ['user' => $wx_id]);
    }

    /**
     * 删除好友 (联系人相关)
     * @param $wx_id
     * @return mixed
     * @throws RequestException
     */
    public function deleteContact($wx_id)
    {
        return $this->post(__FUNCTION__, ['user' => $wx_id]);
    }

    /**
     * 获取二维码 (自己的、群聊) (联系人相关)
     * @param $wx_id
     * @return mixed
     * @throws RequestException
     */
    public function getContactQrcode($wx_id)
    {
        return $this->post(__FUNCTION__, ['user' => $wx_id]);
    }

    /**
     * 设置用户头像
     * @param $image
     * @param int $image_size
     * @return mixed
     * @throws RequestException
     */
    public function setHeadImage($image, $image_size = 0)
    {
        return $this->post(__FUNCTION__, compact('image', 'image_size'));
    }

    /**
     * 同意好友申请
     * @param $stranger
     * @param $ticket
     * @return mixed
     * @throws RequestException
     */
    public function acceptUser($stranger, $ticket)
    {
        return $this->post(__FUNCTION__, compact('stranger', 'ticket'));
    }

    /**
     * 添加好友
     * stranger v2 type desc
     * @param $params
     * @return mixed
     * @throws RequestException
     */
    public function addUser($params)
    {
        !empty($params['desc']) && $params['desc'] = urlencode($params['desc']);
        return $this->post(__FUNCTION__, $params);
    }

    /**
     * 退出群聊
     * @param $room
     * @return mixed
     * @throws RequestException
     */
    public function quitRoom($room)
    {
        return $this->post(__FUNCTION__, compact('room'));
    }

    /**
     * 删除群成员
     * @param $room
     * @param $user
     * @return mixed
     * @throws RequestException
     */
    public function deleteRoomMember($room, $user)
    {
        return $this->post(__FUNCTION__, compact('room', 'user'));
    }

    /**
     * 获取群成员列表
     * @param $room
     * @return mixed
     * @throws RequestException
     */
    public function getRoomMembers($room)
    {
        return $this->post(__FUNCTION__, compact('room'));
    }

    /**
     * 创建群聊
     * @param $members
     * @return mixed
     * @throws RequestException
     */
    public function createRoom($members)
    {
        $members = json_encode($members);
        return $this->post(__FUNCTION__, compact('members'));
    }

    /**
     * 设置群公告
     * @param $room
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function setRoomAnnouncement($room, $content)
    {
        $content = urlencode($content);
        return $this->post(__FUNCTION__, compact('room', 'content'));
    }

    /**
     * 设置群名称
     * @param $room
     * @param $name
     * @return mixed
     * @throws RequestException
     */
    public function setRoomName($room, $name)
    {
        $name = urlencode($name);
        return $this->post(__FUNCTION__, compact('room', 'name'));
    }

    /**
     * 邀请好友入群
     * @param $room
     * @param $user
     * @param int $type 1直接邀请 2发送链接
     * @return mixed
     * @throws RequestException
     */
    public function addRoomMember($room, $user, $type = 2)
    {
        return $this->post(__FUNCTION__, compact('room', 'user', 'type'));
    }

    /**
     * 查看红包转账信息
     * 这里的content是$this->msg['content']
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function queryTransfer($content)
    {
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 接受转账
     * 这里的content是$this->msg['content']
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function acceptTransfer($content)
    {
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 查看红包
     * 这里的content是$this->msg['content']
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function queryRedPacket($content)
    {
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 接受红包(主要是为了拿到key然后去领取红包)
     * 这里的content是$this->msg['content']
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function receiveRedPacket($content)
    {
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 领取红包
     * 这里的content是$this->msg['content']
     * key是需要从接受红包返回参数获取
     * @param $content
     * @param $key
     * @return mixed
     * @throws RequestException
     */
    public function openRedPacket($content, $key)
    {
        return $this->post(__FUNCTION__, compact('content', 'key'));
    }

    /**
     * 获取标签列表
     * @return mixed
     * @throws RequestException
     */
    public function getLabelList()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 添加标签
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function addLabel($content)
    {
        $content = urlencode($content);
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 删除标签
     * @param $id
     * @return mixed
     * @throws RequestException
     */
    public function deleteLabel($id)
    {
        return $this->post(__FUNCTION__, compact('id'));
    }

    /**
     * 设置用户标签
     * @param $id
     * @param $user
     * @return mixed
     * @throws RequestException
     */
    public function setLabel($id, $user)
    {
        return $this->post(__FUNCTION__, compact('id', 'user'));
    }

    /**
     * 查看朋友圈
     * @param string $last_id 用作翻页
     * @return mixed
     * @throws RequestException
     */
    public function snsTimeLine($last_id = '')
    {
        return $this->post(__FUNCTION__, compact('last_id'));
    }

    /**
     * 查看好友的朋友圈
     * @param $user
     * @param string $last_id
     * @return mixed
     * @throws RequestException
     */
    public function snsUserPage($user, $last_id = '')
    {
        return $this->post(__FUNCTION__, compact('last_id', 'user'));
    }

    /**
     * 朋友圈上传图片
     * @param $image
     * @param $size
     * @return mixed
     * @throws RequestException
     */
    public function snsUpload($image, $size)
    {
        return $this->post(__FUNCTION__, compact('image', 'size'));
    }

    /**
     * 操作朋友圈动态
     * @param $id 动态ID
     * @param $type 操作类型,1为删除朋友圈，4为删除评论，5为取消赞
     * @param $comment 当type为4时，对应删除评论的id，通过WXSnsObjectDetail接口获取。当type为5时，comment不可用，置为0。
     * @param $comment_type 评论类型,当删除评论时可用，2或者3.(规律未知)
     * @return mixed
     * @throws RequestException
     */
    public function snsOp($id, $type, $comment, $comment_type)
    {
        return $this->post(__FUNCTION__, compact('id', 'type', 'comment', 'comment_type'));
    }

    /**
     * 发布朋友圈
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function snsSendMoment($content)
    {
        $content = urlencode($content);
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 查看动态详情
     * @param $id
     * @return mixed
     * @throws RequestException
     */
    public function snsDetail($id)
    {
        return $this->post(__FUNCTION__, compact('id'));
    }

    /**
     * 朋友圈评论
     * @param $user 对方wxid
     * @param $id 动态id
     * @param $content 回复内容
     * @param int $reply_id 回复其他人的评论
     * @return mixed
     * @throws RequestException
     */
    public function snsComment($user, $id, $content, $reply_id = 0)
    {
        $content = urlencode($content);
        return $this->post(__FUNCTION__, compact('content', 'id', 'user', 'reply_id'));
    }

    /**
     * 打招呼
     * @param $stranger
     * @param string $content
     * @return mixed
     * @throws RequestException
     */
    public function sayHello($stranger, $content = '')
    {
        $content = urlencode($content);
        return $this->post(__FUNCTION__, compact('content', 'stranger'));
    }

    /**
     * 设置备注
     * @param $user
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function setRemark($user, $content)
    {
        $content = urlencode($content);
        return $this->post(__FUNCTION__, compact('content', 'user'));
    }

    /**
     * post 请求
     * @param $url
     * @param $params
     * @return mixed
     * @throws RequestException
     */
    private function post($url, $params = [])
    {
        if (!is_array($params)) {
            throw new RequestException("请求参数必须为数组", -1);
        }
        !empty($this->config['wx_user']) && $params['wx_user'] = $this->config['wx_user'];
        $params['timestamp'] = time();
        $params['sign'] = Util::makeSign($params, !empty($this->config['secret']) ? $this->config['secret'] : '123');
        $response = $this->client->request('POST', $this->base_uri . $url, [
            'form_params' => $params
        ]);

        if ($response->getStatusCode() != 200) {
            throw new RequestException("请求接口失败了，响应状态码：" . $response->getStatusCode(), $response->getStatusCode());
        }
        $ret = $response->getBody()->getContents();
        if (!$ret) {
            throw new RequestException("未收到任何响应信息", -1);
        }
        $resStr = json_decode($ret, true);
        if (!$resStr) {
            throw new RequestException("接口返回的数据非JSON格式：" . $response->getBody()->getContents(), -1);
        }
        return $resStr;
    }
}