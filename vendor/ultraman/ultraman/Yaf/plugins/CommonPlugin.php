<?php

/**
 *  公用插件管理
 */
namespace ultraman\Yaf\plugins;

class CommonPlugin extends \Yaf_Plugin_Abstract
{

	/**
	 *  路由开始前
	 */

    public function routerStartup(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
        $request->starttime = round(microtime(true) * 1000);        
        $params = \Yaf_Registry::get('REQUEST_GET');
        $request->jsonp = isset($params['callback'])?$params['callback']:'';
    }

    /**
     *  路由结束之后触发
     */

    public function routerShutdown(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {	

    }

    /**
     * 分发循环开始之前被触发
     */

    public function dispatchLoopStartup(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {

    }

    /**
     *  分发之前触发
     */


    public function preDispatch(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
  
    }	

    /**
     *  分发结束之后触发
     */

    public function postDispatch(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {

    }

    /**
     * 分发循环结束之后触发
     */

    public function dispatchLoopShutdown(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
        // if(!isset($response->data)){
            // echo 1;
            // return;
        // }
        $data = $response->data;        
        $interval = round(microtime(true) * 1000) - $request->starttime;
        $data['s'] = $interval.'ms';
        $result = json_encode($data);
        if($request->jsonp){
            $params = \Yaf_Registry::get('REQUEST_GET');
            $result = $params['callback'] . "(" . $result . ")";
        }
        echo $result;
       
    }

}