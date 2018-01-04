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

            $dm =  new DmManage($app);

            $initList = $this->app['config']['sms']['init'];

            if(!is_array($initList) || count($initList) <= 0){
                throw new InvalidArgumentException('not init drivers');
            }

            foreach ($initList as $key => $classed){
                $dm->extend($key,function() use($dm, $key, $classed){
                    $instance = new \ReflectionClass($classed);
                    $instance->newInstance($dm->getConfig($key));
                });
            }

        });
    }
}