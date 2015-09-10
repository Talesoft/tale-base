<?php

namespace Tale\App;

class Router
{

    private static $_routes = [];

    public static function addRoute($route, $handler)
    {

        if (!is_callable($handler))
            throw new \InvalidArgumentException(
                "Argument 2 passed to Router->setRoute needs to be"
                ."a valid PHP-style callback"
            );

        self::$_routes[$route] = $handler;
    }

    public static function addRoutes(array $routes)
    {

        foreach ($routes as $route => $handler)
            self::addRoute($route, $handler);
    }

    public static function route($string)
    {

        foreach (self::$_routes as $route => $handler) {

            if ($result = self::match($route, $string)) {

                if (($result = call_user_func($handler, $result)) !== false) {

                    return $result;
                }
            }
        }

        throw new \Exception("Failed to route: Route $string didn't match the request");
    }

    public static function getRouteRegEx($route)
    {

        return '/^'.str_replace('/', '\\/', preg_replace_callback('#(.)?:([a-z\_]\w*)(\?)?#i', function ($m) {

            $key = $m[2];
            $initiator = '';
            $optional = '';

            if (!empty($m[1])) {

                $initiator = '(?<'.$key.'Initiator>'.preg_quote($m[1]).')';
            }

            if (!empty($m[3]))
                $optional = '?';

            return '(?:'.$initiator.'(?<'.$key.'>[a-z0-9\_\-]*?))'.$optional;

        }, $route)).'$/i';
    }

    public static function match($route, $string)
    {

        $matches = [];
        $regEx = self::getRouteRegEx($route);
        $isMatch = preg_match($regEx, $string, $matches);

        if (!$isMatch)
            return false;

        $vars = [];
        if (!empty($matches))
            foreach ($matches as $name => $value)
                if (is_string($name) && !empty($value))
                    $vars[$name] = $value;

        return $vars;
    }
}