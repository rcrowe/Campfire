<?php

namespace rcrowe\Campfire;

use \rcrowe\Campfire as Campfire;

class Facade
{
    /**
     * @var rcrowe\Campfire\Facade
     */
    protected static $instance;

    public static function init(array $config = array(), $http = NULL)
    {
        return static::instance(new Campfire($config, $http));
    }

    public static function instance(Campfire $instance = NULL)
    {
        if ($instance !== NULL)
        {
            static::$instance = $instance;
        }

        return static::$instance;
    }

    public static function destroy()
    {
        static::$instance = NULL;
    }

    public static function __callStatic($name, $arguments)
    {
        // Do we have a valid instance initialised
        if (!isset(static::$instance))
        {
            throw new Campfire\Exceptions\FacadeException('Facade::init(...) must be called first');
        }

        switch ($name)
        {
            case 'msg':
                    $method = 'send';
                    $arg1   = (isset($arguments[0])) ? $arguments[0] : NULL;

                    if ($arg1 === NULL)
                    {
                        // throw exception
                        throw new \InvalidArgumentException('No message was passed in as the first argument');
                    }
                    break;

            default:
                    $method = $name;
                    $arg1   = (isset($arguments[0])) ? $arguments[0] : NULL;
        }

        static::$instance->$method($arg1);
    }
}