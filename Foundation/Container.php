<?php

/**
 * Container 基础类
 * 依赖注入服务类
 *
 * @package   ultraman\Foundation
 * @copyright Copyright (c) 2017, ultraman
 *  
 */


namespace ultraman\Foundation;

class Container
{
    
    /**
     * services 注入的服务列表
     * @return array 
     */

    protected $services = [];

    /**
     * instances 容器绑定对象
     * @return object
     */

    protected $instances = [];

    /**
     * 获取注册的服务
     * @param string alias 
     */

    public function get($alias)
    {
        return isset($this->services[$alias]) ? $this->services[$alias] : null;
    }

    /**
     * 将类名或者匿名函数注册绑定到容器
     * @param alias    key 
     * @param instance 注册的服务
     * @param rewrite  覆盖
     */

    public function set($alias, $instance, $rewrite = false)
    {
        if (!isset($this->services[$alias]) || !$rewrite) {
            $this->services[$alias] = $instance;
            return true;
        }
        return false;
    }

    /**
     * 将类名或者匿名函数注册绑定到容器，并指定为单例模式
     * @param abstract 抽象名称
     * @param concrete 匿名函数、构造方法
     */

    public function singleton($alias, $parameters = [])
    {
        if (isset($this->instances[$alias])) {
            return $this->instances[$alias];
        }

        if (isset($this->services[$alias]) && is_callable($this->services[$alias])) {
            $object = call_user_func_array($this->services[$alias], $parameters);
        } else {
            $class = new \ReflectionClass($alias);
            $object = $class->newInstanceArgs($parameters);
        }

        if ($object !== null) {
            $this->instances[$alias] = $object;
        }

        return $object;
    }

    /**
     * 根据抽象名称构造对象.
     * @param  string $abstract   注册绑定的抽象名称
     * @param  array  $parameters 构造参数
     */

    public function make($alias, $parameters = [], $shared = false)
    {
        if ($shared && isset($this->instances[$alias])) {
            return $this->instances[$alias];
        }

        if (isset($this->services[$alias]) && is_callable($this->services[$alias])) {
            $object = call_user_func_array($this->services[$alias], $parameters);
        } else {
            $class = new \ReflectionClass($alias);
            $object = $class->newInstanceArgs($parameters);
        }

        if ($shared && $object !== null) {
            $this->instances[$alias] = $object;
        }

        return $object;
    }
}
