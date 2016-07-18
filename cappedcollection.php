<?php

/**
 * mongodb 相关操作
 * @author 赵兴壮 <834916321@qq.com>
 */
class CreateCollection {

    protected $cmd = array();

    /**
     * 设置 集合名
     */
    function __construct($collectionName) {
        $this->cmd["create"] = (string) $collectionName;
    }

    /**
     * 如果为true，自动创建索引_id字段的默认值是false
     */
    function setAutoIndexId($bool) {
        $this->cmd["autoIndexId"] = (bool) $bool;
    }

    /**
     * 设置 固定集合
     *  @param int $maxBytes 最大字节数   大于该值 则更新数据
     * @param bool $maxDocument 最大文档数量    maxByte 必须指定大小，文档限制是容量没满时进行淘汰，要是满了，就根据容量限制进行淘汰
     */
    function setCappedCollection($maxBytes, $maxDocuments = false) {
        $this->cmd["capped"] = true;
        $this->cmd["size"] = (int) $maxBytes;
        if ($maxDocuments) {
            $this->cmd["max"] = (int) $maxDocuments;
        }
    }

    /**
     * 现在比较郁闷
     */
    function usePowerOf2Sizes($bool) {
        if ($bool) {
            $this->cmd["flags"] = 1;
        } else {
            $this->cmd["flags"] = 0;
        }
    }

    function setFlags($flags) {
        $this->cmd["flags"] = (int) $flags;
    }

    /**
     * 获取命令 
     */
    function getCommand() {
        return new MongoDB\Driver\Command($this->cmd);
    }

    /**
     * 获取集合名
     */
    function getCollectionName() {
        return $this->cmd["create"];
    }

}

$manager = new MongoDB\Driver\Manager("mongodb://root:201671zhuang@localhost:27017/admin");

$createCollection = new CreateCollection("cappedCollection");
//设置固定集合大小
$createCollection->setCappedCollection(64 * 1024);

try {
    $command = $createCollection->getCommand();
    $cursor = $manager->executeCommand("cappedCollection", $command);
    var_dump($cursor);
    $response = $cursor->toArray()[0];
    var_dump($response);
    exit;
    $collstats = ["collstats" => $createCollection->getCollectionName()];
    $cursor = $manager->executeCommand("databaseName", new MongoDB\Driver\Command($collstats));
    $response = $cursor->toArray()[0];
    var_dump($response);
} catch (MongoDB\Driver\Exception $e) {
    echo $e->getMessage(), "\n";
    exit;
}
?>