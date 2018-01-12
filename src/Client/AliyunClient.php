<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/2
 * Time: 17:52
 */

namespace OverNick\Dm\Client;

use InvalidArgumentException;
use OverNick\Dm\Abstracts\DmClientAbstract;

class AliyunClient extends DmClientAbstract
{
    /**
     * @var string 阿里云的产品名，不需要跟换
     */
    protected $action = 'SendSms';

    /**
     * @var string 阿里云短信请求的接口地址
     */
    protected $url = 'http://dysmsapi.aliyuncs.com/';

    /**
     * @var string 阿里云的业务区域
     */
    protected $region = 'cn-hangzhou';

    /**
     * @var string 返回结果格式
     */
    protected $format = 'JSON';

    /**
     * @var string 接口请求方式
     */
    protected $method = 'POST';

    /**
     * @var string 签名版本号
     */
    protected $sign_version = '1.0';

    /**
     * @var string 版本号
     */
    protected $version = '2017-05-25';

    /**
     * @var string 加密方式
     */
    protected $algo = 'HMAC-SHA1';

    protected $dateTimeFormat = 'Y-m-d\TH:i:s\Z';

    /**
     * 校验配置信息
     */
    protected function validateConfig()
    {
        if(!isset($this->config['access_key_id']) || !isset($this->config['access_secret'])){
            throw new InvalidArgumentException("Configure access_key_id or access_secret not found.");
        }
    }

    /*
     * 校验参数
     */
    protected function validateParams($to,$params)
    {
        if(!(isset($params['tpl']) &&
            isset($params['sign']) &&
            isset($params['params'])))
        {
            throw new InvalidArgumentException("params is empty.");
        }

        $params['to'] = $to;

        $this->params = $params;
    }

    /**
     * 单发
     */
    protected function sendOnce($mobile = null)
    {
        return $this->sendSms($mobile = null);
    }

    /**
     * 群发
     */
    protected function sendMulti()
    {
        foreach ($this->params['to'] as $mobile){
            $this->sendOnce($mobile);
        }
    }

    /**
     * 发送短信
     *
     * @param null $mobile
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendSms($mobile = null)
    {

        $mobile = is_null($mobile) ? $this->params['to'] : $mobile;

        $postData = [
            'PhoneNumbers' => $mobile,              // 手机号
            'SignName' => $this->params['sign'],                // 签名
            'TemplateCode' => $this->params['tpl'],             // 模版号
            'TemplateParam' => $this->params['params'],         // 模版参数
            'RegionId' => $this->region,                        // Region
            'AccessKeyId' => $this->config['access_key_id'],
            'Format' => $this->format,
            'SignatureMethod' => $this->algo,
            'SignatureVersion' => $this->sign_version,
            'SignatureNonce' => uniqid(mt_rand(0,0xffff), true),
            'Timestamp' => gmdate($this->dateTimeFormat),
            'Action' => $this->action,
            'Version' => $this->version,
        ];

        $postData = array_merge($postData,[
            'Signature' => $this->getSign($postData)
        ]);

        return $this->client->post($this->url, [
            'verify' => false,
            'form_params' => $postData,
        ]);
    }

    /**
     * 获取签名
     *
     * @param $parameters
     * @return string
     */
    private function getSign($parameters)
    {
        ksort($parameters);

        $stringToSign = $this->method.'&%2F&' . $this->percentencode(http_build_query($parameters));

        $signature =  base64_encode(hash_hmac('sha1', $stringToSign, $this->config['access_secret']."&", true));

        return $signature;
    }

    /**
     * 转义字符
     *
     * @param $str
     * @return mixed|string
     */
    private function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
}