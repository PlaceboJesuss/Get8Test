<?php

namespace Helpers;

use Helpers\SubSystems\Logger;
use Psr\Log\LoggerInterface;

/**
 * @method static Log setPath(string $path)
 * @method static Log debug(string $message,array $context) 
 * @method static Log error(string $message,array $context) 
 */

class Log
{
    private static Logger $object;

    public static function __callStatic($name, $arguments)
    {
        if (!isset(static::$object)) {
            static::$object = new Logger();
        }
        static::$object->$name(...$arguments);
    }

    public static function getLogger(): Logger
    {
        if (!isset(static::$object)) {
            static::$object = new Logger();
        }
        return static::$object;
    }
}
