<?php

return [
    //基础数据库 后台基础功能
    'partner' => [
        'connection_string' => 'mysql:host=127.0.0.1;dbname=partner-service',
        'username' => 'root',
        'password' =>'',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_PERSISTENT => false, 
            PDO::ATTR_TIMEOUT => 3600,
        ],
    ],

  

];