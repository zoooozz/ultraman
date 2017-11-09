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

    public static function Instance()
    {
        if (null === static::$_instance) {
            $redis =  new \Redis;

            @$connect = $redis->connect(self::$config['host'], self::$config['port'], 0.2);
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
