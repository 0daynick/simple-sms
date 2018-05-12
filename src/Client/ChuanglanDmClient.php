<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/5/12
 * Time: 9:54
 */

namespace OverNick\Sms\Client;

use InvalidArgumentException;
use OverNick\Sms\Abstracts\SmsClientAbstract;
use OverNick\Sms\Config\Repository as Config;
use OverNick\Support\AES;

/**
 * 当郎短信接口
 *
 * Class ChuanglanDmClient
 * @package OverNick\Sms\Client
 */
class ChuangLanDmClient extends SmsClientAbstract
{
    /**
     * @var string
     */
    protected $url = "http://sdk.gzdlinfo.cn/dlcpInterface/sendsms";

    /**
     * @param Config $params
     * @return Config
     */
    public function getParams(Config $params)
    {
        if((!isset($params['to']) ||
            !isset($params['tpl']) ||
            !isset($params['sign']) ||
            !isset($params['params'])))
        {
            throw new InvalidArgumentException("params is empty.");
        }

        $params['params'] = $this->arrayToJson($params['params']);

        return $params;
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        if(!isset($config['app_id']) || !isset($config['key'])){
            throw new InvalidArgumentException("Configure app_id or key not found.");
        }

        $this->config = [
            'app_id' => $config['app_id'],
            'key' =>  $config['key']
        ];
    }

    /**
     * 发送短信
     *
     * @param Config $params
     * @return \OverNick\Sms\Config\ResultConfig
     */
    public function send(Config $params)
    {
        if(is_array($this->params['to'])){
            return $this->sendMulti();
        }else{
            return $this->sendOnce();
        }
    }

    /**
     * 群发短信
     *
     * @return \OverNick\Sms\Config\ResultConfig
     * @throws \Exception
     */
    public function sendMulti()
    {
        // 是否只有一条内容
        $once_content = is_string($this->params['content']) ? true : false;

        if(!$once_content && count($this->params['to']) !== count($this->params['content'])){
            throw new \Exception('账号与内容数量不一致');
        }

        $smsData = [];

        foreach ($this->params['to'] as $key => $val){

            $content = $once_content ? $this->params['content'] : $this->params['content'][$key];

            array_push($smsData,[
                'mobile' => $val,
                'smscontent' => '【'.$this->params['sign'].'】'.$content
            ]);
        }

        return $this->sendSms($smsData);
    }

    /**
     * 发送单挑短信
     *
     * @return \OverNick\Sms\Config\ResultConfig
     */
    public function sendOnce()
    {
        $smsData = [
            [
                'mobile' => $this->params['to'],
                'smscontent' => '【'.$this->params['sign'].'】'.$this->params['content']
            ]
        ];
        return $this->sendSms($smsData);
    }

    /**
     * @param $smsData
     * @return \OverNick\Sms\Config\ResultConfig
     */
    public function sendSms($smsData)
    {
        $post_data = [
            'appId' => $this->config['app_id'],
            'sign' => AES::encrypt(time(), $this->config['key'],''),
            'smsData' => AES::encrypt(json_encode($smsData),$this->config['key'],''),
        ];

        $result = $this->client->request('GET', $this->url, [
            'verify' => false,
            'query' => $post_data
        ]);

        // 获取到页面返回值
        $rsp = json_decode($result->getBody()->getContents(), true);

        $this->result->setOrigin($rsp);

        if ($rsp['code'] === 'SUCCESS'){
            $this->result->setState(true);
        }else{
            $this->result->setState(false);
        }

        return $this->result;
    }
}