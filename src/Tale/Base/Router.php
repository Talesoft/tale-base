<?php

namespace Tale\Base;

class Router
{

    public static function route($string)
    {

        $routes = Config::get('routes', []);
        foreach ($routes as $route => $handler) {

            var_dump("ROUTE $string => $route");

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