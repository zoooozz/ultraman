<?php 

use dao\OrderHandle;
use dao\Ecode;
class OrderController  extends ultraman\Yaf\BaseController
{
    public function indexAction()
    {
        throw new \Exception("API Call Error",Ecode::ERROR);
    }

    public function ListAction()
    {
        $params = $this->get();
        if(isset($params['order_id']) && $params['order_id']!=''){
            $item['order_id'] = $params['order_id'];
        }
        if(isset($params['uid']) && $params['uid']!=''){
            $item['uid'] = $params['uid'];
        }
        if(isset($params['phone']) && $params['phone']!=''){
            $item['phone'] = $params['phone'];
        }
        if(isset($params['state']) && $params['state']!=''){
            $item['state'] = $params['state'];
        }
        if(isset($params['type']) && $params['type']!=''){
            $item['type'] = $params['type'];
        }
        if(isset($params['channel']) && $params['channel']!=''){
            $item['channel'] = $params['channel'];
        }
        if(isset($params['stime']) && $params['stime']!=''){
            $item['stime'] = $params['stime'];
        }
        $item['page'] = isset($params['page'])?$params['page']:1;
        $item['pagesize'] = isset($params['pagesize'])?$params['pagesize']:20;
        $item['page'] = ($item['page'] * $item['pagesize']) - $item['pagesize'];
        $resp = OrderHandle::getByOrderList($item);
        $this->output(['data'=>['list'=>$resp['list'],'count'=>$resp['count']],'msg'=>"success"],Ecode::OK);
    }

    public function detailAction()
    {
        $params = $this->get();
        if(!isset($params['order_id']) && $params['order_id'] == ''){
            throw new \Exception("order_id 是必须传入的条件",Ecode::ERROR);
        }
        $order_id = $params['order_id'];
        $resp = OrderHandle::getByOrderLDateil($item);
        $this->output(['data'=>$resp,'msg'=>"success"],Ecode::OK);        
    }

}



    // /**
    //  * 订单详情
    //  */
    // public function detailAction(){
    //     $params = $this->get();
    //     if(empty($params['id'])){
    //         throw new \Exception("订单id不能为空",Ecode::PARAMS_ERROR);
    //     }
    //     $item['id'] = $params['id'];
    //     $resp = Order::getOrder($item);
    //     $this->output(['data'=>$resp,'msg'=>"success"],Ecode::OK);
    // }



//     <?php
// /**
//  * @author : yb
//  * @date : 2018/3/1513:20
//  * @description :
//  */
// namespace dao\logic\Sale;

// use model\Sale\AccountModel;
// use model\Sale\OrderModel;

// class Order{
//     /**获取所有**/
//     public static function getByFetchAll(){
//        return  (new OrderModel())->getByList();
//     }

//     /**分页获取**/
//     public static function getListByParams($conditions = [] ,$pageCoditions = []){
//         $model = new OrderModel();
//         $resp['list'] =$model->getListByParams($conditions,$pageCoditions);
//         $cnt = $model->countWhere($conditions);
//         $resp = array_merge($resp,$cnt);
//         return $resp;
//     }

//     /**
//     * 订单状态总数
//      */
//     public static function  getStatesCnt(){
//         return  (new OrderModel())->getStateCnt();
//     }

//     /**
//      *  关联用户信息
//      *  (暂时不用 先用join查询)
//      * @param $orderInfo
//      * @return mixed
//      */
//     public static function getOwnerInfo($orderInfo){
//         if(empty($orderInfo)){
//             return $orderInfo;
//         }
//         $userIds = implode(',',array_column($orderInfo,'uid'));
//         $userInfo = (new AccountModel())->getListByIds($userIds);
//         foreach ($orderInfo as &$orderRow){
//             foreach ($userInfo as $userRow){
//                 if($orderRow['uid'] == $userRow['id']){
//                     $orderRow['phone'] = $userRow['phone'];
//                     $orderRow['username'] = $userRow['username'];
//                     $orderRow['phone'] = $userRow['phone'];
//                     $orderRow['open_id'] = $userRow['open_id'];
//                 }
//             }
//         }
//         return $orderInfo;
//     }

//     /**
//      * 单个订单
//      */
//     public static function getOrder($params){
//         return (new OrderModel())->getOrder($params);
//     }
// }