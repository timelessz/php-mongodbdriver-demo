<?php
/**
 * 将多个数据更新操作（插入 修改 删除）操作放到一个待执行的列表中来批量执行
 * 批量操作   分成两种操作  一种是  顺序批量操作   一种是 并行执行
 * 　包括：　　    db.collection.initializeOrderedBulkOp()      顺序执行     如果其中某项操作出现问题 mongodb 不会做任何修改
 * db.collection.initializeUnorderedBulkOp()   并行执行
 * 例如：    如果一个执行list 包含添加 更新  删除操作 ，mongodb 会把操作分成三个组：第一个执行insert 操作 第二个执行更新操作  第三个执行删除操作
 * 　　　　Bulk.getOperations()可以查看一组各类型的操作是如何分组的，执行之后才能调用。
 */

$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
$bulk->delete([]);
$bulk->insert(['_id' => 1]);
$bulk->insert(['_id' => 2]);
$bulk->insert(['_id' => 3, 'hello' => 'world']);
$bulk->update(['_id' => 3], ['$set' => ['hello' => 'earth']]);
$bulk->insert(['_id' => 4, 'hello' => 'pluto']);
$bulk->update(['_id' => 4], ['$set' => ['hello' => 'moon']]);
$bulk->insert(['_id' => 3]);
$bulk->insert(['_id' => 4]);
$bulk->insert(['_id' => 5]);

echo '<pre>';

//$manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
$manager = new MongoDB\Driver\Manager("mongodb://finddemo:201671zhuang@localhost:27017/finddemo");

/**
 * write concern 写入安全级别 设置
 *                  1.unacknowledged  非确认方式写入 w:0  仅仅可以获取到网络错误  还有一个 w:-1  任何错误都不会获取到
 *                  2.acknowledged 确认式写入 w:1 该级别会在 数据写入到内存镜像之后返回 结果状态，但是 此时如果服务器崩溃 数据一样会丢
 *                  3.journaled journal w:1&j:true 日志写入 日志操作用于故障回复跟持久化，64位机器上 Mongodb 2.0 以上版本 默认情况下式开启journal的
 *                              journal 文件位于 journal 目录中，只能以追加的方式添加数据，文件名称以j._开头。mongodb 会每个100ms （或者30ms）向journal文件中flush 一次数据
 *                  4.replia acknowledged 复制集确认写入 w:majority&j:true 表示>1/2的节点有数据 就会返回数据
 *
 * mongodb 写入安全机制介绍   我的博客
 * http://www.cnblogs.com/timelesszhuang/p/5156276.html
 */
//设置写入安全级别
$writeConcern = new MongoDB\Driver\WriteConcern(1, 1000, TRUE);
try {
    $result = $manager->executeBulkWrite('finddemo.testbulk', $bulk, $writeConcern);
} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
    $result = $e->getWriteResult();
    // Check if the write concern could not be fulfilled
    if ($writeConcernError = $result->getWriteConcernError()) {
        printf("%s (%d): %s\n", $writeConcernError->getMessage(), $writeConcernError->getCode(), var_export($writeConcernError->getInfo(), true)
        );
    }
    // Check if any write operations did not complete at all
    foreach ($result->getWriteErrors() as $writeError) {
        printf("Operation#%d: %s (%d)\n", $writeError->getIndex(), $writeError->getMessage(), $writeError->getCode()
        );
    }
} catch (MongoDB\Driver\Exception\Exception $e) {
    printf("Other error: %s\n", $e->getMessage());
    exit;
}


printf("Inserted %d document(s)\n", $result->getInsertedCount());
printf("Updated  %d document(s)\n", $result->getModifiedCount());
//print_r("Updated  %s document(s)\n", $result->getInfo());
?>