<?php

namespace Peyman1992\TelegramFramework\Keyboard;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use function array_merge;

class Keyboard implements Arrayable, Jsonable, JsonSerializable
{
    use ToJsonTrait;

    private bool $inlineKeyboard;
    private ?bool $resizeKeyboard = NULL;
    private ?bool $oneTimeKeyboard = NULL;
    private ?bool $selective = NULL;
    private ?string $inputFieldPlaceholder = NULL;
    private array $keyboard = [];

    public function __construct($inlineKeyboard = FALSE)
    {
        $this->inlineKeyboard = $inlineKeyboard;
    }

    public function addRow(JsonSerializable...$button)
    {
        $this->keyboard[] = $button;
    }

    public function setResizeKeyboard(bool $value)
    {
        $this->resizeKeyboard = $value;
    }

    public function setOneTimeKeyboard(bool $value)
    {
        $this->oneTimeKeyboard = $value;
    }

    public function setSelective(bool $value)
    {
        $this->selective = $value;
    }

    public function setInputFieldPlaceholder(string $value)
    {
        $this->inputFieldPlaceholder = $value;
    }

    public function toArray(): array
    {
        $extra = [];
        $key = "keyboard";
        if ($this->inlineKeyboard) {
            $key = "inline_keyboard";
        }
        if (!$this->inlineKeyboard) {
            if ($this->resizeKeyboard !== NULL) {
                $extra['resize_keyboard'] = $this->resizeKeyboard;
            }
            if ($this->oneTimeKeyboard !== NULL) {
                $extra['one_time_keyboard'] = $this->oneTimeKeyboard;
            }
            if ($this->selective !== NULL) {
                $extra['selective'] = $this->selective;
            }
            if ($this->inputFieldPlaceholder !== NULL) {
                $extra['input_field_placeholder'] = $this->inputFieldPlaceholder;
            }
        }
        $keyboard = [
            $key => $this->keyboard,
        ];

        return array_merge($keyboard, $extra);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}