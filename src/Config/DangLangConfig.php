<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/5/12
 * Time: 11:10
 */

namespace OverNick\Sms\Config;

use OverNick\Sms\Config\Repository as Config;

/**
 * 当郎短信接口
 *
 * Class DangLangConfig
 * @method $this setTo($to)             一个号码使用string,多个号码使用array
 * @method $this setSign($sign)
 * @method $this setContent($params)    内容一致使用string,多个号码使用不同内容使用array,数量需要保持一致
 * @package OverNick\Sms\Config
 */
class DangLangConfig extends Config
{

}