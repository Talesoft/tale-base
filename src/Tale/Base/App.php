<?php

namespace Tale\Base;

use Tale\ClassLoader;
use Tale\Util\StringUtil;

class App
{

    private static $_path;
    private static $_loaders = [];


    public static function getPath()
    {
        return self::$_path;
    }

    public static function getUrl()
    {

        return Config::get('app.url');
    }

    public static function getNameSpace()
    {

        return Config::get('app.nameSpace');
    }

    public static function getControllerNameSpace()
    {

        return self::getNameSpace().'\\Controller';
    }

    public static function getModelNameSpace()
    {

        return self::getNameSpace().'\\Model';
    }

    public static function getRequestPath()
    {

        $path = Server::getRequestPath();
        $url = self::getUrl();

        if ($url) {

            $basePath = rtrim(parse_url($url, \PHP_URL_PATH), '/');

            if ($basePath !== '/') {

                $len = strlen($basePath);

                if (strncmp($path, $basePath, $len) !== 0) {

                    throw new \Exception(
                        "Failed to run app: The app is bound to $basePath"
                    );
                }

                $path = substr($path, $len + 1);
            }
        }

        return '/'.ltrim($path, '/');
    }

    private static function _registerLoaders()
    {

        $appPath = self::getPath();
        $loaders = [
            "$appPath/controllers" => self::getControllerNameSpace(),
            "$appPath/models" => self::getModelNameSpace(),
            "$appPath/library" => self::getNameSpace(),
            "$appPath/vendor" => null
        ];

        foreach ($loaders as $path => $nameSpace) {

            $loader = new ClassLoader($path, $nameSpace);
            self::$_loaders[$path] = $loader;
            $loader->register();
        }
    }

    private static function _setPhpOptions()
    {

        $options = Config::get('php-options', []);
        foreach ($options as $name => $value) {

            ini_set(StringUtil::dasherize($name, '.'), $value);
        }
    }

    public static function run($path)
    {

        self::$_path = $path;

        $configFiles = ['php-options', 'app', 'routes'];

        foreach ($configFiles as $configFile)
            Config::tryLoad($configFile);

        if (!Config::get('app.url') || !Config::get('app.nameSpace'))
            throw new \Exception(
                "Failed to run app: Please configure an url and a nameSpace in your app.php config file"
            );

        self::_registerLoaders();
        self::_setPhpOptions();

        var_dump(self::$_loaders);

        Router::route(self::getRequestPath());
    }
}