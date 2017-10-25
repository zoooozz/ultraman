<?php 

/**
 * DataValidator 过滤器
 * 
 * @package   ultraman\Tools
 * @copyright Copyright (c) 2017, ultraman
 */

namespace ultraman\Common;

class DataValidator
{
	/**
	 * Emoji表情处理
     * @param $str 处理的表情字符
	 */

	public static function filterEmoji($str)
    {
        $str = preg_replace_callback(
                '/./u',
                function (array $match) {
                    return strlen($match[0]) >= 4 ? '' : $match[0];
                },
                $str);

         return $str;
    }

    

    
	
}