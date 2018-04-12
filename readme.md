# 短信集成包

## 安装

```shell
composer require overnic/dm-package
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

use OverNick\Sms\Config\SmsConfig;

class SmsController extends Controller
{

    /**
     * 发送短信
     *
     * @return string
     */
    public function index()
    {
        // 初始化配置文件
        $config = new SmsConfig();
        
        // 设置模版参数
        $config->setParams([
            "code" => "123456",
            "product" => "001"
            ]);
        
        // 设置模版id
        $config->setTpl('001');
        // 设置收信手机号
        $config->setTo('13100000001');
        // 使用签名
        $config->setSign('阿里云签名');
        
        // 默认使用阿里云短信
        app('sms')->send($config);
        
        // 设置模版
        $config->setTpl('001');
         // 设置收信手机号
         $config->setTo('13100000001');
        // 设置模版参数
        $config->setParams([1,2,3]);
        // 设置签名
        $this->setSign('腾讯云签名');
        
        // 使用腾讯云短信
        app('sms')->dirver('tencent')->send($config);
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


> 独立使用
```php
<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/3
 * Time: 19:20
 */

// composer 自动加载，路径自行修改
require_once $path.'/../vendor/autoload.php';

// 引用配置文件，路径可自行调整
$config = require_once $path.'/../config/sms.php';

// 实例化短信服务类
$manage = new \OverNick\Sms\SmsManage($config);

// 短信模版参数短信
$param = new \OverNick\Sms\Config\SmsConfig();
$param->setTo('13100000001');
$param->setParams(['123456', '产品名']);   // 设置参数
$param->setSign('签名');              // 签名
$param->setTpl('001');             // 模版id

// 使用腾讯云发送短信
$manage->driver('tencent')->send($param);


// 阿里云短信模版参数
$param->setTo('13100000001');                 // 设置手机号
$param->setParams(['123456', '产品名']);       // 设置参数
$param->setSign('签名');                      // 签名
$param->setParams([
    "code" => "123456",
    "product" => "001"
]);

// 使用阿里云发送短信
$manage->driver('aliyun')->send($param);
```