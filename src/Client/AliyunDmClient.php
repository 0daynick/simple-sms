<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/2
 * Time: 17:52
 */

namespace OverNick\Sms\Client;

use InvalidArgumentException;
use OverNick\Sms\Abstracts\SmsClientAbstract;
use OverNick\Sms\Config\Repository as Config;
use OverNick\Sms\Config\ResultConfig;
use OverNick\Support\Arr;

/**
 * 阿里云短信服务
 *
 * Class AliyunClient
 * @package OverNick\Dm\Client
 */
class AliyunDmClient extends SmsClientAbstract
{
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

    /**
     * @var string
     */
    protected $dateTimeFormat = 'Y-m-d\TH:i:s\Z';

    /**
     * 错误码
     *
     * @var array
     */
    protected $errors = [
        'OK' => '请求成功',
        'InvalidAccessKeyId.NotFound' => 'AccessKeyId不存在',
        'SignatureDoesNotMatch' => 'AccessKeyId或AccessKeySecrete不正确',
        'isp.RAM_PERMISSION_DENY' => 'RAM权限DENY',
        'isv.OUT_OF_SERVICE' => '业务停机',
        'isv.PRODUCT_UN_SUBSCRIPT' => '未开通云通信产品的阿里云客户',
        'isv.PRODUCT_UNSUBSCRIBE' => '产品未开通',
        'isv.ACCOUNT_NOT_EXISTS' => '账户不存在',
        'isv.ACCOUNT_ABNORMAL' => '账户异常',
        'isv.SMS_TEMPLATE_ILLEGAL' => '短信模板不合法',
        'isv.SMS_SIGNATURE_ILLEGAL' => '短信签名不合法',
        'isv.INVALID_PARAMETERS' => '参数异常',
        'isp.SYSTEM_ERROR' => '系统错误',
        'isv.MOBILE_NUMBER_ILLEGAL' => '非法手机号',
        'isv.MOBILE_COUNT_OVER_LIMIT' => '手机号码数量超过限制',
        'isv.TEMPLATE_MISSING_PARAMETERS' => '模板缺少变量',
        'isv.BUSINESS_LIMIT_CONTROL' => '业务限流',
        'isv.INVALID_JSON_PARAM' => 'JSON参数不合法，只接受字符串值',
        'isv.BLACK_KEY_CONTROL_LIMIT' => '黑名单管控',
        'isv.PARAM_LENGTH_LIMIT' => '参数超出长度限制',
        'isv.PARAM_NOT_SUPPORT_URL' => '不支持URL',
        'isv.AMOUNT_NOT_ENOUGH' => '账户余额不足'
    ];

    /**
     * 获取配置信息
     *
     * @param $config
     */
    protected function setConfig($config)
    {
        if(!isset($config['access_key_id']) || !isset($config['access_secret'])){
            throw new InvalidArgumentException("Configure access_key_id or access_secret not found.");
        }

        $this->config = [
            'access_key_id' => $config['access_secret'],
            'access_secret' =>  $config['access_secret']
        ];
    }

    /**
     * 获取参数
     *
     * @param Config $params
     * @return Config
     */
    protected function getParams(Config $params)
    {
        if(!(isset($params['tpl']) &&
            isset($params['sign']) &&
            isset($params['params'])))
        {
            throw new InvalidArgumentException("params is empty.");
        }

        $params['params'] = $this->arrayToJson($params['params']);

        return $params;
    }

    /**
     * 单发短信
     *
     * @return ResultConfig
     */
    protected function sendOnce()
    {
        $postData = [
            'PhoneNumbers' => $this->params['to'],              // 手机号
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
            'Action' => 'SendSms',
            'Version' => $this->version,
        ];

        $postData = array_merge($postData,[
            'Signature' => $this->getSign($postData)
        ]);

        return $this->sendSms($postData);
    }

    /**
     * 群发短信
     *
     * @return ResultConfig
     */
    protected function sendMulti()
    {
        $postData = [
            'PhoneNumberJson' => $this->arrayToJson($this->params['to']),               // 手机号
            'SignNameJson' => $this->arrayToJson($this->params['sign']),                // 签名
            'TemplateCode' => $this->params['tpl'],                                     // 模版号
            'TemplateParamJson' => $this->arrayToJson($this->params['params']),         // 模版参数
            'RegionId' => $this->region,                                                // Region
            'AccessKeyId' => $this->config['access_key_id'],
            'Format' => $this->format,
            'SignatureMethod' => $this->algo,
            'SignatureVersion' => $this->sign_version,
            'SignatureNonce' => uniqid(mt_rand(0,0xffff), true),
            'Timestamp' => gmdate($this->dateTimeFormat),
            'Action' => 'SendBatchSms',
            'Version' => $this->version,
        ];

        $postData = array_merge($postData,[
            'Signature' => $this->getSign($postData)
        ]);

        return $this->sendSms($postData);
    }

    /**
     * 发送短信
     *
     * @param $postData
     * @return ResultConfig
     */
    public function sendSms($postData)
    {
        $result =  $this->client->post($this->url, [
            'verify' => false,
            'form_params' => $postData,
            'http_errors' => false
        ]);

        // 得到接口返回结果, 并解析成数组
        $body = json_decode($result->getBody(), true);

        // 设置源以及错误码
        $this->result->setOrigin($body)
            ->setMessage(Arr::get($this->errors, $body['Code'], '未知错误'))
            ->setCode($body['Code']);

        // 判断是否请求成功
        if($body['Code'] == 'OK'){
            $this->result->setState(true);
        }else{
            $this->result->setState(false);
        }

        return $this->result;
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
     * 数组转换成json字符串
     *
     * @param $data
     * @return string
     */
    private function arrayToJson($data)
    {
        return is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
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