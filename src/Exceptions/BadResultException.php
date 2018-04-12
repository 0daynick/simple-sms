<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/3/21
 * Time: 16:10
 */

namespace OverNick\Sms\Exceptions;

use Exception;
use Throwable;

class BadResultException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}