<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2018/8/24
 * Time: 下午2:55
 */

namespace p2p\model;


trait Error
{
    protected $_code;
    protected $_error;

    public function buildError($code = null, $message = '')
    {
        if ($code instanceof Error) {
            $this->holdError($code);
        } else {
            $this->_code = $code;
            $this->_error = $message;
        }
        return false;
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function getError()
    {
        return (ERROR_MESSAGE[$this->_code] ? ERROR_MESSAGE[$this->_code] . ':' : '') . $this->_error;
    }

    public function holdError(Error $m)
    {
        $this->_code = $m->getCode();
        $this->_error = $m->getError();
        return $this;
    }
}