<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramLibrary\Facade;

use Illuminate\Support\Facades\Facade;
use Telegram\Bot\Objects\ChatMember;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\WebhookInfo;

/**
 * @method static Message editMessageText(array $properties)
 * @method static Message sendMessage(array $properties)
 * @method static Message sendPhoto(array $properties)
 * @method static Message sendVideo(array $properties)
 * @method static Message sendDocument(array $properties)
 * @method static ChatMember getChatMember(array $properties)
 * @method static boolean answerCallbackQuery(array $properties)
 * @method static boolean deleteMessage(array $properties)
 * @method static boolean setWebhook(array $properties)
 * @method static WebhookInfo getWebhookInfo(array $properties)
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