<?php

/**
 * Di 基础类
 *
 * @package   ultraman\Foundation
 * @copyright Copyright (c) 2017, ultraman
 */

namespace ultraman\Foundation;

use ultraman\Foundation\Container;

class DI
{   
    /**
     * @var instance 容器对象
     */

    protected static $instance;

    /**
     * 自动重载容器获取设置容器对象
     * @param method 函数方法
     * @param args key
     */

    public static function __callStatic($method, $args)
    {   

        if (!static::$instance) {
            static::$instance = new Container();
        }

        switch (count($args)) {
        case 0:
            return static::$instance->$method();
        case 1:
            return static::$instance->$method($args[0]);
        case 2:
            return static::$instance->$method($args[0], $args[1]);
        case 3:
            return static::$instance->$method($args[0], $args[1], $args[2]);
        case 4:
            return static::$instance->$method($args[0], $args[1], $args[2], $args[3]);
        default:
            return call_user_func_array([static::$instance, $method], $args);
        }
    }
}
