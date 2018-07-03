<?php 

/**
 * CommonFun 公用方法
 *
 * @package   ultraman\Tools
 * @copyright Copyright (c) 2017, ultraman
 */

namespace ultraman\Common;

class CommonFun
{
    /**
     * 数据过滤
     * @param array $keys
     * @param array $params
     */

    public static function FilterNULL($keys, $params)
    {
        if (count($keys) == 0 || count($params) == 0) {
            return $params;
        }

        $item = [];
        foreach ($keys as $key => $value) {
            if (!isset($params[$value])) {
                continue;
            }
            if ($params[$value] !== null) {
                $item[$value] = $params[$value];
            }
        }
        
        return $item;
    }

    /**
     *  类型转换
     */

    public static function Types($params, $types)
    {
        switch ($types) {
            case 'int':
                $items = (int)$params;
            break;
            case 'bool':
                $items = (bool)$params;
            break;
            case 'float':
                $items = (float)$params;
            break;
            case 'string':
                $items = (string)$params;
            break;
            case 'unset':
                $items = (unset)$params;
            break;
            case 'object':
                $items = (object)$params;
            break;
            default:
               $items = (string)$params;
            break;
        }
        return $items;
    }

    /**
     *  重新封装分割
     */

    public static function explode_keys($key, $params)
    {
        $items = [];
        if ($params == "") {
            return $items;
        }

        $item = explode($key, $params);
        foreach ($item as $key => $value) {
            $items[]=trim($value);
        }

        return $items;
    }

    /**
     *  为参数或字段加引号 或者特殊符号
     */

    public static function Quotes($keys, $params)
    {
        if ($keys == "") {
            $keys = "'";
        }

        $str = '';
        foreach ($params as $key => $value) {
            $str.=$keys.$value.$keys.',';
        }
        $str =  substr($str, 0, -1);
        return $str;
    }

    /**
     *  获取毫秒级时间戳
     */

    public static function microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
}
