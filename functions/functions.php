<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/13/2019
 * Time: 11:02 PM
 */

use Peyman1992\TelegramLibrary\Facade\TelegramBot;
use Peyman1992\TelegramLibrary\Session\TelegramSessionManager;
use Telegram\Bot\Exceptions\TelegramSDKException;

if (!function_exists("sessionTelegram")) {
    function telegramSession(string $id): TelegramSessionManager
    {
        return app('telegram-session', ["id" => $id]);
    }
}
if (!function_exists("renderView")) {
    function renderView($view = NULL, $data = [], $mergeData = []): string
    {
        return view($view, $data, $mergeData)->render();
    }
}

if (!function_exists("safeDeleteTelegramMessage")) {
    function safeDeleteTelegramMessage(array $params): bool
    {
        try {
            return TelegramBot::deleteMessage($params);
        } catch (TelegramSDKException $e) {
            return FALSE;
        }
    }
}
if (!function_exists("safeAnswerCallbackQuery")) {
    function safeAnswerCallbackQuery(array $params): bool
    {
        try {
            return TelegramBot::answerCallbackQuery($params);
        } catch (TelegramSDKException $e) {
            return FALSE;
        }
    }
}
if (!function_exists("safeCallTelegram")) {
    function safeCallTelegram(callable $fn): void
    {
        try {
            $fn();
        } catch (TelegramSDKException) {
        }
    }
}