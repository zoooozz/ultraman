<?php

$name = Yaf_Registry::get('main')['application']['service_name'];

define('VERSION_V1', '/x/outter/'.$name.'/v1');

// $route['index'] = new Yaf_Route_Rewrite(VERSION_V1.'/index$',['controller'=>'Index','action'=>'index']);
// $route['index2'] = new Yaf_Route_Rewrite(VERSION_V1.'/index2$',['controller'=>'Index','action'=>'index2']);
return $route;
