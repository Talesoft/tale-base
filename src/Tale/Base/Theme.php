<?php

namespace Tale\Base;

use Tale\Theme\Manager;

class Theme
{

    private static $_manager;

    public static function getManager()
    {

        if (!isset(self::$_manager))
            self::$_manager = new Manager([
                'path' => App::getPath().'/themes',
                'baseUrl' => App::getUrl().'/themes'
            ]);

        return self::$_manager;
    }

    public static function __callStatic($method, array $args = null)
    {

        return call_user_func_array([self::getManager(), $method], $args);
    }
}