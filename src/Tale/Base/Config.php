<?php

namespace Tale\Base;

class Config
{

    private static $_path = './config';

    public static function getPath($filePath = null)
    {

        return self::$_path.( $filePath ? "/$filePath": '');
    }

    public function setPath($path)
    {

        self::$_path = $path;
    }

    public static function load($path)
    {

        $path = self::getPath($path).'.php';

        if (!file_exists($path))
            throw new \Exception("Failed to load config: $path does not exist");

        return include($path);
    }
}