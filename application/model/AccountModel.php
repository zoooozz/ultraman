<?php 
namespace model;
use ultraman\Common\CommonFun;
use ultraman\Db\Model;

class AccountModel  extends Model
{
    public function __construct()
    {
        parent::__construct('partner');
    }
    protected static $_supports = ['id','ctime','mtime','phone','open_id','type','photo','username','sex','unionid', ];

    public function getByAccountIds($uid)
    {
        $_supports = CommonFun::Quotes('`',static::$_supports);
        $condition = CommonFun::Quotes('"',$uid);
        $items = $this->query("SELECT {$_supports} FROM `account` WHERE id IN ({$condition})")->fetchAll(\PDO::FETCH_ASSOC);
        return $items;
    }



}   