<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/5/17
 * Time: 下午9:00
 */

include '../../autoload.php';

function callback($retval, $callinfo)
{
    if ($callinfo != null) {
        var_dump($retval, $callinfo);
        echo "<br/>";
        return;
    }
    var_dump('正常的逻辑');
    echo "<br/>";
}

function callback1($retval, $callinfo)
{
    if ($callinfo != null) {
        echo "callback1:<br/>";
        var_dump($retval, $callinfo);
        echo "<br/>";
        return;
    }
    var_dump('正常的逻辑');
    echo "<br/>";
}

function error_callback($type, $error, $callinfo)
{
    error_log($error);
    echo "<br/>";
}

$server_url = \yar\client\RpcClient::$rpcConfig['Test'];

echo date('Y-m-d H:i:s'), ' ' . microtime(false), ' 1<br/>';
\Yar_Concurrent_Client::call($server_url, "hello", ["1"], "callback1");

echo date('Y-m-d H:i:s'), ' ' . microtime(false), ' 2<br/>';
// if the callback is not specificed,callback in loop will be used
\Yar_Concurrent_Client::call($server_url, "hello", ["2"]);

echo date('Y-m-d H:i:s'), ' ' . microtime(false), ' 3<br/>';
//this server accept json packager
\Yar_Concurrent_Client::call($server_url, "hello", ["3"], "callback", NULL, [YAR_OPT_PACKAGER => "json"]);

echo date('Y-m-d H:i:s'), ' ' . microtime(false), ' 4<br/>';
\Yar_Concurrent_Client::call($server_url, "hello", ["11"], "callback", NULL, [YAR_OPT_TIMEOUT => 1]);

echo date('Y-m-d H:i:s'), ' ' . microtime(false), ' 5<br/>';
\Yar_Concurrent_Client::loop("callback", "error_callback");

echo date('Y-m-d H:i:s'), ' ' . microtime(false), ' 6<br/>';
