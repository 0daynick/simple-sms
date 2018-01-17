<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/3
 * Time: 16:58
 */
namespace OverNick\Dm\Abstracts;

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
        $this->config = $this->getConfig($config);

        $this->client = new Client();
    }

    /**
     * 发送短信
     *
     * @param $to
     * @param DmConfigAbstract $params
     * @return mixed
     */
    public function send($to,DmConfigAbstract $params)
    {
        $this->params = $this->getParams($to,$params);

        return is_array($to) ? $this->sendMulti() : $this->sendOnce();
    }

    /**
     * @param $config
     * @return mixed
     */
    abstract protected function getConfig($config);

    /**
     * 校验传入参数
     *
     * @param $to
     * @param DmConfigAbstract $params
     * @return mixed
     */
    abstract protected function getParams($to,DmConfigAbstract $params);

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