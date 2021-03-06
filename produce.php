<?php

$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', 'kafka:9092');


$topicConf = new RdKafka\TopicConf();
$topicConf->set("request.required.acks", 'all');

    function createData(int $id) {
    $now = new DateTimeImmutable('now');
    return [ 'ID' => $id, 'timestamp' =>  $now->format('Y-m-d H:i:s:u')];
}

$producer = new RdKafka\Producer($conf);
$topic = $producer->newTopic("BigPool", $topicConf);

for ($i = 0; $i < 10000; $i++) {
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode(createData($i)));
    echo 'Produced msg with ID: ' . $i . PHP_EOL;
    usleep(100000);
    $producer->poll(0);
}

for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
    $result = $producer->flush(10000);
    if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
        break;
    }
}

if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
    throw new \RuntimeException('Was unable to flush, messages might be lost!');
}

