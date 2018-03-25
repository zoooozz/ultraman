<?php 
namespace dao;

use model\OrderModel;
use model\AccountModel;
class OrderHandle
{

    public static function getByOrderList($params)
    {
        $mode = (new OrderModel());
        $items = [];
        $resp = $mode->getByList($params);
        $list = [];
        if(count($resp)!=0){
            $uid = array_unique(array_column($resp,'uid')); 
            $account = (new AccountModel())->getByAccountIds($uid);

            if(count($account)!=0){
                $account = array_column($account,NULL,'id');
            }
            foreach ($resp as $key => $value) {
               $value['username'] = "未知用户";
               if (count($account)!=0 && isset($account[$value['uid']])){
                    $value['username'] = $account[$value['uid']]['username'];
               }
               $list[]=$value;
            }
        }
        $count = $mode->getByListCount($params);
        $items['count'] = $count['count'];
        $items['list'] = $list;
        return $items;
    }
}

