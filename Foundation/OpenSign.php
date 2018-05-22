<?php

/**
 * OpenSign 加密类
 *
 * @package   ultraman\Foundation
 * @copyright Copyright (c) 2017, ultraman
 */

namespace ultraman\Foundation;

use ultraman\Log\monoLog;

class OpenSign
{
    public static function Auth($params, $common)
    {
        $header = \Yaf_Registry::get('REQUEST_HEADER');
        \ultraman\Log\monoLog::write("INFO", 'server__'.json_encode($header));
        
        if (!isset($header['appkey']) || $header['appkey'] !=$common['appkey']) {
            throw new \Exception("header sign is Error", 400);
        }

        if ($header['appkey'] == $common['appkey'] && isset($header['appsecret']) && $header['appsecret']!="" && $header['appsecret'] == $common['appsecret']) {
            return true;
        }
        
        $timestamp = isset($header['timestamp'])?$header['timestamp']:0;
        $time = time();
        $last = $time - 10 * 60;
        $next = $time + 10 * 60;
        if ($timestamp < $last || $timestamp > $next) {
            throw new \Exception("time sign is Error", 400);
        }
        ksort($params, SORT_STRING);
        $sign = $common['appkey']."#x#".$common['appsecret'].'#f'.md5($common['appkey'])."#f";
        foreach ($params as $k => $v) {
            $sign .= ($k . '=' . urlencode(urldecode($v)));
        }
        \ultraman\Log\monoLog::write("INFO", 'sign:_'.$sign);
        \ultraman\Log\monoLog::write("INFO", $params);
        
        $sign = md5($sign);
        if ($sign == $header['sign']) {
            return true;
        }
        
        throw new \Exception("sign is Error", 400);
    }
}
