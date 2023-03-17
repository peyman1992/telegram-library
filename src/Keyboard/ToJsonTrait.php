<?php

namespace Peyman1992\TelegramLibrary\Keyboard;

use Illuminate\Database\Eloquent\JsonEncodingException;
use function get_class;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use const JSON_ERROR_NONE;

trait ToJsonTrait
{
    public function toJson($options = 0): bool|string
    {
        $json = json_encode($this->jsonSerialize(), $options);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonEncodingException('Error encoding keyboard [' . get_class($this) . '] to JSON: ' . json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}