<?php
class KafkaDemo{
    /**
     * php RdKafka lowlevel 消费多个partions 的两种方式: topic vs queue
     * @reference https://arnaud-lb.github.io/php-rdkafka/phpdoc/rdkafka.examples-low-level-consumer-multi.html
     **/
    function example(){
        //via topic
        list($topic, $partitionNum) = $this->getConsumeTopic('topicName', 'groupID', 'brokersAddr');
        while(true){
            //手动轮询partions 
            for($i=0; $i<$partitionNum; $i++){
                $topic->consume($i, 100*1000);
            }
        }

        //via queue
        $queue = $this->getConsumeQueue('topicName', 'groupID', 'brokersAddr');
        while(true){
            $queue->consume(100*1000);
        }

    }

    function getConsumeQueue($topicName, $groupId, $brokersAddr){
        list($rk, $topic, $partitionNum) = getRK($topicName, $groupId, $brokersAddr);

        $queue = $rk->newQueue();
        for ($i = 0; $i < $partitionNum; $i++) {//如果分区号是连续的, 就这么干
            $topic->consumeQueueStart($i, RD_KAFKA_OFFSET_END, $queue);
        }
        return $queue;
    }

    function getConsumeTopic($topicName, $groupId, $brokersAddr){
        list($rk, $topic, $partitionNum) = getRK($topicName, $groupId, $brokersAddr);
        for ($i = 0; $i < $partitionNum; $i++) {//如果分区号是连续的, 就这么干
            $topic->consumeStart($i, RD_KAFKA_OFFSET_END);
        }
        return array($topic, $partitionNum);
    }


    function getRK($topicName, $groupId, $brokersAddr){
        $conf = new RdKafka\Conf();

        // Set the group id. This is required when storing offsets on the broker
        $conf->set('group.id',$groupID);
        $conf->set('broker.version.fallback', '0.8.2.2');
        $conf->set('socket.timeout.ms', 10000);
        //15M
        $conf->set('fetch.message.max.bytes', '15000000');

        $rk = new RdKafka\Consumer($conf);
        $rk->addBrokers($brokersAddr);
        $rk->setLogLevel(LOG_DEBUG);


        $topicConf = new RdKafka\TopicConf();
        $topicConf->set('auto.commit.interval.ms', 2000);
        $conf->set('socket.timeout.ms', 1120);

        $topicConf->set('offset.store.method', 'file');
        $topicConf->set('offset.store.path', sys_get_temp_dir());
        $topicConf->set('auto.offset.reset', 'largest');

        $topic = $rk->newTopic($topicName, $topicConf);
        $metaData = $rk->getMetadata(false, $topic, 1000);
        $partitions = $metaData->getTopics()->current()->getPartitions();
        $partitionNum = count($partitions);

        return array($rk, $topic, $partitionNum);

    }

}
