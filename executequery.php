<?php

//$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$manager = new MongoDB\Driver\Manager("mongodb://root:201671zhuang@localhost:27017/admin");
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert(['x' => 1]);
$bulk->insert(['x' => 2]);
$bulk->insert(['x' => 3]);
$manager->executeBulkWrite('db.collection', $bulk);

$filter = ['x' => ['$gt' => 1]];
$options = [
    //映射
    'projection' => ['_id' => 0],
    //根据x 降序
    'sort' => ['x' => -1],
];

$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('db.collection', $query);
echo '<pre>';
foreach ($cursor as $document) {
    var_dump($document);
}
?>