# 短信集成包

## 安装

```shell
composer require "overnic/dm-package:~1.0"
```

## 配置

> Laravel 应用

1. 复制文件`src/config/sms.php` 到程序`config`目录下

2. 在 `config/app.php` 注册 ServiceProvider
```php
'providers' => [
    // ...
    OverNick\Dm\DmServiceProvider::class,
],
```

3. 程序中的使用
```php

<?php

namespace App\Http\Controllers;

class SmsController extends Controller
{

    /**
     * 发送短信
     *
     * @return string
     */
    public function index()
    {
        // 默认使用阿里云短信
        app('sms')->send('13100000001',[
            'tpl' => '001',
            'params' => json_encode([
                        "code" => "123456",
                        "product" => "001"
                ], JSON_UNESCAPED_UNICODE),
            'sign' => '签名'
        ]);
        
        // 使用腾讯云短信
        app('sms')->dirver('tencent')->send('13100000001',[
           'tpl' => '001',
           'params' => [1,2,3],
           'sign' => '签名'
        ]);
    }
}

```

4. 修改默认服务商,修改`config/sms.php`,将`default` 值修改为tencent
```php
return [
    /**
     * 默认使用的短信服务商
     */
    'default' => 'tencent',   
    /**
     * 配置信息
     */
    'drivers' => [
        // 腾讯云配置
        'tencent' => [
            'app_id' => '控制台中的app id',
            'app_key' => '控制台中的app key'
        ],
        // 阿里云配置
        'aliyun' => [
            'access_key_id' => '控制台中的AccessKeyId',
            'access_secret' => '控制台中的AccessSecret'
        ]
    ]
];
```
5. 扩展...


> 不嵌套框架使用
```php
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
```