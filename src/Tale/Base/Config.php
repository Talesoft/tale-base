<?php

namespace Tale\Base;

use Tale\Util\StringUtil;

class Config
{

    private static $_path = './config';
    private static $_options = [];
    private static $_resolvedOptions = [];

    public static function getPath($filePath = null)
    {

        return self::$_path.( $filePath ? "/$filePath": '');
    }

    public function setPath($path)
    {

        self::$_path = $path;
    }

    public static function load($name)
    {

        $path = self::getPath("$name.php");
        $suffixedPaths = glob(self::getPath("$name.*.php"));

        if (!file_exists($path))
            throw new \Exception("Failed to load config $name: $path does not exist");

        $options = include($path);

        foreach ($suffixedPaths as $path)
            $options = array_replace_recursive($options, include($path));

        self::$_options[$name] = $options;
    }

    public static function tryLoad($name)
    {

        try {

            self::load($name);
        } catch(\Exception $e) {

            self::$_options[$name] = [];
        }
    }

    public static function get($key = null, $default = null)
    {

        if (!$key)
            return self::$_options;

        if (strpos($key, '.') !== false) {

            if (!isset(self::$_resolvedOptions[$key]))
                self::$_resolvedOptions[$key] = StringUtil::resolve($key, self::$_options, $default);

            return self::$_resolvedOptions[$key];
        }

        return isset(self::$_options[$key]) ? self::$_options[$key] : $default;
    }
}