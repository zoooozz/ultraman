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
use League\CLImate\CLImate;

class App
{
    /**
     * 构造函数 入口加载配置文件进行注入
     *
     * @param db    数据库配置 
     * @param main  服务配置项
     * @param api   外部接口配置项
     */

    protected $_config = [];
    protected $climate;
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
        $this->_config = $config;
        return true;
    }


    /**
     *  服务器启动 
     */

    public function ServiceRun($params)
    {
        $this->climate = new CLImate();
        $service = strtolower(trim(isset($params[1])?$params[1]:''));
        $command = strtolower(trim(isset($params[2])?$params[2]:''));
        $env = strtolower(trim(isset($params[3])?$params[3]:''));
        $this->configure($env,$command);
        if($service == 'h'){
            if($command == 's'){
                $app = new \ultraman\Http\HttpYafServer();
            }
            if($command == 'r'){
                $this->reloadSwoole($service);
            }
        }
        if($service == 't'){
            if($command == 's'){
                $app = new \ultraman\Tcp\SwooleServer($params);
            }

            if($command == 'r'){
                $this->reloadSwoole($service);
            }
        }
        die;
    }
    //重启
    private function reloadSwoole($service)
    {
        if(PHP_OS == 'Darwin'){
            $this->climate->red('重启命令暂时不支持mac电脑');
            die;
        }
        $config = DI::get('main');
        $name = $config['common']['application.service_name'];
        if($name == ''){
            $this->climate->red('配置信息不完整 无法重启');
            return;
        }

        if($service == 't'){
            $name = $name.'tcp';
        }
        $pid = exec('pidof'.' '.$name);
        exec("kill -USR1 ".$pid);
        $this->climate->lightGreen('当前环境重启成功');
        die;
    }
    //配置文件
    private function configure($env,$command)
    {   
        if($env == '' && $command == 's'){
            $this->climate->red('启动时候必须选择环境');
            return;
        }

        if($env == '' && $command == 'r'){
            return true;
        }
        $_supprot=['dev','test','pre','prod'];
        if(!in_array(trim($env),$_supprot)){
            $this->climate->red('环境必须选择:dev,test,pre,prod');
            return;
        }
        $config = dirname(dirname(dirname(dirname(__FILE__))));
        $conf = dir($config.'/conf');
        $env = dir($config.'/env/'.$env);

        while(false != ($item = $conf->read())) {
            if($item == '.' || $item == '..') {
                continue;
            }
            unlink($conf->path.'/'.$item);
        }

        while(false != ($item = $env->read())) {
            if($item == '.' || $item == '..') {
                continue;
            }
            copy($env->path.'/'.$item,$conf->path.'/'.$item);
        }
        return true;
    }
}
