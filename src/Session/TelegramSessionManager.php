<?php

namespace Peyman1992\TelegramFramework\Session;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use function storage_path;
use const DIRECTORY_SEPARATOR;

/**
 * @method get($key, $defaultValue = NULL)
 * @method set($key, $value)
 * @method has($key)
 * @method remove($key)
 * @method destroy()
 */
class TelegramSessionManager extends Manager
{
    protected string $id;

    public function __construct(Container $container, string $id)
    {
        parent::__construct($container);
        $this->id = $id;
    }

    public function getDefaultDriver(): string
    {
        return 'file';
    }

    /**
     * Create an instance of the file session driver.
     *
     * @return SessionDriver
     * @throws \Exception
     */
    protected function createFileDriver(): SessionDriver
    {
        return new FileSessionDriver($this->id, storage_path('telegram' . DIRECTORY_SEPARATOR . 'sessions'));
    }
}