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
    protected $base_uri;

    /**
     * 请求超时时间
     * @var float|int
     */
    protected $timeout = 5.0;

    /**
     * HTTP类
     * @var HttpClient|null
     */
    protected $client = null;

    protected $config = [];

    /**
     * 初始化，如果本地化部署需要用户自己填写IP
     * Api constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        if (empty($config['host'])) {
            throw new \Exception("host地址未配置");
        }
        $this->base_uri = $config['host'];
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
     * @param $wx_data
     * @return mixed
     * @throws RequestException
     */
    public function getLoginQrcode($wx_data = '')
    {
        return $this->post(__FUNCTION__, compact('wx_data'));
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
     * 发送文字
     * @param $wxid
     * @param $content
     * @param array $at_user
     * @return mixed
     * @throws RequestException
     */
    public function sendMsg($wxid, $content, $at_user = [])
    {
        return $this->post(__FUNCTION__, compact('wxid', 'content'));
    }

    /**
     * 发送语音
     * @param $wxid
     * @param $voice
     * @return mixed
     * @throws RequestException
     */
    public function sendVoiceMsg($wxid, $voice)
    {
        return $this->post(__FUNCTION__, compact('wxid', 'voice'));
    }

    /**
     * 发送图片
     * @param $wxid
     * @param $image
     * @return mixed
     * @throws RequestException
     */
    public function sendImageMsg($wxid, $image)
    {
        return $this->post(__FUNCTION__, compact('wxid', 'image'));
    }

    /**
     * 发送cdn图片
     * @param $wxid
     * @param array $image
     * @return mixed
     * @throws RequestException
     */
    public function sendCdnImageMsg($wxid, array $image)
    {
        return $this->post(__FUNCTION__, compact('wxid', 'image'));
    }

    /**
     * 发送名片
     * @param $wxid
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function sendContactMsg($wxid, $content)
    {
        return $this->post(__FUNCTION__, compact('wxid', 'content'));
    }

    /**
     * 发送appmsg
     * @param $wxid
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function sendAppMsg($wxid, $content)
    {
        return $this->post(__FUNCTION__, compact('wxid', 'content'));
    }

    /**
     * 发送表情
     * @param $wxid
     * @param array $emoji
     * @return mixed
     * @throws RequestException
     */
    public function sendFaceMsg($wxid, array $emoji)
    {
        return $this->post(__FUNCTION__, compact('wxid', 'emoji'));
    }

    /**
     * 发送cdn视频
     * @param $wxid
     * @param array $video
     * @return mixed
     * @throws RequestException
     */
    public function sendCdnVideoMsg($wxid, array $video)
    {
        return $this->post(__FUNCTION__, compact('wxid', 'video'));
    }

    /**
     * 获取已登录的微信用户信息
     * @return mixed
     * @throws RequestException
     */
    public function getCheck()
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
     * @param $content 消息字符串
     * @return mixed
     * @throws RequestException
     */
    public function getMsgImage($content)
    {
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 获取消息视频
     * @param $content 消息字符串
     * @return mixed
     * @throws RequestException
     */
    public function getMsgVideo($content)
    {
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 获取消息语音
     * @param $content 消息字符串
     * @return mixed
     * @throws RequestException
     */
    public function getMsgVoice($content)
    {
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 登录 (token) (username + password + wx_data)
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
     * 同步消息
     * 获取continue字段为0则不需要再同步
     * @return mixed
     * @throws RequestException
     */
    public function syncMsg()
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
     * @param $users
     * @param string $chatroom_id
     * @return mixed
     * @throws RequestException
     */
    public function getContact($users, $chatroom_id = '')
    {
        return $this->post(__FUNCTION__, compact('users', 'chatroom_id'));
    }

    /**
     * 通过wxid搜索联系人信息，未加好友可以获取信息 (联系人相关)
     * @param $user
     * @return mixed
     * @throws RequestException
     */
    public function searchContact($user)
    {
        return $this->post(__FUNCTION__, compact('user'));
    }

    /**
     * 删除好友 (联系人相关)
     * @param $wxid
     * @return mixed
     * @throws RequestException
     */
    public function delUser($wxid)
    {
        return $this->post(__FUNCTION__, compact('wxid'));
    }

    /**
     * 获取群聊二维码接口
     * @param $room
     * @return mixed
     * @throws RequestException
     */
    public function getRoomQrcode($room)
    {
        return $this->post(__FUNCTION__, compact('room'));
    }

    /**
     * 获取群聊二维码接口
     * @param $room
     * @return mixed
     * @throws RequestException
     */
    public function getRoomDetail($room)
    {
        return $this->post(__FUNCTION__, compact('room'));
    }

    /**
     * 设置用户头像
     * @param $image
     * @param int $image_size
     * @return mixed
     * @throws RequestException
     */
    public function setHeadImage($image)
    {
        return $this->post(__FUNCTION__, compact('image'));
    }

    /**
     * 同意好友申请
     * @param $stranger
     * @param $ticket
     * @return mixed
     * @throws RequestException
     */
    public function acceptUser($stranger, $ticket, $sence = 12)
    {
        return $this->post(__FUNCTION__, ['v1' => $stranger, 'v2' => $ticket, 'sence' => $sence]);
    }

    /**
     * 退出群聊
     * @param $room
     * @return mixed
     * @throws RequestException
     */
    public function exitRoom($room)
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
    public function delRoomMember($room, $user)
    {
        return $this->post(__FUNCTION__, compact('room', 'user'));
    }

    /**
     * 创建群聊
     * @param $members
     * @return mixed
     * @throws RequestException
     */
    public function createRoom($members)
    {
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
        return $this->post(__FUNCTION__, compact('room', 'name'));
    }

    /**
     * 邀请好友入群
     * @param $room
     * @param $users
     * @return mixed
     * @throws RequestException
     */
    public function addRoomMember($room, $users)
    {
        return $this->post(__FUNCTION__, compact('room', 'users'));
    }

    /**
     * 邀请入群，链接
     * @param $room
     * @param $user
     * @return mixed
     * @throws RequestException
     */
    public function inviteRoomMember($room, $user)
    {
        return $this->post(__FUNCTION__, compact('room', 'user'));
    }

    /**
     * 同意进群申请
     * @param $room
     * @param $user
     * @param $ticket
     * @param $invite_user
     * @return mixed
     * @throws RequestException
     */
    public function agreeInviteRoom($room, $user, $ticket, $invite_user)
    {
        return $this->post(__FUNCTION__, compact('room', 'user', 'ticket', 'invite_user'));
    }

    /**
     * 设置群验证
     * @param $room
     * @param bool $status
     * @return mixed
     * @throws RequestException
     */
    public function setRoomVerify($room, $status = true)
    {
        return $this->post(__FUNCTION__, compact('room', 'status'));
    }

    /**
     * 设置群聊置顶
     * @param $room
     * @param bool $is_top
     * @return mixed
     * @throws RequestException
     */
    public function setRoomTop($room, $is_top = true)
    {
        return $this->post(__FUNCTION__, compact('room', 'is_top'));
    }

    /**
     * 保存群聊到通讯录
     * @param $room
     * @param bool $is_save
     * @return mixed
     * @throws RequestException
     */
    public function setRoomSave($room, $is_save = true)
    {
        return $this->post(__FUNCTION__, compact('room', 'is_save'));
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
     * @param $user
     * @param $transfer_id
     * @param $invalidtime
     * @return mixed
     * @throws RequestException
     */
    public function acceptTransfer($user, $transfer_id, $invalidtime)
    {
        return $this->post(__FUNCTION__, compact('user', 'transfer_id', 'invalidtime'));
    }

    /**
     * 查看红包
     * @param $user
     * @param $nativeurl
     * @return mixed
     * @throws RequestException
     */
    public function getRedPacketInfo($user, $nativeurl)
    {
        return $this->post(__FUNCTION__, compact('user', 'nativeurl'));
    }

    /**
     * 获取红包领取列表
     * @param $user
     * @param $nativeurl
     * @return mixed
     * @throws RequestException
     */
    public function getRedPacketTakeList($user, $nativeurl)
    {
        return $this->post(__FUNCTION__, compact('user', 'nativeurl'));
    }

    /**
     * 领取红包
     * @param $user
     * @param $nativeurl
     * @return mixed
     * @throws RequestException
     */
    public function takeRedPacket($user, $nativeurl)
    {
        return $this->post(__FUNCTION__, compact('user', 'nativeurl'));
    }

    /**
     * 创建自定义收款码
     * @param $amount
     * @param $desc
     * @return mixed
     * @throws RequestException
     */
    public function createPaymentQrcode($amount, $desc)
    {
        return $this->post(__FUNCTION__, compact('amount', 'desc'));
    }

    /**
     * 转账给好友
     * @param $user
     * @param $amount
     * @param $content
     * @param $password
     * @return mixed
     * @throws RequestException
     */
    public function transferUser($user, $amount, $content, $password)
    {
        return $this->post(__FUNCTION__, compact('user', 'amount', 'content', 'password'));
    }

    /**
     * 发送红包
     * @param $user
     * @param $amount
     * @param $count
     * @param $content
     * @param $password
     * @return mixed
     * @throws RequestException
     */
    public function sendRedPacket($user, $amount, $count, $content, $password)
    {
        return $this->post(__FUNCTION__, compact('user', 'amount', 'count', 'content', 'password'));
    }

    /**
     * 获取标签列表
     * @return mixed
     * @throws RequestException
     */
    public function getLabels()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 添加标签
     * @param $name
     * @return mixed
     * @throws RequestException
     */
    public function addLabel($name)
    {
        return $this->post(__FUNCTION__, compact('name'));
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
     * @param $ids
     * @param $user
     * @return mixed
     * @throws RequestException
     */
    public function setUserLabels($user, $ids)
    {
        return $this->post(__FUNCTION__, compact('ids', 'user'));
    }

    /**
     * 查看朋友圈动态
     * @param string $md5_page
     * @param string $last_id
     * @return mixed
     * @throws RequestException
     */
    public function snsTimeLine($md5_page = '', $last_id = '')
    {
        return $this->post(__FUNCTION__, compact('md5_page', 'last_id'));
    }

    /**
     * 同步朋友圈
     * @return mixed
     * @throws RequestException
     */
    public function syncSns()
    {
        return $this->post(__FUNCTION__);
    }

    /**
     * 查看好友的朋友圈
     * @param $user
     * @param $md5_page
     * @param string $max_id
     * @return mixed
     * @throws RequestException
     */
    public function getUserSns($user, $md5_page, $max_id = '')
    {
        return $this->post(__FUNCTION__, compact('user', 'md5_page', 'max_id'));
    }

    /**
     * 朋友圈上传图片
     * @param $image
     * @return mixed
     * @throws RequestException
     */
    public function uploadImageSns($image)
    {
        return $this->post(__FUNCTION__, compact('image'));
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
    public function sendSns($content)
    {
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
        $content = rawurlencode($content);
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
        $content = rawurlencode($content);
        return $this->post(__FUNCTION__, compact('content', 'stranger'));
    }

    /**
     * 设置备注
     * @param $user
     * @param $remark
     * @return mixed
     * @throws RequestException
     */
    public function setUserRemark($user, $remark)
    {
        return $this->post(__FUNCTION__, compact('name', 'remark'));
    }

    /**
     * 搜索公众号
     * @param $keyword
     * @return mixed
     * @throws RequestException
     */
    public function searchMp($keyword)
    {
        return $this->post(__FUNCTION__, compact('keyword'));
    }

    /**
     * 执行公众号菜单
     * @param $user
     * @param $id
     * @param $key
     * @return mixed
     * @throws RequestException
     */
    public function operateSubscription($user, $id, $key)
    {
        return $this->post(__FUNCTION__, compact('user', 'id', 'key'));
    }

    /**
     * 获取公众号信息
     * @param $user
     * @return mixed
     * @throws RequestException
     */
    public function getSubscriptionInfo($user)
    {
        return $this->post(__FUNCTION__, compact('user'));
    }

    /**
     * 获取请求TOKEN
     * @param $user
     * @param $url
     * @param $key
     * @return mixed
     * @throws RequestException
     */
    public function getRequestToken($user, $url, $key)
    {
        return $this->post(__FUNCTION__, compact('user', 'url', 'key'));
    }

    /**
     * 请求URL
     * @param $url
     * @param $key
     * @param $uin
     * @return mixed
     * @throws RequestException
     */
    public function requestUrl($url, $key, $uin)
    {
        return $this->post(__FUNCTION__, compact('uin', 'url', 'key'));
    }

    /**
     * 同步收藏列表
     * @param string $key 如果是翻页的话需要传这个key
     * @return mixed
     * @throws RequestException
     */
    public function syncFav($key = '')
    {
        return $this->post(__FUNCTION__, compact('key'));
    }

    /**
     * 取消收藏
     * @param $id
     * @return mixed
     * @throws RequestException
     */
    public function deleteFav($id)
    {
        return $this->post(__FUNCTION__, compact('id'));
    }

    /**
     * 添加收藏
     * @param $content json消息体
     * @return mixed
     * @throws RequestException
     */
    public function addFav($content)
    {
        return $this->post(__FUNCTION__, compact('content'));
    }

    /**
     * 获取收藏详情
     * @param $id
     * @return mixed
     * @throws RequestException
     */
    public function getFav($id)
    {
        return $this->post(__FUNCTION__, compact('id'));
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

        $requestData = ['body' => json_encode($params)];

        /** 处理图片上传机制 */
        if (!empty($params['image'])) {
            if (file_exists($params['image'])) {
                $body = fopen($params['image'], 'r');
                unset($requestData['body']);
                $requestData['multipart'][] = ['name' => 'image', 'contents' => $body];
                foreach ($params as $key => $value) {
                    $requestData['multipart'][] = ['name' => $key, 'contents' => $value];
                }
            }
        }

        /** 处理语音上传机制 */
        if (!empty($params['voice'])) {
            if (file_exists($params['voice'])) {
                unset($requestData['body']);
                $body = fopen($params['voice'], 'r');
                $requestData['multipart'][] = ['name' => 'voice', 'contents' => $body];
                foreach ($params as $key => $value) {
                    $requestData['multipart'][] = ['name' => $key, 'contents' => $value];
                }
            }
        }
        $response = $this->client->request('POST', $this->base_uri . $url, $requestData);
        if ($response->getStatusCode() != 200) {
            throw new RequestException("请求接口失败了，响应状态码：" . $response->getStatusCode(), $response->getStatusCode());
        }
        $ret = $response->getBody()->getContents();
        if (!$ret) {
            throw new RequestException("未收到任何响应信息", -1);
        }
        $resStr = json_decode(mb_convert_encoding($ret, 'utf-8', 'utf-8'), true);
        if (!$resStr) {
            throw new RequestException("接口返回的数据非JSON格式：" . $response->getBody()->getContents(), -1);
        }
        return $resStr;
    }
}