<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/3
 * Time: 14:56
 */

return [
    /**
     * 默认使用的短信服务商
     */
    'default' => 'aliyun',
    /**
     * 配置信息
     */
    'drivers' => [
        // 腾讯云配置
        'tencent' => [
            'driver' => 'tencent',
            'app_id' => '控制台中的app id',
            'app_key' => '控制台中的app key'
        ],
        // 阿里云配置
        'aliyun' => [
            'driver' => 'aliyun',
            'access_key_id' => '控制台中的AccessKeyId',
            'access_secret' => '控制台中的AccessSecret'
        ]
    ],
    /**
     * 需要初始化的内容，如果不需要自启动，注释即可
     */
    'init' => [
        'aliyun' => \OverNick\Dm\Client\AliyunDmClient::class,
        'tencent' => \OverNick\Dm\Client\TencentDmClient::class
    ]
];