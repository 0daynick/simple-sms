<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/3/20
 * Time: 18:58
 */

use PHPUnit\Framework\TestCase as BaseTestCase;

use OverNick\Sms\Config\DangLangConfig;
use OverNick\Sms\Config\SmsConfig;
use OverNick\Sms\SmsManage;

class TestCase extends BaseTestCase
{

    /**
     * @test
     */
    public function DangLangSend()
    {
        $config = new DangLangConfig();
        $config->setTo('1310000001');
        $config->setSign('签名');
        $config->setContent('尊敬的用户，你本次操作验证码是:123456');

        $result = $this->getSms()->driver(\OverNick\Sms\Config\Sms::DRIVER_DANGLANG)->send($config);

        $this->assertTrue($result->getState(), '请求失败：'. $result->getMessage());
    }

    public function AliyunSend()
    {
    }

    public function TencentSend()
    {
        // 参数
        $params = new SmsConfig();
        $params->setTo('13100000000');
        $params->setTpl('SMS_00000000');
        $params->setSign('签名');
        $params->setParams([
            "code" => "123456",
            "product" => "001"
        ]);

        // 执行发送
        $result = $this->getSms()->driver('tencent')->send($params);

        $this->assertTrue($result->getState(), '请求失败：'. $result->getMessage());
    }

    /**
     * @return SmsManage
     */
    protected function getSms()
    {
        // 引用配置文件
        $config = require __DIR__.'/../config/sms.php';

        // 实例化程序
        return new SmsManage($config);
    }
}