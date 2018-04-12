<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/2
 * Time: 17:31
 */
namespace OverNick\Sms;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('sms',function($app){
            return new SmsManage($app->make('config')->get('sms'));
        });
    }
}