<?php

namespace Tale\Base;

use Tale\Base\Util\StringUtil;

class Server
{

    public static function getOption($name, $default = null)
    {

        if (!isset($_SERVER[$name]))
            return $default;

        return $_SERVER[$name];
    }

    public static function getOptions()
    {

        return $_SERVER;
    }

    public static function getScheme()
    {

        return self::getOption('REQUEST_SCHEME',
            self::getOption('HTTPS', '') == 'on' ? 'https' : 'http'
        );
    }

    public static function getHost()
    {

        return self::getOption('HTTP_HOST',
            self::getOption('SERVER_NAME', 'localhost')
        );
    }

    public static function getPort()
    {

        return self::getOption('SERVER_PORT');
    }

    public static function getPath()
    {

        $path = self::getOption('PATH_INFO');
        if (empty($path)) {

            $path = self::getOption(
                'REDIRECT_REQUEST_URI',
                self::getOption('REQUEST_URI', '/')
            );
        }

        return $path;
    }

    public static function getProtocol()
    {

        $proto = self::getOption('SERVER_PROTOCOL');
        return explode('/', $proto)[0];
    }

    public static function getProtocolVersion()
    {

        $proto = self::getOption('SERVER_PROTOCOL');
        return explode('/', $proto)[1];
    }

    public static function getMethod()
    {

        return self::getOption('REQUEST_METHOD');
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

        return self::getOption('redirectQueryString', self::getOption('queryString', ''));
    }
}