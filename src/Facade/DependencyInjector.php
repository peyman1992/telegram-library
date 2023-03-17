<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramLibrary\Facade;

use Illuminate\Support\Facades\Facade;
use Peyman1992\TelegramLibrary\Injector\DependencyInjector as DependencyInjectorAlias;

class DependencyInjector extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return DependencyInjectorAlias::class;
    }

}