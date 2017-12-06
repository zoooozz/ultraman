<?php


/**
 * @var 异常捕获 
 * 当程序出现错误时候 立刻被捕获
 */

use ultraman\Foundation\Response;
class ErrorController extends \Yaf_Controller_Abstract
{
    public function errorAction($exception)
    {  
        $params = [
        	'code'=>$exception->getCode(),
        	'msg'=>$exception->getMessage()
        ];
   		
        header('Content-Type: application/json; charset=utf-8');
    	echo Response::_end($params,(string)$params['code']);
    }
}