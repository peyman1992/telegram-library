<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/7/2019
 * Time: 12:03 AM
 */

namespace Peyman1992\TelegramFramework\Interfaces;

use Longman\TelegramBot\Entities\Update;

interface ErrorHandler
{
    public function report(\Throwable $throwable, Update $update);

    public function render(\Throwable $throwable, Update $update);
}