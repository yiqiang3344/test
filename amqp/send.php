<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/5/26
 * Time: 下午2:18
 */

$conn_args = array(
    'host' => '127.0.0.1',
    'port' => '5672',
    'login' => 'guest',
    'password' => 'guest',
    'vhost' => '/'
);

$e_name = 'e_test3';

$conn = new AMQPConnection($conn_args);
if (!$conn->connect()) {
    die('Cannot connect to the broker');
}

$channel = new AMQPChannel($conn);

$k_route = array(0 => 'key', 1 => 'key'); //路由key
//创建交换机
$ex = new AMQPExchange($channel);
$ex->setName($e_name);
$ex->setType(AMQP_EX_TYPE_DIRECT);
$ex->setFlags(AMQP_DURABLE);
$ex->declareExchange();

for ($i = 0; $i < 5; ++$i) {
    echo "Send Message:" . $ex->publish('hellow sidney' . $i, $k_route[$i % 2]) . "\n";
}
