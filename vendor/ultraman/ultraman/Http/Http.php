<?php

/**
 * Http 链接
 * 
 * @package   ultraman\Tools
 * @copyright Copyright (c) 2017, ultraman
 */


namespace ultraman\Http;

use ultraman\Foundation\Response;
use ultraman\Foundation\Ecode;
use ultraman\Log\monoLog;


/**
 * Http请求类
 * 封装了常用的CURL操作
 *
 * @package moondog\Tools
 */

class Http
{
    /**
     * @var array $data config data
     */

    protected $data = [];

    /**
     * @var string $api_path 请求路径
     */

    protected $api_path = '';

    /**
     * @var string $url 请求地址
     */

    protected $url = '';

    /**
     * @var array $headers 请求头部
     */

    protected $headers = [];

    /**
     * @var bool $withHeader 设置是否将返回头包含在输出中
     */

    protected $withHeader = false;

    /**
     * @var array $query 请求参数
     */
    
    protected $query = [];

    /**
     * @var array $info curl_getinfo
     */
    protected $info = null;

    /**
     * @var array $info curl error
     */
    protected $error = null;
    /**
     * @var array 返回码
     */
    protected $httpCode = 200;


    /**
     * @var string  接口作者
     */
    protected $author = '';

    /**
     * @var bool  是否中断抛异常
     */
    protected $throw = false;

    /**
     * @var array $opts curl设置参数
     */
    protected $opts = [
        'dns_use_global_cache' => true,
        'dns_cache_timeout' => 300,
        'returntransfer' => true,
        'failonerror' => true,
        'maxredirs' => 5,
        'connecttimeout' => 3,
        'timeout' => 3,
        'retry' => 0,
        'cookie'=>'',
        'user_agent'=>'',
    ];

