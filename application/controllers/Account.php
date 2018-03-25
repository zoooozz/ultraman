<?php 

class AccountController  extends ultraman\Yaf\BaseController
{
    public function indexAction()
    {
        throw new \Exception("API Call Error",Ecode::ERROR);
    }

   
}

// class AccountController  extends ultraman\Yaf\BaseController{

//     /**
//      * 用户详情
//      */
//     public function detailAction(){
//         $params = $this->get();
//         if(empty($params['uid'])){
//             throw new \Exception("参数uid不能为空",Ecode::PARAMS_ERROR);
//         }
//         $item['id'] = $params['uid'];
//         $resp = Account::getByFetch($item);
//         $this->output(['data'=>$resp,'msg'=>"success"],Ecode::OK);
//     }

//     /**
//      * 用户列表
//      */
//     public function listAction(){
//         $params = $this->get();
//         //分页参数
//         $pageCoditions['page'] = isset($params['page'])?$params['page']:1;
//         $pageCoditions['pagesize'] = isset($params['pagesize'])?$params['pagesize']:20;
//         $pageCoditions['page'] = ($pageCoditions['page'] * $pageCoditions['pagesize']) - $pageCoditions['pagesize'];

//         //订单参数
//         !isset($params['id']) OR $coditions['id'] = $params['id'];//用户账号
//         !isset($params['username']) OR $coditions['username'] = $params['username'];//用户昵称
//         !isset($params['ctime']) OR $coditions['ctime'] = $params['ctime'];//用户昵称

//         $resp = Account::getListByParams($coditions,$pageCoditions);
//         $this->output(['data'=>$resp,'msg'=>"success"],Ecode::OK);
//     }
// }


// <?php
// /**
//  * @author : yb
//  * @date : 2018/3/1513:20
//  * @description :
//  */
// namespace dao\logic\Sale;

// use model\Sale\AccountModel;

// class Account{
//     /**获取所有**/
//     public static function getByFetchAll(){

//     }

//     /**
//      * 获取单条
//      */
//     public static function getByFetch($params){
//         return (new AccountModel())->getByFetch($params);
//     }

//     /**
//      * 分页获取
//      */
//     public static function getListByParams($conditions,$pageCoditions){
//         $model = new AccountModel();
//         $resp['list'] =  $model->getListByParams($conditions,$pageCoditions);
//         $cnt = $model->countWhere($conditions);
//         $resp = array_merge($resp,$cnt);
//         return $resp;
//     }
// }




// <?php
// /**
//  * Created by PhpStorm.
//  * User: win7
//  * Date: 2018/3/15
//  * Time: 13:22
//  */
// namespace model\Sale;
// use ultraman\Common\CommonFun;
// use ultraman\Db\Model;

//  public function getListByParams($params=[],$pageCoditions)
//     {
//         $_supports = CommonFun::Quotes('`',static::$_supports);
//         $page = $pageCoditions['page'];
//         $pagesize = $pageCoditions['pagesize'];
//         $where = $this->condition($params);
//         $sql ="SELECT $_supports  FROM `account`    $where   LIMIT {$page},{$pagesize}";
//         $items = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
//         return $items;
//     }

//     /**
//      * 获取单条
//      */
//     public function getByFetch($params=[])
//     {
//         $items = $this->select(static::$_supports,"`account`",$params)->fetch(\PDO::FETCH_ASSOC);
//         return $items;
//     }

//     public function getListByIds($ids)
//     {
//         $items = $this->query("SELECT id,phone,username,open_id FROM `account` where id IN ($ids) ")->fetchAll(\PDO::FETCH_ASSOC);
//         return $items;
//     }

//     /**
//      * 获取总数
//      * @return mixed
//      */
//     public function countWhere($params=[]){
//         $where = $this->condition($params);
//         $sql ="SELECT count(id) as `count` FROM `account`    $where  ";
//         $count = $this->query($sql)->fetch(\PDO::FETCH_ASSOC);
//         return $count;
//     }




// }




//     /**
//      * 单个订单
//      */
//     public function  getOrder($params = []){
//         return $this ->select(static::$_supports,"`order`",$params)->fetch(\PDO::FETCH_ASSOC);
//     }

