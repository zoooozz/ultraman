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
        if (!isset($header['appkey']) || $header['appkey'] !=$common['appkey']) {
            throw new \Exception("header sign is Error", 400);
        }
        $timestamp = $header['timestamp'];
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
        $sign = md5($sign);
        if ($sign == $header['sign']) {
            return true;
        }
        \ultraman\Log\monoLog::write("INFO", $params);
        
        throw new \Exception("sign is Error", 400);
    }
}