    /**
     * 获取curl句柄，可设置自定义参数
     *
     * @param  array $opts curl参数
     * @return resource
     */
    protected function getHandler($opts = [])
    {
        $ch = curl_init();
        $opts = array_merge($this->opts, $opts);        
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, $opts['dns_use_global_cache']);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, $opts['dns_cache_timeout']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $opts['returntransfer']);
        curl_setopt($ch, CURLOPT_FAILONERROR, $opts['failonerror']);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $opts['maxredirs']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $opts['connecttimeout']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $opts['timeout']);
        curl_setopt($ch, CURLOPT_COOKIE, $opts['cookie']);
        if (isset($opts['proxy_server']) && $opts['proxy_server']) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, $opts['proxy_type'] ? $opts['proxy_type'] : CURLPROXY_SOCKS4);//使用了SOCKS5代理    0/4/5
            curl_setopt($ch, CURLOPT_PROXY, $opts['proxy_server']);
        }
        if (isset($opts['user_agent']) && $opts['user_agent']) {
            curl_setopt($ch,CURLOPT_USERAGENT,$opts['user_agent']);           
        }
        return $ch;
    }


    public function __construct($config = [])
    {
        $this->init($config);
    }

    //基于配置文件初始化 http
    protected function init($config = [])
    {
        if ($config) {
            foreach ($config as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (method_exists($this, $method)) {
                    call_user_func([$this, $method], $value);
                }
                $this[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * 设置请求路径
     *
     * @param  string $api_path 请求路径
     * @return Http
     */
    public function setPath($api_path)
    {
        $this->api_path = $api_path;

        return $this;
    }

    public function getPath()
    {
        return $this->api_path;
    }

    /**
     * 设置请求Host
     *
     * @param  string $host 请求Host
     * @return Http
     */
    public function setHost($host)
    {
        $this->headers[] = "Host: {$host}";

        return $this;
    }

    public function setProxy($type, $ser)
    {
        $this->opts['proxy_type'] = $type;
        $this->opts['proxy_server'] = $ser;
        return $this;
    }

    public function setAuthor($author = '')
    {
        $this->author = $author;
        return $this;
    }

    public function setThrow($throw = false)
    {
        $this->throw = $throw;
        return $this;

    }

    public function setTimeOut($timeout = 3)
    {
        $this->opts['timeout'] = $timeout;
        return $this;
    }


    public function setCookie($cookie = '')
    {
        $this->opts['cookie'] = $cookie;
        return $this;

    }

    public function setUserAgent($str)
    {
        $this->opts['user_agent'] = $str;
        return $this;
    }

    public function setRetry($retry = 1)
    {
        $this->opts['retry'] = $retry;
        return $this;

    }

    /**
     * 设置请求头
     *
     * @param  string $header 设置请求头
     * @return Http
     */
    public function setHeader($header)
    {
        $this->headers[] = $header;

        return $this;
    }

    /**
     * 获取请求头
     *
     * @return array
     */
    public function getHeader()
    {
        return $this->headers;
    }

    /**
     * 设置将响应头包含到输出中
     *
     * @return Http
     */

    public function withHeader()
    {
        $this->withHeader = true;

        return $this;
    }

    /**
     * 发出GET请求
     *
     * @param  string $api API地址
     * @param  string|array $query 请求参数
     * @param  array $opts curl自定义参数
     * @return mixed
     */
    public function Get($api = '', $query = '', $opts = [])
    {
        $ch = $this->getHandler($opts);
        $query = is_array($query) ? http_build_query($query, '', '&', PHP_QUERY_RFC3986) : $query; // PHP_QUERY_RFC3986 : Space will be turn to %20
        $this->query = $query;
        $this->url = $this->api_path . $api . ($query ? "?{$query}" : '');

        if ($this->headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        return $this->run($ch, $this->opts['retry']);
    }

    /**
     * 发出POST请求
     *
     * @param  string $api API地址
     * @param  string|array $query 请求参数
     * @param  array $opts curl自定义参数
     * @return mixed
     */
    public function Post($api = '', $query = '', $opts = [])
    {
        $ch = $this->getHandler($opts);
        $query = is_array($query) ? http_build_query($query) : $query;
        $this->query = $query;
        $this->url = $this->api_path . $api;
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        if ($this->headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }
        return $this->run($ch);
    }

    public function JsonPost($api = '', $query = '', $opts = [])
    {
        $ch = $this->getHandler($opts);
        $query = is_array($query) ? http_build_query($query) : $query;

        $this->query = $query;
        $this->url = $this->api_path . $api;

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $this->setHeader('Content-Type: application/json');
        $this->setHeader('Content-Length: ' . strlen($query));
        if ($this->headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }
        return $this->run($ch);
    }

    /**
     * 发出PUT请求
     *
     * @param  string $api API地址
     * @param  string|array $file 上传文件信息
     * @param  string|array $query 请求参数
     * @param  array $opts curl自定义参数
     * @return mixed
     */

    public function Put($api = '', $file = '', $query = [], $opts = [])
    {
        $ch = $this->getHandler($opts);
        $query = is_array($query) ? http_build_query($query) : $query;
        $this->url = $this->api_path . $api . ($query ? "?{$query}" : '');

        if (is_array($file)) {
            if (isset($file['filepath'])) {
                $fp = fopen($file['filepath'], 'r');
            } elseif (isset($file['fp'])) {
                $fp = $file['fp'];
            }
            $this->headers[] = "Content-Type: {$file['filetype']}";
        } else {
            $fp = fopen($file, 'r');
            if ($img_info = getimagesize($file)) { // get mime type wher file is a image
                $this->headers[] = "Content-Type: {$img_info['mime']}";
            }
        }
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        if ($this->headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        return $this->run($ch);
    }


    function Delete($api = '', $opts = [])
    {
        $ch = $this->getHandler($opts);
        $this->url = $this->api_path . $api;
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        if ($this->headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        return $this->run($ch);

    }

    /**
     * 执行CURL请求，并设置CURL请求及错误信息
     *
     * @param  resource $ch CURL句柄
     * @return mixed
     */

    protected function run($ch, $retry = 0)
    {               
        if (!$this->isUrl($this->url)) {
            throw new \Exception("接口配置错误",Ecode::API_LINK_ERROR);
        }
        curl_setopt($ch, CURLOPT_URL, $this->url);
        if ($this->withHeader) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }
        $response = curl_exec($ch);
        while ($response === false && $retry--) {
            sleep(1);
            $response = curl_exec($ch);
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);        
        $params['HttpCode'] = $httpCode;
        $params['uri'] = $this->url;
        $params['ops'] = $this->query;        
        $this->httpCode = $httpCode;

        if ($response === false) {
            $this->error = ['errno' => curl_errno($ch), 'error' => curl_error($ch)];
            $params['errno'] = $this->error['errno'];
            $params['error'] = $this->error['error'];
        } else {
            $this->info = curl_getinfo($ch);
            $params['getinfo'] = $this->info;
        }

        monoLog::write('INFO', $params);
        curl_close($ch);
        return $response;
    }

    /**
     * 获取CURL请求信息
     *
     * @return array
     */
    public function getCurlInfo()
    {
        return $this->info;
    }

    /**
     * 获取CURL返回码
     *
     * @return array
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }


    /**
     * 获取CURL错误信息
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @var 检查是否是http
     */

    public function isUrl($str)
    {
        $str = ltrim($str);
        return in_array(substr($str, 0, 7),
            array(
                'http://',
                'https:/'
            ));
    }

    /**
     * 判断是否保存键名为$key的数据.
     *
     * @param  string $key 键名
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * 获取键名为$key的数据.
     *
     * @param  string $key 键名
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    /**
     * 设置键名为$key的数据.
     *
     * @param  string $key 键名
     * @param  mixed $value 键值
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * 移除键名为$key的数据.
     *
     * @param  string $key 键名
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * 为数据提供容器成员变量的访问方式.
     *
     * @param  string $key 键名
     * @return void
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * 为数据提供类成员变量的赋值方式.
     *
     * @param  string $key 键名
     * @param  mixed $value 键值
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * 为数据提供类成员变量的移除方式.
     *
     * @param  string $key 键名
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * 用判断类成员变量是否存在的方式检查数据.
     *
     * @param  string $key 键名
     * @return void
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }
}
