<?php
$manager = new MongoDB\Driver\Manager("mongodb://finddemo:201671zhuang@localhost:27017/finddemo");
echo '<pre>';
var_dump($manager);
$filter = ['id' => 2];
$options = [
//    'projection' => ['_id' => 0],
];
$query = new MongoDB\Driver\Query($filter, $options);
$rows = $manager->executeQuery('finddemo.demo1', $query); // $mongo contains the connection object to MongoDB
foreach ($rows as $r) {
    print_r($r);
}
?>