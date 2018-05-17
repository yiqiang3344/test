<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/5/17
 * Time: 下午9:08
 */

namespace yar\client;


class RpcClient
{
    // RPC 服务地址映射表
    public static $rpcConfig = [
        "Test" => "http://test.sidneyyi.com:9010/yar/server/Test.class.php",
    ];

    public static function init($server)
    {
        if (array_key_exists($server, self::$rpcConfig)) {
            $uri = self::$rpcConfig[$server];
            return new \Yar_Client($uri);
        }
    }
}