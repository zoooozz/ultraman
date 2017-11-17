<?php 

/**
 * Mysql 数据库链接类
 * 封装了数据库连接以及一些简单的查询方法 
 *
 * @package   ultraman\Foundation
 * @copyright Copyright (c) 2017, ultraman
 */

namespace ultraman\Db;

use ultraman\Foundation\Response;
use ultraman\Log\monoLog;
use ultraman\Foundation\Ecode;
use ultraman\Foundation\DI;

class Model
{
    /**
     *  @var string _database 类绑定的数据 
     */

    protected $_database = 'default';

    /**
     * @var $_db  object PDO对象
     */

    protected $_db;

    /**
     * 构造函数自动加载
     * @param $database 数据库类
     */

    public function __construct($database = 'default')
    {
        $this->_database = $database;
        $this->connect();
    }

    /**
     * PDO链接数据库
     * @param array []
     */

    protected function connect()
    {
        $db = DI::get('database');
        if($db == ""){
            throw new \Exception("数据库链接失败",Ecode::SQL_ERROR);
        }
        $config = $db[$this->_database];

        try {
            $this->_db = new \PDO(
              $config['connection_string'],
              $config['username'],
              $config['password'],
              $config['driver_options']
            );
            $this->_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this;
        } catch (\PDOException $e) {
            throw new \Exception("数据库链接失败",Ecode::SQL_LINK_ERROR);
        }
    }
    
    /**
     * 方法重载类
     * @param 函数方法
     * @param参数 
     */

    public function __call($method, $arguments)
    {   
        if ($this->_db && method_exists($this->_db, $method)) {
            return call_user_func_array([$this->_db, $method], $arguments);
        }

        return false;
    }

    /**
     * @var 初始化定义 禁止删除掉
     */

    public function init()
    {

    }

    /**
     * SQL 条件判断重组
     * @param $params array 参数
     * @param $f 判断条件 
     */

    public function condition($params, $f = '=')
    {
        if (count($params) == '') {
            return '';
        }
        $condition = 'WHERE ';
        foreach ($params as $key => $value) {
            $condition .= $key . $f . "'" . $value . "'" . ' and ';
        }
        $condition = substr($condition, 0, -4);
        return $condition;
    }

    /**
     * 预处理新增一条数据
     * @param $sentence SQL 语句
     * @param $params  对应数据
     */

    public function prepareHandle($sentence,$params)
    {   
        $stmt = $this->getStatement($sentence);
        $stmt->execute($params);
        $count = $stmt->rowCount();
        return $count ? $count : $this->lastInsertId(); 
        
    }

    /**
     * 获取PDOStatement对象
     *
     * @param  string $sql 需要Prepare的SQL语句
     * @return \PDOStatement
     */
    
    public function getStatement($sql)
    {
        $mark = md5($sql);
        if (!isset($this->_prepared[$mark])) {
            $this->_prepared[$mark] = $this->prepare($sql);
        }

        return $this->_prepared[$mark];
    }

    /**
     * 更新语句组装
     * @param 参数
     */

    public function editPretend($params, $bind = true)
    {
        $condition = '';
        if ($params == '') {
            return false;
        }
        foreach ($params as $key => $value) {
            $condition .= '`' . $key . '` =:' . $key . ',';
        }
        $condition = substr($condition, 0, -1);
        return $condition;
    }
    /**
     * 新增语句组装
     * @param 参数
     */

    public function addPretend($params)
    {
        $result = '';
        $key = array_keys($params);
        foreach ($key as $k => $v) {
            $arr[] = ":" . $v;            
        }
        $result = ' (`' . implode('`,`', $key) . '`) VALUES ('.implode(',', $arr).')';;
        return $result;
    }

    /**
     * 搜索组装
     */
    

    public function select($f=[],$tab='',$params=[])
    {
        if($tab ==""){
            return false;
        }
        $condition = $this->condition($params);
        $keys = array_keys($f);
        if($keys !== array_keys($keys)){
            $f = $keys;
        }

        $str = '';
        foreach ($f as $key => $value) {
                $str.='`'.$value.'`,';
        }
        $str = substr($str, 0, -1)?:"*";
        $items = $this->query("SELECT {$str} FROM {$tab} {$condition}");
        return $items;
    }
}
