<?php
namespace model;
use ultraman\Db\Model;
use ultraman\Common\CommonFun;

class OrderModel  extends Model
{
    public function __construct()
    {
        parent::__construct('partner');
    }
    protected static $_supports = ['id' ,'ctime' ,'mtime' ,'order_id' ,'stime' ,'uid' ,'type' ,'channel' ,'state' ];

    public function getByList($params)
    {
        $page = $params['page'];
        $pagesize = $params['pagesize'];
        unset($params['page'],$params['pagesize']);
        $condition = $this->condition($params);
        $_supports = CommonFun::Quotes('`',static::$_supports);        
        $items = $this->query("SELECT {$_supports} FROM `order` {$condition} ORDER BY ctime DESC LIMIT {$page},{$pagesize}")->fetchAll(\PDO::FETCH_ASSOC);        
        return $items;
    }

    public function getByListCount($params)
    {
        unset($params['page'],$params['pagesize']);
        $condition = $this->condition($params);
        $items = $this->query("SELECT count(id) as count FROM `order` {$condition}")->fetch(\PDO::FETCH_ASSOC);        
        return $items;
    }
}
