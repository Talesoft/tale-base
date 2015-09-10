<?php

namespace Tale\Base;

class Session
{

    public static function isStarted()
    {

        $id = session_id();
        return !empty($id);
    }

    public static function start()
    {

        if (self::isStarted())
            return;

        session_name(Config::get('session.name', 'tale.base.session'));
        session_cache_expire(Config::get('session.lifeTime', 24 * 60 * 60));
        session_start();
    }

    public static function get($key, $default = null)
    {

        self::start();
        return $_SESSION[$key];
    }

    public static function set($key, $value)
    {

        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has($key)
    {

        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {

        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy()
    {

        self::start();
        foreach ($_SESSION as $key => $val)
            unset($_SESSION[$key]);

        session_destroy();
    }
}