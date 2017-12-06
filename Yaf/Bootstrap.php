<?php

/**
 * @var 初始化类 为其他类提供继承
 * init 初始化
 */
namespace ultraman\Yaf;
class Bootstrap extends \Yaf_Bootstrap_Abstract
{

	//禁止使用view 
	public function _initYaf()
    {      
        \Yaf_Dispatcher::getInstance()->autoRender(false);
    }


    public function _initConfig()
    {
        $main = \Yaf_Application::app()->getConfig();
        $conf = include $main['application']['database'];
        if($conf != "" && count($conf)!=0){
            \ultraman\Foundation\DI::set('database',$conf);
        }
        $log = $main['application']['logger']['path']?:'/tmp';
        $log_name = $main['application']['service_name'];      
       	\ultraman\Foundation\DI::set("log",['path'=>$log,'name'=>$log_name]);
        \Yaf_Registry::set('main', $main);
    }
    
    //注册一个插件
    public function _initCommonPlugin(\Yaf_Dispatcher $dispatcher) {
        $common_plugin = new \ultraman\Yaf\plugins\CommonPlugin();
        $dispatcher->registerPlugin($common_plugin);
    }



    /**
     * @var 路由配置加载
     */

    public function _initRoute(\Yaf_Dispatcher $dispatcher)
    {   
        $router = \Yaf_Dispatcher::getInstance()->getRouter();
        $routes = include APPLICATION_PATH.'/application/route.php';
        foreach ($routes as $name => $route) {
            $router->addRoute($name, $route);
        }
    }

}
