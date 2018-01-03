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
$config = require_once $path.'/../src/config/sms.php';

// 腾讯云短信

// 实例化
$ten_sms = new \OverNick\Dm\Client\TencentClient(array_get($config,'drivers.tencent'));

// 发送短信
$ten_sms->send('13100000001',[
    'tpl' => '001',         // 模版id
    'sign' => '签名',       // 签名
    'params' => [           // 参数
        '123456',
        '产品名'
    ]
]);

// 阿里云短信

// 实例化
$sms = new \OverNick\Dm\Client\AliyunClient(array_get($config,'drivers.aliyun'));

// 发送短信
$result = $sms->send('13100000001',[
        'tpl' => '001',         // 模版id
        'sign' => '签名',       // 签名
        // 短信模板中字段的值
        'params' => json_encode([
            "code" => "123456",
            "product" => "001"
    ], JSON_UNESCAPED_UNICODE)
]);


