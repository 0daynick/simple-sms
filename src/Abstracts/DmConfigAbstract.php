<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/12
 * Time: 10:20
 */

namespace OverNick\Dm\Abstracts;

use ArrayAccess;
use Exception;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * Class DmConfigAbstract
 * @package OverNick\Dm\Abstracts
 */
class DmConfigAbstract implements Config, ArrayAccess
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param null $key
     * @return bool
     */
    public function has($key = null)
    {
        return is_null($key) ? !empty($this->config) : array_key_exists($key, $this->config);
    }

    /**
     * @param array|string $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_get($this->config, $key, $default);
    }

    /**
     * @param array|string $key
     * @param null $value
     */
    public function set($key, $value = null)
    {
        array_set($this->config, $key, $value);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function push($key, $value)
    {
        array_push($this->config, [$key => $value]);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function prepend($key, $value)
    {
        array_prepend($this->config, $value, $key);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->config;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        if ($prefix == 'set') {
            $this->set(snake_case(substr($name, 3)), array_get($arguments, '0', true));
            return $this;
        }

        if ($prefix == 'get') {
            return $this->get(snake_case(substr($name, 3)));
        }

        throw new Exception("Call to undefined method {$name}()");
    }
}