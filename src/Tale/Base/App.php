<?php

namespace Tale\Base;

class App
{

    public static function getRequestPath()
    {

        $path = Server::getPath();
        $url = Config::get('app.url');

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

    public static function run()
    {

        $configFiles = ['app', 'php-options', 'routes'];

        foreach ($configFiles as $configFile)
            Config::tryLoad($configFile);

        Router::route(self::getRequestPath());
    }
}