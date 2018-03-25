<?php

/**
 * Redis 链接类
 * 
 * @package   ultraman\Foundation
 * @copyright Copyright (c) 2017, ultraman
 */

namespace ultraman\Cache;
use ultraman\Log\monoLog;

class Redis
{

    /**
     * @var $conf 配置项
     */

    protected static $config;
    protected static $_instance;

    /**
     * 绑定配置项
     * @param $config array 
     */

    public static function configure($config)
    {
        static::$config = $config;
        return true;
    }

    /**
     * redis 服务链接
     * @param []
     */

    public static function getRedisInstance($key = 'master')
    {   

        if (null === static::$_instance) {
            $redis =  new \Redis;
            \ultraman\Log\monoLog::write("INFO","链接了一次redis");
            $conf = self::$config[$key];
            $timeout = isset($conf['timeout'])?$conf['timeout']:0.2;
            @$connect = $redis->connect($conf['host'], $conf['port'], $timeout);

            if($conf['password']!=''){
                 $redis->auth($conf['password']);
            }

            if (!empty($conf['database'])){
                $redis->select($conf['database']);
            }

            if($connect){
                static::$_instance = $redis;
            }else{
                static::$_instance = '';
                monoLog::write("ERROR","使用Redis链接失败请处理");
            }
        }
        return static::$_instance;
    }
}
