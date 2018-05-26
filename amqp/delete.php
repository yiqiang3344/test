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

$e_name = 'e_test'; //交换机名
$q_name = 'q_test'; //队列名

//创建连接和channel
$conn = new AMQPConnection($conn_args);
if (!$conn->connect()) {
    die("Cannot connect to the broker!\n");
}
$channel = new AMQPChannel($conn);

//创建交换机
$ex = new AMQPExchange($channel);
//echo "delete exchange [$e_name] status:" . $ex->delete($e_name), "\n";

$q = new AMQPQueue($channel);
$q->setName($q_name);
echo "delete queue [$q_name] status:" . $q->delete(), "\n";