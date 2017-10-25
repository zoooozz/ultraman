<?php


/**
 * DbTransaction 事物处理器
 * 
 * @package   ultraman
 * @copyright Copyright (c) 2017, ultraman
 */

namespace ultraman\Db;

class DbTransaction
{

    /**
     * @var _db 数据库配置
     */
    protected $_db;

    /**
     * @var _instance PDO对象
     */

    protected static $_instance;

    protected $transactions;

    private function __construct($output)
    {
        $this->_db = $output;
    }

    private function __clone()
    {

    }

    /**
     * 单例模式初始化
     * @param string $output
     */

    public static function getInstance($output = '')
    {
        if (static::$_instance == null) {
            // 后期静态绑定
            static::$_instance = new static($output);
        }

        return static::$_instance;
    }

    /**
     * 开始一个事务
     */

    public function beginTransaction()
    {
        if ($this->transactions == 0) {
            $this->_db->beginTransaction();           
        } elseif ($this->transactions >= 1) {
            $this->_db->exec(
                $this->compileSavePoint('trans' . ($this->transactions + 1))
            );
        }

        ++$this->transactions;

    }

    /**
     * 提交事务
     */
    public function commit()
    {
        if ($this->transactions == 1) {
            $this->_db->commit();
        }
        $this->transactions = max(0, $this->transactions - 1);
    }

    /**
     * 事务回滚
     */
    public function rollBack()
    {

        if ($this->transactions == 1) {
            $this->_db->rollBack();
        } elseif ($this->transactions > 1) {
            $this->_db->exec(
                $this->compileSavepointRollBack('trans' . $this->transactions)
            );
        }
        $this->transactions = max(0, $this->transactions - 1);
    }

    /**
     * 生成数据库保存点的语句
     * @param string $name
     * @return string
     */
    public function compileSavePoint($name)
    {
        return 'SAVEPOINT ' . $name;
    }

    /**
     * 生成回滚保存点的语句
     * @param string $name
     * @return string
     */
    
    public function compileSavepointRollBack($name)
    {
        return 'ROLLBACK TO SAVEPOINT ' . $name;
    }

    /**
     * 获得事务的层级
     */
    
    public function transactionLevel()
    {
        return $this->transactions;
    }
}