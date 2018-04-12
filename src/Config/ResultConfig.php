<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/3/21
 * Time: 16:02
 */

namespace OverNick\Sms\Config;

use OverNick\Sms\Config\Repository as Config;

/**
 * 结果返回
 *
 * Class ResultConfig
 * @method $this setState($state)
 * @method $this setCode($code)
 * @method $this setMessage($message)
 * @method $this setOrigin($origin)
 * @method $this getState()
 * @method $this getCode()
 * @method $this getMessage()
 * @method $this getOrigin()
 * @package OverNick\Dm\Config
 */
class ResultConfig extends Config
{
    /**
     * state 状态
     * code http请求状态码
     * message 消息
     * origin 接口返回的
     * 原数据
     * @var array
     */
    protected $config = [
        'state' => true,
        'code' => 200,
        'message' => '',
        'origin' => ''
    ];
}