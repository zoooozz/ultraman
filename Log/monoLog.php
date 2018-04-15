<?php 

/**
 * Log 日志类
 *
 * @package   ultraman\Tools
 * @copyright Copyright (c) 2017, ultraman
 */


namespace ultraman\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use ultraman\Foundation\DI;

class monoLog
{
    /**
     *  @var  config 日志配置项
     */

    protected static $config;

    /**
     * @var 数据格式
     */
    
    protected static $data = [];
    
    /**
     * @var 错误级别
     */

    protected static $level;
    
    /**
     * 日志记录 require Monolog
     * @param $params 参数设置
     * @param $level 级别处理
     */

    public static function write($level = 'INFO', $params = [])
    {
        if (count($params) == 0) {
            return true;
        }
        $item = [];
        $item['query'] = $params;
        $config = DI::get('log');
        if (count($config) == 0 || $config == "") {
            return true;
        }
        
        $logs = stripslashes(json_encode($item, JSON_UNESCAPED_UNICODE));
        static::$config = $config;
        static::$data = $logs;
        static::$level = strtoupper($level);
        $source_name = $config['name']?: "default";
        $path = $config['path']?: "/tmp";
        $file = date("Ymd");
        $base_path = $path."/".$source_name.'/'.$file.'.log';
        $log = new Logger($source_name);
        $log->pushHandler(new StreamHandler($base_path));
        call_user_func_array(array(__NAMESPACE__ .'\monoLog', static::$level), array($log));
    }
    
    public static function __callStatic($name, $log)
    {
        return true;
    }

    public static function ERROR($log)
    {
        $log->addError(static::$data);
    }
    public static function INFO($log)
    {
        $log->addInfo(static::$data);
    }
    public static function DEBUG($log)
    {
        $log->addDebug(static::$data);
    }
    public static function WARNING($log)
    {
        $log->addWarning(static::$data);
    }
}
