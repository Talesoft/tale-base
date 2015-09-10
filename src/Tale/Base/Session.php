<?php

namespace Tale\Base;

class Session
{

    private $_options;

    public static function getOptions()
    {

        return array_replace([
            'name' => 'Tale.Base.Session',
            'lifeTime' => 3600
        ], Config::load('session'));
    }
    

    public static function isStarted()
    {

        $id = session_id();
        return !empty($id);
    }

    public static function start()
    {


    }
}