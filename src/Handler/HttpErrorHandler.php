<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/6/2019
 * Time: 11:19 PM
 */

namespace Peyman1992\TelegramFramework\Handler;

use Longman\TelegramBot\Entities\Update;

class HttpErrorHandler extends AbstractErrorHandler
{

    public function __construct($path)
    {
        parent::__construct($path);
    }

    public function render(\Throwable $throwable, Update $update = NULL)
    {
//        $whoops = new \Whoops\Run;
//        $whoops->pushHandler(new PrettyPageHandler());
//        $whoops->handleException($throwable);
    }
}