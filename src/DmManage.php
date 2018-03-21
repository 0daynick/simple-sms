<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/2
 * Time: 16:56
 */

namespace OverNick\Dm;

use Closure;
use InvalidArgumentException;
use OverNick\Dm\Client\AliyunDmClient;
use OverNick\Dm\Client\TencentDmClient;

class DmManage
{

    /**
     * @var
     */
    protected $config;

    protected $dm = [];

    protected $customCreators = [];

    /**
     * DmManage constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 使用驱动发信
     *
     * @param null $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        return $this->get($driver);
    }

    /**
     * @param $driver
     * @return mixed
     */
    protected function get($driver)
    {
        return $this->dm[$driver] ?? $this->resolve($driver);
    }

    /**
     * Resolve the given store.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Cache\Repository
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Cache store [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        } else {
            $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

            if (method_exists($this, $driverMethod)) {
                return $this->{$driverMethod}($config);
            } else {
                throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
            }
        }
    }

    /**
     * 实例化阿里云短信
     *
     * @param $config
     * @return object
     */
    protected function createAliyunDriver($config)
    {
        return $this->resolveDriver(AliyunDmClient::class, $config);
    }

    /**
     * 实例化腾讯云短信
     *
     * @param $config
     * @return object
     */
    protected function createTencentDriver($config)
    {
        return $this->resolveDriver(TencentDmClient::class, $config);
    }

    /**
     * 实例化程序
     *
     * @param $class
     * @param $config
     * @return object
     */
    private function resolveDriver($class, $config)
    {
        $instance = new \ReflectionClass($class);
        return $instance->newInstance($config);
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($config);
    }

    /**
     * Get the cache connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    public function getConfig($name)
    {
        return array_get($this->config, "drivers.{$name}");
    }

    /**
     * 获取默认短信驱动
     *
     * @return mixed
     */
    public function getDefaultDriver()
    {
        return array_get($this->config, 'default');
    }

    /**
     * Set the default cache driver name.
     *
     * @param $name
     * @return $this
     */
    public function setDefaultDriver($name)
    {
        array_set($this->config,'default', $name);
        return $this;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string    $driver
     * @param  \Closure  $callback
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}