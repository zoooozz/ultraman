<?php

/**
 * Redis 链接类
 * 
 * @package   ultraman\Foundation
 * @copyright Copyright (c) 2017, ultraman
 */

namespace ultraman\Cache;

class Redis
{

    /**
     * @var $conf 配置项
     */

    private static $config;

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

    public static function getRedisInstance()
    {
        $redis = new Redis();
        $redis->connect(self::$config['host'], self::$config['port']);
        return $redis;
    }
}
