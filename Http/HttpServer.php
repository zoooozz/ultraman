<?php 

/**
 *  架起Swoole Http服务器
 */

namespace ultraman\Http;

use ultraman\Foundation\Ecode;
use ultraman\Foundation\DI;

class SwooleHttpServer
{
    protected static $_config = [];
    public function __construct($config)
    {
        if (DI::get('port') != '') {
            $config['port'] = DI::get('port');
        }

        if ($config['host'] == '' || $config['port'] == '') {
            throw new \Exception("服务器配置错误", Ecode::ERROR);
        }
        static::$_config = $config;
    }

    public function connent()
    {
        $config = static::$_config;
        $http = new \swoole_http_server($config['host'], $config['port']);
        $http->set(
            array(
                'worker_num' => $config['worker_num']?:3,
                'daemonize' => $config['daemonize'],
                'max_request' => $config['max_request']?:10000,
                'dispatch_mode' => $config['dispatch_mode'],
                'task_worker_num'=>$config['task_worker_num']?:4,
                'task_ipc_mode'=>$config['task_ipc_mode']?:1,
                'task_max_request'=>$config['task_max_request']?:5000,
                'log_file' => $config['log_file']
            )
        );
        return $http;
    }
}
