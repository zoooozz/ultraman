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

	public static function FilterNULL($keys,$params)
    {
        if(count($keys) == 0 || count($params) == 0 ){
            return $params;
        }

        $item = [];
        foreach ($keys as $key => $value) {
            if(!isset($params[$value])){
                continue;
            }
            if($params[$value] !== ""){
                $item[$value] = $params[$value];
            }
        }
        
        return $item;
    }

    public static function Types($params,$types)
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

    public static function explode_keys($key,$params)
    {
        $items = [];
        if($params == ""){
            return $items;
        }

        $item = explode($key, $params);
        foreach ($item as $key => $value) {
            $items[]=trim($value);
        }

        return $items;
    }

    public static function Quotes($keys,$params)
    {
        if($keys == ""){
            $keys = "'";
        }

        $str = '';
        foreach ($params as $key => $value) {
            $str.=$keys.$value.$keys.',';
        }
        $str =  substr($str, 0, -1);
        return $str;
    }
}