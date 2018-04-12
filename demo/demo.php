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

// 实例化短信服务类
$manage = new \OverNick\Sms\SmsManage($config);

// 短信模版参数短信
$param = new \OverNick\Sms\Config\SmsConfig();
$param->setTo('13100000001');
$param->setParams(['123456', '产品名']);   // 设置参数
$param->setSign('签名');              // 签名
$param->setTpl('001');             // 模版id

// 发送短信
$manage->driver('tencent')->send($param);


// 阿里云短信模版参数
$param->setTo('13100000001');                 // 设置手机号
$param->setParams(['123456', '产品名']);       // 设置参数
$param->setSign('签名');                      // 签名
$param->setParams([
    "code" => "123456",
    "product" => "001"
]);

// 发送短信
$manage->driver('aliyun')->send($param);


