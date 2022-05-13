<?php
namespace App\Http\Controllers;
use App\Http\Controllers\dbLoopService;


class dbsave
{
    protected static function resolveFacade($name)
    {
        return app()[$name];
    }

    public static function __callStatic($method, $arguments)
    {
        return (
            self::resolveFacade('dbsave')
        )->$method(...$arguments);
    }
}