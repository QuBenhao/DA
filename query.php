<?php
 
//链接mongodb
$manager = new MongoDB\Driver\Manager('mongodb://root:sjhc168@10.10.10.104:27017');
 
//查询
$filter =  ['user_id'=>['$gt'=>0]]; //查询条件 user_id大于0
$options = [
   'projection' => ['_id' => 0], //不输出_id字段
   'sort' => ['user_id'=>-1] //根据user_id字段排序 1是升序，-1是降序
];
$query = new MongoDB\Driver\Query($filter, $options); //查询请求
$list = $manager->executeQuery('location.box',$query); // 执行查询 location数据库下的box集合
 
 
foreach ($list as $document) {
    print_r($document); 
}

