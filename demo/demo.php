<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/3
 * Time: 19:20
 */

$path =  __DIR__.DIRECTORY_SEPARATOR;

// composer 自动加载，路径自行修改
require_once $path.'/../vendor/autoload.php';

// 引用配置文件
$config = require_once $path.'/../config/sms.php';

// 腾讯云短信

// 实例化
$ten_sms = new \OverNick\Dm\Client\TencentDmClient(array_get($config,'drivers.tencent'));

$param = new \OverNick\Dm\Config\DmConfig();
$param->setParams(['123456', '产品名']);   // 设置参数
$param->setSign('签名');              // 签名
$param->setTpl('001');             // 模版id

// 发送短信
$ten_sms->send('13100000001',$param);

// 阿里云短信

// 实例化
$sms = new \OverNick\Dm\Client\AliyunDmClient(array_get($config,'drivers.aliyun'));

$param->setParams(['123456', '产品名']);   // 设置参数
$param->setSign('签名');              // 签名
$param->setParams(json_encode([
    "code" => "123456",
    "product" => "001"
], JSON_UNESCAPED_UNICODE));

// 发送短信
$result = $sms->send('13100000001',$param);


