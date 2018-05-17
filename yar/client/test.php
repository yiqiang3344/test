<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/5/17
 * Time: 下午9:00
 */
include '../../autoload.php';

$Test = \yar\client\RpcClient::init("Test");
var_dump($Test->hello('sidney'));