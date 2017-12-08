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
