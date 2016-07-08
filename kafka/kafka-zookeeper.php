<?php
/**
 * https://github.com/andreiz/php-zookeeper/blob/master/examples/Zookeeper_Example.php
 */
$zk = new Zookeeper('127.0.0.1:port,...,ip:port/path');
$brokersPath = '/brokers/ids';

$ids = $zk->getChildren($brokersPath);
$brokers = array();
foreach ($ids as $id) {
    $path = $brokersPath . "/{$id}";
    $val = $zk->get($path);
    $val = json_decode($val, true);
    $brokers[] = $val['host'] . ":" . $val['port'];
}
$brokersAddr = implode(',', $brokers);

//begin to consume
$conf = new RdKafka\Conf();

// Set the group id. This is required when storing offsets on the broker
$conf->set('group.id', 'my-alarm-group');
$conf->set('broker.version.fallback', '0.8.2.2');

$rk = new RdKafka\Consumer($conf);
$rk->addBrokers($brokersAddr);
$rk->setLogLevel(LOG_DEBUG);


$topicConf = new RdKafka\TopicConf();
$topicConf->set('auto.commit.interval.ms', 1000);

// Set the offset store method to 'file'
$topicConf->set('offset.store.method', 'file');
$topicConf->set('offset.store.path', sys_get_temp_dir());
//$topicConf->set('api.version.request', true);
//$topicConf->set('broker.version.fallback', '0.8.2.2');

// Alternatively, set the offset store method to 'broker'
// $topicConf->set('offset.store.method', 'broker');

// Set where to start consuming messages when there is no initial offset in
// offset store or the desired offset is out of range.
// 'smallest': start from the beginning
$topicConf->set('auto.offset.reset', 'largest');

$topic = $rk->newTopic("Topic_Name", $topicConf);

// Start consuming partition 0
$topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);

while (true) {
    $message = $topic->consume(0, 120*10000);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            var_dump($message);
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo "No more messages; will wait for more\n";
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            throw new \Exception($message->errstr(), $message->err);
            break;
    }
}
