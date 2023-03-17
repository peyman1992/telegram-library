<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/6/2019
 * Time: 11:19 PM
 */

namespace Peyman1992\TelegramFramework\Handler;

use Longman\TelegramBot\Entities\Update;
use Peyman1992\TelegramFramework\Interfaces\ErrorHandler;

abstract class AbstractErrorHandler implements ErrorHandler
{
    protected string $path;
    private string $htmlName = "error.html";
    private string $textName = "error.log";
    private string $updateName = "update.log";
    protected int $time;

    public function __construct($path)
    {
        $this->time = time();
        $this->path = $path;
    }

    abstract public function render(\Throwable $throwable, Update $update);

    public function report(\Throwable $throwable, Update $update)
    {
//        $whoops = new \Whoops\Run;
//        $whoops->writeToOutput(FALSE);
//        $whoops->allowQuit(FALSE);
//
//        $dateTime = date('Y-m-d H-i-s ', $this->time);
//
//        $folder = $this->path . $dateTime . "update-" . $update->getUpdateId() . DS;
//
//        $pathToHtml = $folder . $this->htmlName;
//        $pathToText = $folder . $this->textName;
//        $pathToUpdate = $folder . $this->updateName;
//
//        if (!is_dir($folder)) {
//            mkdir($folder, 0777, TRUE);
//        }
//        $format = new PrettyPageHandler();
//        $whoops->pushHandler($format);
//        $response = $whoops->handleException($throwable);
//        file_put_contents($pathToHtml, $response);
//
//        $format = new PlainTextHandler();
//        $whoops->popHandler();
//        $whoops->pushHandler($format);
//        $response = $whoops->handleException($throwable);
//        file_put_contents($pathToText, $response);
//        if ($update)
//            file_put_contents($pathToUpdate, json_encode($update->toJson(), JSON_PRETTY_PRINT));
    }
}