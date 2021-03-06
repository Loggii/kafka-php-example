<?php

$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', 'localhost:9093');
//produce exactly once and keep the original produce order
$conf->set('enable.idempotence', 'true');

$topicConf = new RdKafka\TopicConf();
$topicConf->set("request.required.acks", 'all');

function createData(int $id) {
    $now = new DateTimeImmutable('now');
    return [ 'ID' => $id, 'timestamp' =>  $now->format('Y-m-d H:i:s:u')];
}

$producer = new RdKafka\Producer($conf);
$topic = $producer->newTopic("BigPool", $topicConf);

for ($i = 0; $i < 100000; $i++) {
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode(createData($i)));
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

