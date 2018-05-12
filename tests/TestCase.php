<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/3/20
 * Time: 18:58
 */

use PHPUnit\Framework\TestCase as BaseTestCase;

use OverNick\Sms\Config\SmsConfig;
use OverNick\Sms\SmsManage;

class TestCase extends BaseTestCase
{

    public function DangLangSend()
    {

    }

    public function AliyunSend()
    {
        
    }

    /**
     * @test
     */
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
        $config = require_once TEST_ROOT.'/../config/sms.php';

        // 实例化程序
        return new SmsManage($config);
    }
}