<?php

$manager = new MongoDB\Driver\Manager("mongodb://finddemo:201671zhuang@localhost:27017/finddemo");

$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert(['x' => 1, 'y' => 'foo']);
$bulk->insert(['x' => 2, 'y' => 'bar']);
$bulk->insert(['x' => 3, 'y' => 'bar']);
$manager->executeBulkWrite('finddemo.testaggregate', $bulk);

$command = new MongoDB\Driver\Command([
    'aggregate' => 'testaggregate',
    'pipeline' => [
        ['$group' => ['_id' => '$y', 'sum' => ['$sum' => '$x']]],
    ],
    'cursor' => new stdClass,
]);
$cursor = $manager->executeCommand('finddemo', $command);

/* The aggregate command can optionally return its results in a cursor instead
 * of a single result document. In this case, we can iterate on the cursor
 * directly to access those results. */
foreach ($cursor as $document) {
    var_dump($document);
}