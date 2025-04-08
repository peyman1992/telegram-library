<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramLibrary\Facade;

use Illuminate\Support\Facades\Facade;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\ChatMember;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\WebhookInfo;

/**
 * @method static Message editMessageText(array $properties)
 * @throws TelegramSDKException
 * @method static Message sendMessage(array $properties)
 * @throws TelegramSDKException
 * @method static Message sendPhoto(array $properties)
 * @throws TelegramSDKException
 * @method static Message sendVideo(array $properties)
 * @throws TelegramSDKException
 * @method static Message sendDocument(array $properties)
 * @throws TelegramSDKException
 * @method static Message sendAudio(array $properties)
 * @throws TelegramSDKException
 * @method static ChatMember getChatMember(array $properties)
 * @throws TelegramSDKException
 * @method static boolean answerCallbackQuery(array $properties)
 * @throws TelegramSDKException
 * @method static boolean deleteMessage(array $properties)
 * @throws TelegramSDKException
 * @method static boolean setWebhook(array $properties)
 * @throws TelegramSDKException
 * @method static WebhookInfo getWebhookInfo(array $properties)
 * @throws TelegramSDKException
 */
class TelegramBot extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bot';
    }

}