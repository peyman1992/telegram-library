<?php

namespace Peyman1992\TelegramFramework\Keyboard;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

final class KeyboardRemove implements Arrayable, Jsonable, JsonSerializable
{
    use ToJsonTrait;

    public function toArray(): array
    {
        return [
            'remove_keyboard' => TRUE,
        ];
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}