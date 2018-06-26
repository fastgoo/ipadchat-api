<p align="center">
  Ipadchat-Api
</p>
<p align="center">对接ipadchat-api.exe服务的PHP扩展包，扩展包实现了全部的接口操作。可用于生产环境的ipad接口扩展包</p>

<p align="center">
  <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg"></a> 
 <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/php->=5.6-brightgreen.svg"></a> <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/server-windows-2077ff.svg"></a>
</p>

---

:gift: ipadchat-api.exe为本地化接口服务程序，为php扩展包提供基础的接口服务。

:tada: exe服务程序需要配置，授权配置信息，不然无法正常使用

:ghost: 不要问稳不稳定，目前这这套服务正在内测阶段，稳定性和性能会慢慢优化迭代

:loop: 如果自己想本地试用先加微信群，如果想获取授权app_secret，请向机器人回复 我要测试，就可以获得一个15天的测试授权key。到期后需要花费部分金额来续期


## 更新日志
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

## 技术支持
- 服务支持：周先生 微信号 huoniaojugege
- 协议支持：大牙 QQ 51166611
