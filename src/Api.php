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
     * @param $wx_data
     * @return mixed
     * @throws RequestException
     */
    public function getLoginQrcode($wx_data = '')
    {
        $res = $this->post(__FUNCTION__, compact('wx_data'));
        !empty($res['url']) && $res['url'] = $this->base_uri . $res['url'];
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
     * 发送小程序消息
     * 测试数据
     * [
     * 'app_id' => 'wxd3f6cb54399a8489',
     * 'wx_id' => 'gh_bc33767b4df6',
     * 'icon' => 'http://b.hiphotos.baidu.com/image/pic/item/8326cffc1e178a82a8e4af46fa03738da877e878.jpg',
     * 'title' => '测试标题，测试哈哈哈哈',
     * 'desc' => '这是描述内容',
     * 'url' => 'https://www.zhihu.com/question/23060126/answer/400464283?utm_source=wechat_session&utm_medium=social&utm_oi=977494335025119232',
     * 'path' => '/zhihu/answer.html?id=400464283&utm_source=wechat_session&utm_medium=social&utm_oi=977494335025119232',
     * 'statextstr' => 'GhQKEnd4ZDNmNmNiNTQzOTlhODQ4OQ==',
     * 'attach' => [
     * 'url' => '30590201000452305002010002049a53ddb902032f56c10204a0e5e77302045b53f90c042b777875706c6f61645f777869645f7a697a64346830757a6666673232313033315f313533323232393930300204010400030201000400',
     * 'md5' => 'b936d3f91e61c3d9f8cd35ce895f4a73',
     * 'aeskey' => '689c988d510d4afda28306183f9d1151',
     * 'filekey' => 'wxid_zizd4h0uzffg221031_1532229900',
     * 'length' => '128715',
     * ],
     * ],
     * @param $user
     * @param array $params
     * @return mixed
     * @throws RequestException
     */
    public function sendWeappMsg($user, array $params)
    {
        $obj = '<appmsg appid="{app_id}" sdkver="0"><title>{title}</title><des>{desc}</des><action/><type>36</type><showtype>0</showtype><soundtype>0</soundtype><mediatagname /><messageext /><messageaction /><content /><contentattr>0</contentattr><url>{url}</url><lowurl /><dataurl /><lowdataurl />{appattach}<statextstr>{statextstr}</statextstr><weappinfo><username>{wx_id}@app</username><pagepath>{path}</pagepath><version>37</version><weappiconurl>{icon}</weappiconurl></weappinfo></appmsg>';
        $attach = '<appattach><totallen>0</totallen><attachid /><emoticonmd5 /><fileext /><cdnthumburl>{url}</cdnthumburl><cdnthumbmd5>{md5}</cdnthumbmd5><cdnthumblength>{length}</cdnthumblength><cdnthumbwidth>{width}</cdnthumbwidth><cdnthumbheight>{height}</cdnthumbheight><cdnthumbaeskey>{aeskey}</cdnthumbaeskey><aeskey>{aeskey}</aeskey><encryver>0</encryver><filekey>{filekey}</filekey></appattach>';

        $obj = str_replace('{title}', !empty($params['title']) ? $params['title'] : '', $obj);
        $obj = str_replace('{desc}', !empty($params['desc']) ? $params['desc'] : '', $obj);
        $obj = str_replace('{url}', $params['url'], $obj);
        $obj = str_replace('{path}', $params['path'], $obj);
        $obj = str_replace('{statextstr}', !empty($params['statextstr']) ? $params['statextstr'] : '', $obj);
        $obj = str_replace('{app_id}', $params['app_id'], $obj);
        $obj = str_replace('{wx_id}', $params['wx_id'], $obj);
        $obj = str_replace('{icon}', $params['icon'], $obj);

        if (!empty($params['attach']) && is_array($params['attach'])) {
            $attach = str_replace('{url}', $params['attach']['url'], $attach);
            $attach = str_replace('{md5}', $params['attach']['md5'], $attach);
            $attach = str_replace('{length}', $params['attach']['length'], $attach);
            $attach = str_replace('{width}', !empty($params['attach']['width']) ? $params['attach']['width'] : 0, $attach);
            $attach = str_replace('{height}', !empty($params['attach']['height']) ? $params['attach']['height'] : 0, $attach);
            $attach = str_replace('{aeskey}', $params['attach']['aeskey'], $attach);
            $attach = str_replace('{filekey}', $params['attach']['filekey'], $attach);
            $obj = str_replace('{appattach}', $attach, $obj);
        }
        return $this->sendMsg($user, ['app_msg' => $obj]);
    }

    /**
     * 发送消息
     * 文本消息 $content 为字符串文字
     * 图片消息 $content['image_file'] post文件地址
     * 语音消息 $content['voice_file'] post文件地址 + $content['time'] 消息时间  silk格式
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
                $val = rawurlencode($val);
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
        $content = rawurlencode($content);
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
     * 登录（token + wx_data） (username + password + wx_data) (phone + password + wx_data) (request + wx_data)
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
        !empty($params['desc']) && $params['desc'] = rawurlencode($params['desc']);
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
        $content = rawurlencode($content);
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
        $name = rawurlencode($name);
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
        $content = rawurlencode($content);
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
        $content = rawurlencode($content);
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
     * @param $content
     * @return mixed
     * @throws RequestException
     */
    public function setRemark($user, $content)
    {
        $content = rawurlencode($content);
        return $this->post(__FUNCTION__, compact('content', 'user'));
    }

    /**
     * 设置代理
     * @param $proxy 例如 192.168.1.1;8000
     * @param int $type 默认1 http 2sock4 3sock5
     * @return mixed
     * @throws RequestException
     */
    public function setProxy($proxy, $type = 1)
    {
        return $this->post(__FUNCTION__, compact('proxy', 'type'));
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
        $requestData = [
            'form_params' => $params,
        ];

        /** 处理图片上传机制 */
        if (!empty($params['image_file'])) {
            if (file_exists($params['image_file'])) {
                $body = fopen($params['image_file'], 'r');
                $requestData['multipart'][] = ['name' => 'image_file', 'contents' => $body];
            }
        }

        /** 处理语音上传机制 */
        if (!empty($params['voice_file'])) {
            if (file_exists($params['voice_file'])) {
                $body = fopen($params['voice_file'], 'r');
                $requestData['multipart'][] = ['name' => 'voice_file', 'contents' => $body];
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
        return $resStr['data'];
    }
}