<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/8/24
 * Time: 下午2:55
 */

namespace p2p;


Class Base
{
    use Error;

    protected $attributes = [];

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return $this->attributes[$name];
    }
}