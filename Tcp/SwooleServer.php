<?php

/**
 *  架起Swoole Tcp yaf 版本服务器
 */

namespace ultraman\Tcp;

define('AUTHLOAD', dirname(dirname(dirname(dirname(__FILE__)))));
define('APPLICATION_PATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))));
require AUTHLOAD.'/autoload.php';
use League\CLImate\CLImate;
use ultraman\Foundation\DI;

class SwooleServer
{
    protected $climate;
    protected $pid;
    public function run()
    {
        $this->climate = new CLImate();
        
        $config = @parse_ini_file(APPLICATION_PATH."/conf/main.ini", true);
        $this->pid = APPLICATION_PATH.'/'.$config['common']['application.service_name'].'-tcp-service.pid';
        if ($this->isRunning()) {
            $this->climate->error('服务已经启动');
            die;
        }
        $main = $config['tcp-service']??'';
        if ($main == '') {
            $this->climate->error('未配置tcp服务器');
            die;
        }
        if (DI::get('port') != '') {
            $main['port'] = DI::get('port');
        }
   
        $server = new \Hprose\Swoole\Server("tcp://".$main['host'].':'.$main['port']);
        \Yaf_Registry::set('swoole_tcp', $server);

        $server->set(
            array(
                'worker_num'=>isset($main['worker_num'])?$main['worker_num']:3,
                'daemonize'=>isset($main['daemonize'])?$main['daemonize']:false,
                'max_request'=>isset($main['max_request'])?$main['max_request']:10000,
                'dispatch_mode'=>isset($main['dispatch_mode'])?$main['dispatch_mode']:0,
                'task_worker_num'=>isset($main['task_worker_num'])?$main['task_worker_num']:4,
                'task_ipc_mode'=>isset($main['task_ipc_mode'])?$main['task_ipc_mode']:1,
                'task_max_request'=>isset($main['task_max_request'])?$main['task_max_request']:5000,
                'heartbeat_check_interval'=>isset($main['heartbeat_check_interval'])?$main['heartbeat_check_interval']:30,
                'heartbeat_idle_time'=>isset($main['heartbeat_idle_time'])?$main['heartbeat_idle_time']:60,
            )
        );
        $server->setGetEnabled=true;
        $server->on('Start', array($this, 'onStart'));
        $server->on('task', array($this, 'onTask'));
        $server->on('finish', array($this, 'onFinish'));
        $server->addInstanceMethods(new $main['func']);
        $server->setDebugEnabled(false);
        $server->setErrorTypes(E_ALL);
        $server->start();
    }

    public function onStart($serv)
    {
        $config = @parse_ini_file(APPLICATION_PATH."/conf/main.ini", true);
        $name = $config['common']['application.service_name'];
        file_put_contents($this->pid, $serv->master_pid);
        echo $name."TCP服务启动\n";
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        if (isset($data['cli']) && $data['cli'] != '') {
            $cli = $data['cli'];
            $params = '';
            if (isset($data['params']) && count($data['params'])!=0 && is_array($data['params'])) {
                foreach ($data['params'] as $key => $value) {
                    if (trim($value) == '') {
                        continue;
                    }
                    $params.="--{$key} ".$value." ";
                }
            }
            $path = 'php '.APPLICATION_PATH.'/cli '.$cli.' '. $params;
            exec($path);
        } else {
            call_user_func_array($data['class'], [$data]);
        }
        
        $items['msg'] = "异步任务[来自进程 {$from_id}，当前进程 {$task_id}";
        $taskdata = serialize($items);
        $serv->finish($taskdata);
    }
    
    public function onFinish($serv, $task_id, $data)
    {
        $params = unserialize($data);
        $taskdata['msg'] = "异步任务当前进程 {$task_id} 执行完成";
        \ultraman\Log\monoLog::write("INFO", $taskdata);
    }


    public function isRunning()
    {
        if (!file_exists($this->pid)) {
            return false;
        }

        $pid = file_get_contents($this->pid);
        return (bool) posix_getpgid($pid);
    }


    private function getPid()
    {
        $this->climate = new CLImate();
        $config = @parse_ini_file(APPLICATION_PATH."/conf/main.ini", true);
        $this->pid = APPLICATION_PATH.'/'.$config['common']['application.service_name'].'-tcp-service.pid';
        if (!file_exists($this->pid)) {
            $this->climate->error("没有这个进程");
            die;
        }
        $pid = file_get_contents($this->pid);
        if (posix_getpgid($pid)) {
            return $pid;
        }
        unlink($this->pid);
        return false;
    }

    public function stop()
    {
        $pid = $this->getPid();
        posix_kill($pid, SIGTERM);
        usleep(500);
        posix_kill($pid, SIGKILL);
        unlink($this->pid);
        return false;
    }
    
    public function reload()
    {
        posix_kill($this->getPid(), SIGUSR1);
        return true;
    }
}
