<?php

namespace Peyman1992\TelegramFramework\Keyboard;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class KeyboardButton implements Arrayable, Jsonable, JsonSerializable
{
    use ToJsonTrait;

    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function toArray(): array
    {
        return ["text" => $this->text];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}