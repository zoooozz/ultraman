<?php 

/**
 * DataValidator 过滤器
 * 
 * @package   ultraman\Tools
 * @copyright Copyright (c) 2017, ultraman
 */

namespace ultraman\Common;
use ultraman\Common\CommonFun;

class DataValidator
{

    const INT = 'int';
    const NUMBER = 'number';
    const BOOL = 'bool';

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

    
    public static function Student($_supports,$params)
    {
        $items = [];
        foreach ($_supports as $key => $value) {
            if(is_array($value) || $value == ""){
                continue;
            }
            $count = substr_count($value,'|'); 
            if($count!=0){
                $str = CommonFun::explode_keys("|",$value);
            }else{
                $str[0] = trim($value);
                $str[1] = "string";
            }
            $item = isset($params[$str[0]])?$params[$str[0]]:"";
            $items[$key] = CommonFun::Types($item,$str[1]);
        }
        return $items;
    }

    public static function validate($variable, $accept_type, $required = false)
    {
        if ($required) {
            if (!isset($variable)) {
                return false;
            }
        }
        if ($accept_type == self::INT) {
            return preg_match('/^[0-9]+$/', $variable);
        } elseif ($accept_type == self::NUMBER) {
            return preg_match("/^[0-9]+\.?[0-9]*$/", $variable);
        } elseif ($accept_type == self::BOOL) {
            return preg_match('/^[01]+$/', $variable) || $variable == 'true' || $variable == 'false';
        } else {
            return true;
        }
    }

    
	
}