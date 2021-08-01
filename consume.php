<?php

use RdKafka\Message;

$conf = new RdKafka\Conf();

$conf->set('group.id', 'SwitchOrderDispatcher');
$conf->set('metadata.broker.list', 'kafka:9092');
$conf->set('auto.offset.reset', 'latest');
$conf->set('enable.auto.commit', 'false');


$consumer = new RdKafka\KafkaConsumer($conf);
$consumer->subscribe(['BigPool']);


while (true) {
    $message = $consumer->consume(120*1000);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            printConsumedData($message);
            $consumer->commit();
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            throw new Exception($message->errstr(), $message->err);
    }
}


function printConsumedData(Message $message) {
    $payload = $message->payload;
    $payloadArray = json_decode($payload, true);
    $id = $payloadArray['ID'];
    $idString = Color::ColorizeString($id, 'light_green');
    $timeStamp = $payloadArray['timestamp'];
    $timeStampString = Color::ColorizeString($timeStamp, 'yellow');

    $idKey = Color::ColorizeString('ID: ', 'cyan');
    $timeStampKey = Color::ColorizeString('TimeStamp: ', 'blue');
    print_r($idKey . $idString . ', ' . $timeStampKey . $timeStampString . PHP_EOL );
}

