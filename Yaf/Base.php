<?php

/**
 * @var 初始化类 为其他类提供继承
 * init 初始化
 */
namespace ultraman\Yaf;

use ultraman\Foundation\Response;
class BaseController extends \Yaf_Controller_Abstract
{

    protected $route_method_map = [];


    /**
     *  方法的方式验证 
     */


    public function init()
    {
        $request = $this->getRequest();
        if(count($this->route_method_map) == 0 || !isset($this->route_method_map[$request->action])){
            return true;
        }
        $action = strtolower($request->action);
        if (!in_array($request->method, $this->route_method_map[$request->action])) {
            throw new \Exception("Method Not Allowed",500);
        }

    }

   
    /**
     *  输出结果信息
     */

    public function output($params,$code = '401')
    {        
        $params['ts'] = (string)time();    
        $params['code'] = $code;    
        $this->getResponse()->data = $params;
    }



    /**
     *  get
     */

    public function get($name = '')
    {   

        if(count($_GET) == 0){
            $params = \Yaf_Registry::get('REQUEST_GET');
        }else{
            $params = $_GET;
        }
        
        if($name != '' && isset($params[$name])){
            $params = $params[$name];
        }
        return $params;
    }


    /**
     * post
     */


    public function Post($name='')
    {
        if(count($_POST) == 0){
            $params = \Yaf_Registry::get('REQUEST_POST');
        }else{
            $params = $_POST;
        }
        if($name != '' && isset($params[$name])){
            $params = $params[$name];
        }
        return $params;
    }

    /**
     * task 任务执行
     */


    public function task($params)
    {
        $http = \Yaf_Registry::get('swoole_http');
        $http->task($params);
        return true;
    }

}
