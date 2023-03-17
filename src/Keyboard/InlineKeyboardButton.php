<?php

namespace Peyman1992\TelegramLibrary\Keyboard;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class InlineKeyboardButton implements Arrayable, Jsonable, JsonSerializable
{
    use ToJsonTrait;

    private string $text;
    private string $key;
    private string $value;

    public function __construct(string $text, $key, $value)
    {
        $this->text = $text;
        $this->key = $key;
        $this->value = $value;
    }

    public function toArray(): array
    {
        return ["text" => $this->text, $this->key => $this->value];
    }

}