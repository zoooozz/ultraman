<?php 

/**
 * App 基础类
 *
 * @package   ultraman\Foundation
 * @copyright Copyright (c) 2017, ultraman
 * @var 基础服务入口文件 
 */


namespace ultraman;
use ultraman\Foundation\DI;

class App
{
    /**
     * 构造函数 入口加载配置文件进行注入
     *
     * @param db    数据库配置 
     * @param main  服务配置项
     * @param api   外部接口配置项
     */

    public function __construct($config = [])
    {   
        require dirname(dirname(__FILE__)."/../").'/vendor/autoload.php';
        if(count($config) == 0){
            return true;
        }

        foreach ($config['conf'] as $key => $class) {
            $f = pathinfo($class, PATHINFO_EXTENSION); 
            if($f === 'php'){
                $conf = @include $class;
                if($conf == ""){
                    continue;
                }
            }
            if($f === 'ini'){
                $conf = @parse_ini_file($class,true);
                if($conf == ""){
                    continue;
                }
            }    
            DI::set($key,$conf);
        }
        return true;
    }
}
