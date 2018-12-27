<p align="center">
  Ipadchat-Api
</p>
<p align="center">对接ipadchat-api.exe服务的PHP扩展包，扩展包实现了全部的接口操作。可用于生产环境的ipad接口扩展包</p>

<p align="center">
  <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg"></a> 
 <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/php->=5.6-brightgreen.svg"></a> <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/server-windows-2077ff.svg"></a>
</p>

---
中文文档地址： https://www.showdoc.cc/ipadchatgo?page_id=1346708337106380

管理后台地址： https://ipadchat.fastgoo.net

:gift: ipadchat-api.exe为本地化接口服务程序，为php扩展包提供基础的接口服务。

:tada: exe服务程序需要配置，授权配置信息，不然无法正常使用

:ghost: 不要问稳不稳定，目前这这套服务正在内测阶段，稳定性和性能会慢慢优化迭代


## 更新日志

#### 2018-12-25
- 更新了新的服务架构，采用golang + grpc开发...跨平台以及稳定性上升到一个新的层次
- 新的文档地址：https://www.showdoc.cc/ipadchatgo?page_id=1346708337106380
- 摒弃到原来的dll + 易语言的服务端策略...
- 新的服务端将支持window mac linux平台

#### 2018-10-17
- 修复了服务端的获取公网IP的问题



#### 2018-10-11
- 修改了扫码登录失败的问题
- 服务端更新为1.5版本
#### 2018-08-29
- API类添加了收藏全部接口，公众号操作全部接口
- 修复了发送图片语音文件导致的异常错误
#### 2018-08-13
- API类调整了发送语音 图片消息的策略，直接使用文件的路径的方式就可以发送，避免了传输的问题
- API类调整了获取消息图片、语音、视频的请求参数
- exe做了大量优化图片语音上传优化，以及返回数据视频图片语音的优化
#### 2018-07-25
- API类更新了发送小程序的方法
#### 2018-07-16
- 更新PHP SDK 的文字urlencode策略，请求响应结果mb_convert_encoding优化处理
- 服务端回调日志优化显示，调整朋友圈接口url
- 优化服务端BUG
#### 2018-07-08
- 服务端 优化授权策略，需要在管理后台设置公网IP，才可以。
- 如果KEY是受限制的KEY，那么只能启用一个进程，否则可以启用多个进程...非受限制的KEY可以设置N个公网IP
- 添加了请求、响应、回调的日志文件
- 优化了内部的事件机制，以及垃圾销毁机制
#### 2018-06-27
- 优化php扩展包的群邀请事件，新增同步拉去消息接口，调整获取二维码可选择传入wx_data
- 优化服务端，调整垃圾实例的优化策略
- 二维码图片自动销毁
- 添加token(断线重连)、request(手机确认登录)登录操作
- 退出登录接口自动销毁实例
- 确认登录成功后添加推送事件
#### 2018-06-26
- 调整了PHP扩展的内部数据结构
- 如果请求接口返回非1，ret直接返回false。如果是1,直接返回data里的结构体
- 调整了demo的数据结构体
#### 2018-06-25
- 调整了token登录机制(还是有些问题)
- 获取二维码时可以传入wx_data参数来降低异常检测封号
- 更新响应框日志打印，请求框日志打印
- 内部返回数据优化处理，请求参数校验优化
- 删除内部垃圾变量
- 修改垃圾实例销毁策略，如果实例已下线那么30分钟一次的检测就会销毁实例，释放内存连接数
#### 2018-06-23
- 添加红包相关接口
- 添加转账领取查看相关接口
- 添加朋友圈相关接口
- 添加标签相关接口
- demo中新增了红包自动领取，转账自动领取答谢回复功能
#### 2018-06-22
- 发送消息、设置公告、修改群名称等等中文相关的地方使用了urlencode处理以便于服务端识别emoji表情
- 回调通知也修改了服务端的编码以支持emoji表情
- 部分接口优化了emoji表情的展示
- 优化ipadchat-api的服务端的代码结构，简化处理逻辑，优化中文emoji表情支持
#### 2018-06-20
- 发布1.0稳定内测版
- 实现文字、图片、语音、表情、链接、分享联系人的消息发送
- 实现好友申请通过
- 群邀请、群名称修改、群公告发布、删除成员、获取群成员列表、退出群聊
- 事件处理:邀请好友入群、群收款、群红包、好友红包、好友转账...等等98%的消息事件覆盖
- 获取个人信息、获取联系人信息、获取群或者自己的二维码等等功能接口

## 安装说明

第一次请先使用php扩展包的默认api接口地址，如果熟练后可以自己配置自己的api接口地址
#### compsoer 安装：(可集成在框架中)
`composer require fastgoo/padchat-api` 
#### github 安装：
```
git clone https://github.com/fastgoo/ipadchat-api.git  //克隆项目
cd ipadchat-api //进入项目
composer install //安装依赖
//编辑demo.php 配置自己的登录信息
php demo.php //cli运行demo,也可以fpm运行
```

## 快速开始

```PHP
**
 * @var $api
 * host 请求域名 默认域名https://wxapi.fastgoo.net/
 * timeout 请求超时时间
 * secret 请求key
 */
$api = new \PadChat\Api(['secret' => 'test']);

try {
    /** 初始化微信实例 */
    $res = $api->init('https://webhook.fastgoo.net/callback.php');
    if(!$res){
        exit("微信实例获取失败");
    }
    /** 设置微信实例 */
    $api->setWxHandle($res['wx_user']);
    /** 账号密码/账号手机号/token 登录 */
    $loginRes = $api->login([
        'username' => '你的账号',
        'password' => '你的密码',
        'wx_data' => '不填则安全验证登录'
    ]);
    var_dump($loginRes);
    /** 获取登录二维码 */
    $qrcode = $api->getLoginQrcode();
    if(!$qrcode){
        exit("二维码链接获取失败");
    }
    var_dump($qrcode['url']);
    /** 获取扫码状态 */
    while (true) {
        $qrcodeInfo = $api->getQrcodeStatus();
        if (!$qrcodeInfo) {
            exit();
        }
        var_dump($qrcodeInfo);
        sleep(1);
    }
} catch (\PadChat\RequestException $requestException) {
    var_dump($requestException->getMessage());
}
```
##### 回调地址范例：[点这里](https://github.com/fastgoo/ipadchat-api/blob/master/callback.php)

## 加入微信群
- 扫描二维码，备注填写 ipadchat-api 有大小写区分
- 扫描二维码，备注填写 ipadchat-api 有大小写区分
- 扫描二维码，备注填写 ipadchat-api 有大小写区分
<img src="https://resource.fastgoo.net/201806211622073557.JPG" width="240" height="300" alt="图片描述文字"/>

## 加入微星开发者社区QQ群
<img src="https://resource.fastgoo.net/201811161421163380.pic.jpg" width="240" height="300" alt="图片描述文字"/>

## 技术支持
- 服务支持：周先生 微信号 huoniaojugege
- 协议支持：大牙 QQ 51166611
