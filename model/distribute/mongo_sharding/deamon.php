<?php
require_once 'sharding/Data_Sharding.class.php';
require_once 'sharding/Table_Hash.class.php';
require_once 'sharding/Storage_Route.class.php';

$servers = array (
		'node_a' => array (
				'dsn' => 'mongodb://172.28.20.181:27017',
				'positionIndex' => 0,
				'positionBase' => 3
		),
		'node_b' => array (
				'dsn' => 'mongodb://172.28.20.181:27017',
				'positionIndex' => 1,
				'positionBase' => 3
		),
		'node_c' => array (
				'dsn' => 'mongodb://172.28.20.181:27017',
				'positionIndex' => 2,
				'positionBase' => 3
		)
);

$storgeRouteInstance = new storgeRoute ( $servers );

$key = 'abc';
//分库
//dsn为连接串
$dsn = $storgeRouteInstance->getShardingDsn ( $key );
//分表 目前为doc分表,其他不分表,限制id为int型
$key = '95241258';
$collectionBaseName = "doc";
$collection = $storgeRouteInstance->getTableId ( $key, $collectionBaseName );

var_dump($dsn);
var_dump($collection);

