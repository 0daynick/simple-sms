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
            Arr::get($this->app, 'drivers.aliyun')
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
            Arr::get($this->app, 'drivers.tencent')
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
        return Arr::get($this->app, 'default');
    }

    /**
     * Set the default cache driver name.
     *
     * @param $name
     * @return $this
     */
    public function setDefaultDriver($name)
    {
        Arr::set($this->app,'default', $name);
        return $this;
    }
}