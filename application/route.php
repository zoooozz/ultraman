<?php

$name = Yaf_Registry::get('main')['application']['service_name'];

define('VERSION_V1', '/x/outter/'.$name.'/v1');
$route['index'] = new Yaf_Route_Rewrite(VERSION_V1.'/index$',['controller'=>'Index','action'=>'index']);
//订单相关
$route['order_list'] = new Yaf_Route_Rewrite(VERSION_V1.'/order/list',['controller'=>'Order','action'=>'list']);
$route['order_detail'] = new Yaf_Route_Rewrite(VERSION_V1.'/order/list',['controller'=>'Order','action'=>'detail']);


return $route;
