<?php

/**
 *  架起Swoole Tcp yaf 版本服务器
 */

namespace ultraman\Tcp;

define('AUTHLOAD',dirname(dirname(dirname(dirname(__FILE__)))));
define('APPLICATION_PATH',dirname(dirname(dirname(dirname(dirname(__FILE__))))));
require AUTHLOAD.'/autoload.php';

class SwooleServer
{
	public function __construct() 
	{	
		new \ultraman\App();
		$this->run();
	}

	public function run()
	{
		$config = @parse_ini_file(APPLICATION_PATH."/conf/main.ini",true);
		$main = $config['tcp-service'];
		$name = $config['common']['application.service_name'].'tcp';
		$server = new \Hprose\Swoole\Server("tcp://".$main['host'].':'.$main['port']);
        \Yaf_Registry::set('swoole_tcp', $server);

		$server->set(
			array(
				'worker_num' => $main['worker_num']?:3,
				'daemonize' => $main['daemonize'],
	            'max_request' => $main['max_request']?:10000,
	            'dispatch_mode' => $main['dispatch_mode'],
	            'task_worker_num'=>$main['task_worker_num']?:4,
	            'task_ipc_mode'=>$main['task_ipc_mode']?:1,
	            'task_max_request'=>$main['task_max_request']?:5000,
			)
		);
		$server->on('task', array($this, 'onTask'));
        $server->on('finish', array($this, 'onFinish'));

		$server->addInstanceMethods(new $main['func']);
		$server->setGetEnabled=true;
		$server->setDebugEnabled(false);
		$server->setErrorTypes(E_ALL);
	    @cli_set_process_title($name);
		$server->start();
	}

	public function onTask($serv, $task_id, $from_id, array $taskdata)
    {	

    	if($taskdata['cli'] == ''){
    		return false;
    	}
    	$cli = $taskdata['cli'];
    	$params = '';
    	if($taskdata['params']!=''){
    		foreach ($taskdata['params'] as $key => $value) {
                if(trim($value) == ''){
                    continue;
                }
    			$params.="--{$key} ".$value." ";
    		}
    	}
    	$path = 'php '.APPLICATION_PATH.'/public/cli '.$cli.' '. $params;
    	$app = exec($path);
  		$taskdata['msg'] = "异步任务[来自进程 {$from_id}，当前进程 {$task_id}";
  		\ultraman\Log\monoLog::write("INFO",$taskdata);
  		$taskdata = serialize($taskdata);
  		$serv->finish($taskdata);

    }

    public function onFinish($serv, $task_id, $data)
    {
       $params = unserialize($data);
       $taskdata['msg'] = "异步任务当前进程 {$task_id} 执行完成";
  	   \ultraman\Log\monoLog::write("INFO",$taskdata);
    }
}
