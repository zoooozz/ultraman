<?php

/**
 * Response 协议类
 * 
 * @package   ultraman\Foundation
 * @copyright Copyright (c) 2017, ultraman
 */



namespace ultraman\Foundation;

class Response
{
	/**
	 * 标准json 输出
	 * @param $params array 输出的参数
	 * @param $code string 错误代码
	 */

	public static function _end($params,$code)
	{
		$params['code'] = $code;
        return  json_encode($params);
	}

}