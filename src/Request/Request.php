<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramLibrary\Request;

use Exception;
use Illuminate\Contracts\Container\Container;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use function array_filter;
use function config;
use function count;
use function is_null;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function time;
use const JSON_ERROR_NONE;

class Request
{
    private string $identifier;
    private Api $bot;
    private \Illuminate\Cache\CacheManager $cache;
    private array $updates;

    public function __construct(Container $Container)
    {
        $this->bot = $Container->make('bot');
        $this->cache = $Container->make("cache");
        $this->identifier=md5($this->bot->getAccessToken());
    }

    public function getUpdates($fromWebhook = NULL): void
    {
        if (is_null($fromWebhook)) {
            if (config('telegram-library.get_update_from_web_hook')) {
                $this->getWebHookUpdates();
            } else {
                $this->getLongPollingUpdates();
            }
        } else {
            if ($fromWebhook) {
                $this->getWebHookUpdates();
            } else {
                $this->getLongPollingUpdates();
            }
        }
    }

    private function getWebHookUpdates(): void
    {
        $json = $this->getRawBody();
        $this->closeConnection();
        $data = "";
        try {
            $data = $this->toJson($json, TRUE);
        } catch (Exception $e) {
        }
        if (is_array($data)) {
            $this->updates = [new Update($data)];
        } else {
            $this->updates = [];
        }
    }

    private function toJson(string $jsonString, bool $asArray): object|array
    {
        $json = json_decode($jsonString, $asArray);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new Exception(json_last_error_msg(), json_last_error());
        }

        return $json;
    }

    private function closeConnection(): void
    {
        try{
            ob_end_clean();
        }catch (Exception){

        }
        header("Connection: close");
        ignore_user_abort(TRUE);
        ob_start();
        echo 'WEB_HOOK enable';
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();
        flush();
    }

    private function getLongPollingUpdates(): void
    {

        $updateId = $this->cache->get("UPDATE_ID_$this->identifier");
        $updateIdTime = $this->cache->get("UPDATE_ID_TIME_$this->identifier");
        if ($updateId === NULL) {
            $updateId = 0;
        }
        if ($updateIdTime === NULL || $updateIdTime < time() - 24 * 60 * 60) {
            $updateId = 0;
        }
        $this->updates = $this->bot->getUpdates(
            [
                "offset" => $updateId,
            ],
            FALSE
        );
        $this->updates = array_filter($this->updates, function ($update) use ($updateId) {
            /**
             * @var  $update Update
             * */
            return $update->updateId >= $updateId;
        });
    }

    private function getRawBody(): bool|string
    {
        return file_get_contents('php://input');
    }

    public function haveUpdate(): bool
    {
        return count($this->updates) > 0;
    }

    public function getUpdate(bool $increaseUpdateId = TRUE): Update|null
    {
        $update = NULL;
        if ($this->haveUpdate()) {
            /** @var Update $update */
            $update = array_shift($this->updates);
            //env("INCREASE_UPDATE_ID", TRUE) &&
            if ($increaseUpdateId) {
                $this->cache->set("UPDATE_ID_$this->identifier", $update->updateId + 1);
                $this->cache->set("UPDATE_ID_TIME_$this->identifier", time());
            }
        }

        return $update;
    }
}