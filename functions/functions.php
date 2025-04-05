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

if (!function_exists("safeDeleteMessage")) {
    function safeDeleteMessage(array $params): bool
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
if (!function_exists("safeSendMessage")) {
    function safeSendMessage(array $params): ?\Telegram\Bot\Objects\Message
    {
        try {
            return TelegramBot::sendMessage($params);
        } catch (TelegramSDKException $e) {
            return null;
        }
    }
}

if (!function_exists("safeSendPhoto")) {
    function safeSendPhoto(array $params): ?\Telegram\Bot\Objects\Message
    {
        try {
            return TelegramBot::sendPhoto($params);
        } catch (TelegramSDKException $e) {
            return null;
        }
    }
}
if (!function_exists("safeSendVideo")) {
    function safeSendVideo(array $params): ?\Telegram\Bot\Objects\Message
    {
        try {
            return TelegramBot::sendVideo($params);
        } catch (TelegramSDKException $e) {
            return null;
        }
    }
}
if (!function_exists("safeSendDocument")) {
    function safeSendDocument(array $params): ?\Telegram\Bot\Objects\Message
    {
        try {
            return TelegramBot::sendDocument($params);
        } catch (TelegramSDKException $e) {
            return null;
        }
    }
}

if (!function_exists("safeEditMessageText")) {
    function safeEditMessageText(array $params): ?\Telegram\Bot\Objects\Message
    {
        try {
            return TelegramBot::editMessageText($params);
        } catch (TelegramSDKException $e) {
            return null;
        }
    }
}
