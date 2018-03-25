<?php

/**
 * bootstrap 程序引导 
 * 功能:_init 会被自动加载
 */

class Bootstrap extends \ultraman\Yaf\Bootstrap
{
    public function _initConfig()
    {
        $main = \Yaf_Application::app()->getConfig();
        $conf = include $main['application']['database'];
        if($conf != "" && count($conf)!=0){
            \ultraman\Foundation\DI::set('database',$conf);
        }

        $config = \ultraman\Foundation\DI::get('redis');
        \ultraman\Cache\Redis::configure($config);   
        $redis = \ultraman\Cache\Redis::getRedisInstance('admin_u');              
        \Yaf_Registry::set('admin_u', $redis);
           
        
        $log = $main['application']['logger']['path']?:'/tmp';
        $log_name = $main['application']['service_name'];    
        \ultraman\Foundation\DI::set("log",['path'=>$log,'name'=>$log_name]);
        \Yaf_Registry::set('main', $main);
    }

}
