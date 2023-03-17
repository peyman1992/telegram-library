<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramFramework\Session;

use Peyman1992\TelegramFramework\Session\Exceptions\InvalidJsonException;

abstract class SessionDriver
{
    private array $session = [];
    private bool $initialized = FALSE;
    protected string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    private function validateRawSession($data)
    {
        $session = json_decode($data, TRUE);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $session = [];
        }

        return $session;
    }

    private function convertSessionToJson(): string
    {
        $session = json_encode($this->session);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new InvalidJsonException(json_last_error_msg(), json_last_error());
        }

        return $session;
    }

    protected function getSession()
    {
        $data = $this->read();

        $this->session = $this->validateRawSession($data);
    }

    protected function saveSession()
    {
        $json = $this->convertSessionToJson();

        return $this->write($json);
    }

    public function destroySession(): void
    {
        $this->destroy();
    }

    private function loadSessionIfNotLoaded(): void
    {
        if (!$this->initialized) {
            $this->getSession();
            $this->initialized = TRUE;
        }
    }

    abstract protected function read();

    abstract protected function write(string $json);

    abstract public function destroy();

    public function has($key): bool
    {
        $this->loadSessionIfNotLoaded();
        if (isset($this->session[$key])) {
            return TRUE;
        }

        return FALSE;
    }

    public function get($key, $defaultValue = NULL): mixed
    {
        $this->loadSessionIfNotLoaded();
        if ($this->has($key)) {
            return $this->session[$key];
        }

        return $defaultValue;
    }

    public function set($key, $Value): void
    {
        $this->loadSessionIfNotLoaded();
        $this->session[$key] = $Value;
        $this->saveSession();
    }

    public function remove($key): void
    {
        $this->loadSessionIfNotLoaded();
        if (isset($this->session[$key])) {
            unset($this->session[$key]);
        }
        $this->saveSession();
    }
}