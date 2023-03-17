<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/6/2019
 * Time: 11:19 PM
 */

namespace Peyman1992\TelegramLibrary\Handler;

use Longman\TelegramBot\Entities\Update;

class SentToTelegramErrorHandler extends AbstractErrorHandler
{
    private $telegramBot;
    private $id;

    public function __construct($path, $telegramBot, $id)
    {
        parent::__construct($path);
        $this->id = $id;
        $this->telegramBot = $telegramBot;
    }

    public function render(\Throwable $throwable, Update $update = NULL)
    {
//        $whoops = new \Whoops\Run;
//        $format = new PlainTextHandler();
//        $format->addTraceToOutput(FALSE);
//        $whoops->pushHandler($format);
//        $whoops->writeToOutput(FALSE);
//        $whoops->allowQuit(FALSE);
//        $response = "Update id: " . $update->getUpdateId() . "\n";
//        $response = $response . $whoops->handleException($throwable);
//        $this->telegramBot->sendMessage($this->id, $response);
    }

}