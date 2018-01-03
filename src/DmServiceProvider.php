<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/2
 * Time: 17:31
 */

namespace OverNick\Dm;

use Illuminate\Support\ServiceProvider;
use OverNick\Dm\Aliyun\Client as AliyunClient;
use OverNick\Dm\Tencent\TencentClient as TencentClient;

class DmServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('sms',function($app){
            return new DmManage($app);
        });

        $this->app['sms']->extend('aliyun',function(){
            return new AliyunClient($this->app['sms']->getConfig('aliyun'));
        });

        $this->app['sms']->extend('tencent',function(){
            return new TencentClient($this->app['sms']->getConfig('tencent'));
        });

    }
}