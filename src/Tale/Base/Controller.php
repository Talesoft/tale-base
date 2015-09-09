<?php

namespace Tale;

use Tale\Base\Util\StringUtil;

class Controller
{

    const DEFAULT_CONROLLER = 'index';
    const ERROR_CONTROLLER = 'error';
    const DEFAULT_ACTION = 'index';
    const DEFAULT_FORMAT = 'html';

    private static $_current;

    private $_data;

    public function __construct()
    {

    }

    public static function getClassName($controller)
    {

        return implode('\\', array_map('Tale\\Base\\Util\\StringUtil::camelize', explode('.', $controller))).'Controller';
    }

    public static function getMethodName($action)
    {

        return StringUtil::variablize($action).'Action';
    }

    public static function dispatchError($action = null, $id = null, $format = null)
    {

        return self::dispatch(self::ERROR_CONTROLLER, $action, $id, $format);
    }

    public static function dispatch($controller = null, $action = null, $id = null, $format = null)
    {

        
    }
}