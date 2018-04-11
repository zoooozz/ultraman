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
            $conf = self::$config[$key];
            $timeout = isset($conf['timeout'])?$conf['timeout']:0.2;
            @$connect = $redis->connect($conf['host'], $conf['port'], $timeout);
            if ($conf['password']!='') {
                $redis->auth($conf['password']);
            }
            if ($connect) {
                if (!empty($conf['database'])) {
                    $redis->select($conf['database']);
                }
                static::$_instance = $redis;
                return $redis;
            } else {
                return 'error';
            }
        }
        return static::$_instance;
    }
}
