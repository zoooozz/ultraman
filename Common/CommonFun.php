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
        if(count($keys) == 0){
            return $params;
        }

        $item = [];
        foreach ($keys as $key => $value) {
            if($params[$value] !== "" && $params[$value]!=null && $params[$value]!=undefined){
                $item[$value] = $params[$value];
            }
        }
        
        return $item;
    }	
}