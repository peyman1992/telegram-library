<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramLibrary\Facade;

use Closure;
use Illuminate\Support\Facades\Facade;
use Peyman1992\TelegramLibrary\Router\Router;

/**
 * @method static register(Closure|array $checker, Closure|array $controller)
 * @method static registerNotFound(Closure|array $controller)
 */
class TelegramRoute extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Router::class;
    }

}