<?php
/**
 * Created by PhpStorm.
 * User: overnic
 * Date: 2018/1/2
 * Time: 17:31
 */

namespace OverNick\Dm;

use InvalidArgumentException;
use Illuminate\Support\ServiceProvider;

class DmServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('sms',function($app){
            return new DmManage($app->make('config')->get('sms'));
        });
    }
}