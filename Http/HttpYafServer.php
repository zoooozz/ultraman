<?php 

/**
 *  架起Swoole Http yaf 版本服务器
 */

namespace ultraman\Http;

define('AUTHLOAD',dirname(dirname(dirname(dirname(__FILE__)))));
define('APPLICATION_PATH',dirname(dirname(dirname(dirname(dirname(__FILE__))))));

require AUTHLOAD.'/autoload.php';

class HttpYafServer
{
	protected $application;
	public function __construct() 
	{	
		new \ultraman\App();
		$this->run();
	}

	private function run()
	{	

		$config = @parse_ini_file(APPLICATION_PATH."/conf/main.ini",true);
		$http_service = $config['http-service'];
		$app = new \ultraman\Http\SwooleHttpServer($http_service);
		$http = $app->connent();
        \Yaf_Registry::set('swoole_http', $http);
		$http->on('WorkerStart' , array( $this , 'onWorkerStart'));    
        $http->on('request', array($this, 'onRequest'));
        $http->on('Start', array($this, 'onStart'));
		$http->on('task', array($this, 'onTask'));
        $http->on('finish', array($this, 'onFinish'));
		$http->start();
	}


    public function onRequest($request,$response)
    {
        if ($request->server['request_uri'] == '/favicon.ico') {
            $response->end();
            return;
        }

        $this->initRequestParam($request);
        ob_start();
          try {

            $yaf_request = new \Yaf_Request_Http($request->server['request_uri']);
            $this->application->getDispatcher()->dispatch($yaf_request);

        } catch ( \Exception $e ) {
            $params = [
                'code'=>$e->getCode(),
                'msg'=>$e->getMessage(),
                'errcode'=>$e->getCode(),
                'errmsg'=>$e->getMessage(),
               ];
            echo  json_encode($params,JSON_UNESCAPED_UNICODE);
        }
        $result = ob_get_contents();
        ob_end_clean();
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->end($result);
    }


	public function onWorkerStart($serv, $worker_id) 
	{        
		$config = APPLICATION_PATH.'/conf/main.ini';
        try {
            $this->application = new \Yaf_Application($config);
            ob_start();
            $this->application->bootstrap()->run();
            ob_end_clean();
        }catch (\Exception $e) {
            $params = [
                'code'=>$e->getCode(),
                'msg'=>$e->getMessage(),
                'errcode'=>$e->getCode(),
                'errmsg'=>$e->getMessage()
            ];
            echo  json_encode($params,JSON_UNESCAPED_UNICODE);
        }
		
	}

    public function onStart( $serv ) {
        $config = @parse_ini_file(APPLICATION_PATH."/conf/main.ini",true);
        $name = $config['common']['application.service_name'];
        echo $name."服务启动\n";
        @cli_set_process_title($name);
    }


	private function initRequestParam(\swoole_http_request $request)
    {
        $server = isset($request->server) ? $request->server : array();
        $header = isset($request->header) ? $request->header : array();
        $get    = isset($request->get) ? $request->get : array();
        $post   = isset($request->post) ? $request->post : array();
        $cookie = isset($request->cookie) ? $request->cookie : array();
        $files  = isset($request->files) ? $request->files : array();
        \Yaf_Registry::set('REQUEST_SERVER', $server);
        \Yaf_Registry::set('REQUEST_HEADER', $header);
        \Yaf_Registry::set('REQUEST_GET', $get);
        \Yaf_Registry::set('REQUEST_POST', $post);
        \Yaf_Registry::set('REQUEST_COOKIE', $cookie);
        \Yaf_Registry::set('REQUEST_FILES', $files);
        \Yaf_Registry::set('REQUEST_RAW_CONTENT', $request->rawContent());
        return true;
    }

    public function onTask($serv, $task_id, $from_id,$data)
    {	
        if(isset($data['cli']) && $data['cli'] != ''){
            $cli = $data['cli'];
            $params = '';
            if(isset($data['params']) && count($data['params'])!=0 && is_array($data['params'])){
                foreach ($data['params'] as $key => $value) {
                    if(trim($value) == '')continue;
                    $params.="--{$key} ".$value." ";
                }
            }
            $path = 'php '.APPLICATION_PATH.'/cli '.$cli.' '. $params;            
        }else{
            call_user_func_array($data['class'],[$data]);
        }
        
        $items['msg'] = "异步任务[来自进程 {$from_id}，当前进程 {$task_id}";
        $taskdata = serialize($items);
  		$serv->finish($taskdata);
    }
    
    public function onFinish($serv, $task_id, $data)
    {
       $params = unserialize($data);
       $taskdata['msg'] = "异步任务当前进程 {$task_id} 执行完成";
  	   \ultraman\Log\monoLog::write("INFO",$taskdata);
    }

}