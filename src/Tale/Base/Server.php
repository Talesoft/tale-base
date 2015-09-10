<?php

namespace Tale\Base;

use Tale\Util\StringUtil;

class Server
{

    public static function get($name, $default = null)
    {

        if (!isset($_SERVER[$name]))
            return $default;

        return $_SERVER[$name];
    }


    public static function getScheme()
    {

        return self::get('REQUEST_SCHEME',
            self::get('HTTPS', '') == 'on' ? 'https' : 'http'
        );
    }

    public static function getHost()
    {

        return self::get('HTTP_HOST',
            self::get('SERVER_NAME', 'localhost')
        );
    }

    public static function getPort()
    {

        return self::get('SERVER_PORT');
    }

    public static function getPath()
    {

        $path = self::get('PATH_INFO');
        if (empty($path)) {

            $path = self::get(
                'REDIRECT_REQUEST_URI',
                self::get('REQUEST_URI', '/')
            );
        }

        return $path;
    }

    public static function getProtocol()
    {

        $proto = self::get('SERVER_PROTOCOL');
        return explode('/', $proto)[0];
    }

    public static function getProtocolVersion()
    {

        $proto = self::get('SERVER_PROTOCOL');
        return explode('/', $proto)[1];
    }

    public static function getMethod()
    {

        return self::get('REQUEST_METHOD');
    }

    public static function getHeaders()
    {

        foreach ($_SERVER as $name => $value) {

            if (strncmp($name, 'HTTP_', 5) === 0) {

                $name = StringUtil::dasherize(StringUtil::humanize(strtolower(substr($name, 5))));
                yield $name => $value;
            }
        }
    }

    public static function getHeaderArray()
    {

        return iterator_to_array(self::getHeaders());
    }

    public static function getQueryString()
    {

        return self::get('redirectQueryString', self::get('queryString', ''));
    }
}