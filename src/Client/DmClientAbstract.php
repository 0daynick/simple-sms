<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/3
 * Time: 16:58
 */
namespace OverNick\Dm\Client;

use GuzzleHttp\Client;

abstract class DmClientAbstract
{

    /**
     * @var array 配置信息
     */
    protected $config;

    /**
     * guzzle http 的client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var array 传入的参数
     */
    protected $params;

    public function __construct(array $config = [])
    {
        $this->config = $config;

        $this->validateConfig();

        $this->client = new Client();
    }

    /**
     * 检测配置信息
     *
     * @return mixed
     */
    abstract protected function validateConfig();

    /**
     * 发送短信
     *
     * @param $to
     * @param array $params
     * @return mixed
     */
    public function send($to,array $params = [])
    {
        $this->validateParams($to,$params);

        return is_array($to) ? $this->sendMulti() : $this->sendOnce();
    }

    /**
     * 校验传入参数
     *
     * @param $to
     * @param $params
     * @return mixed
     */
    abstract protected function validateParams($to,$params);

    /**
     * 单发
     *
     * @return mixed
     */
    abstract protected function sendOnce();

    /**
     * 群发
     *
     * @return mixed
     */
    abstract protected function sendMulti();

}