<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/2
 * Time: 16:56
 */
namespace OverNick\Sms;

use OverNick\Support\Arr;
use OverNick\Sms\Client\AliyunDmClient;
use OverNick\Sms\Client\TencentDmClient;
use OverNick\Support\Manager;

/**
 * Class SmsManage
 * @method send($param)
 *
 * @package OverNick\Sms
 */
class SmsManage extends Manager
{
    /**
     * 实例化阿里云短信
     *
     * @return object
     */
    protected function createAliyunDriver()
    {
        return $this->resolveDriver(
            AliyunDmClient::class,
            $this->getConfigure('drivers.aliyun')
        );
    }

    /**
     * 实例化腾讯云短信
     *
     * @return object
     */
    protected function createTencentDriver()
    {
        return $this->resolveDriver(
            TencentDmClient::class,
            $this->getConfigure('drivers.tencent')
        );
    }

    /**
     * 实例化程序
     *
     * @param $class
     * @param $config
     * @return object
     */
    private function resolveDriver($class, $config = [])
    {
        $instance = new \ReflectionClass($class);
        return $instance->newInstance($config);
    }

    /**
     * 获取默认短信驱动
     *
     * @return mixed
     */
    public function getDefaultDriver()
    {
        return $this->getConfigure('default');
    }

    /**
     * Set the default cache driver name.
     *
     * @param $driver
     * @return $this
     */
    public function setDefaultDriver($driver)
    {
        return $this->setConfigure('default', $driver);
    }
}