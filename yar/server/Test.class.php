<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/5/17
 * Time: 下午8:57
 */

 class Test{
     /**
      * Test constructor.
      * @param $name
      */
    public function hello($name){
        return 'hello '.$name;
    }
 }

 $yarServer = new Yar_server(new Test());
 $yarServer->handle();