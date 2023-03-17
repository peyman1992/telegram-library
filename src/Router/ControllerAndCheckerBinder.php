<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/15/2019
 * Time: 7:36 PM
 */

namespace Peyman1992\TelegramLibrary\Router;

class ControllerAndCheckerBinder
{
    //todo convert static class to object
    private static $binds = [];

    public static function bind($key, $value)
    {
        static::$binds[$key] = $value;
    }

    public static function hasBind($key)
    {
        return isset(static::$binds[$key]);
    }

    public static function make($key)
    {
        return static::$binds[$key];
    }

    public static function remove($key)
    {
        if (static::hasBind($key))
            unset(static::$binds[$key]);
    }

    public static function destroy()
    {
        static::$binds = [];
    }
}