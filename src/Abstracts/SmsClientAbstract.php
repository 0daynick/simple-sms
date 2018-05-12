<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/3
 * Time: 16:58
 */
namespace OverNick\Sms\Abstracts;

use GuzzleHttp\Client;
use OverNick\Sms\Config\ResultConfig;
use OverNick\Sms\Exceptions\BadResultException;
use InvalidArgumentException;
use OverNick\Sms\Config\Repository as Config;

/**
 * Class DmClientAbstract
 * @package OverNick\Dm\Abstracts
 */
abstract class SmsClientAbstract
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

    /**
     * 返回的对象
     *
     * @var ResultConfig
     */
    protected $result;

    public function __construct(array $config = [],Client $client = null)
    {
        $this->setConfig($config);

        $this->client = $client ?: new Client();

        $this->result = new ResultConfig();
    }

    /**
     * 发送短信
     *
     * @param Config $params
     * @return mixed
     * @throws BadResultException
     */
    public function send(Config $params)
    {
        if(!isset($params['to'])){
            throw new InvalidArgumentException("params is empty.");
        }

        $this->params = $this->getParams($params);

        // 批量发送还是单条发送
        $result =  is_array($params['to']) ? $this->sendMulti() : $this->sendOnce();

        // 必须按照约定返回对象
        if (!$result instanceof ResultConfig) {
            throw new BadResultException();
        }

        return $result;
    }

    /**
     * 数组转换成json字符串
     *
     * @param $data
     * @return string
     */
    protected function arrayToJson($data)
    {
        return is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
    }

    /**
     * @param $config
     */
    abstract protected function setConfig($config);

    /**
     * 校验传入参数
     *
     * @param Config $params
     * @return mixed
     */
    abstract protected function getParams(Config $params);

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