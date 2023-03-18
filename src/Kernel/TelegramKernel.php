<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramLibrary\Kernel;

use Illuminate\Contracts\Container\Container;
use Peyman1992\TelegramLibrary\Interfaces\ErrorHandler;
use Peyman1992\TelegramLibrary\PipeLine;
use Peyman1992\TelegramLibrary\Request\Request;
use Peyman1992\TelegramLibrary\Router\Router;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use Throwable;
use function config;
use function dd;
use function dump;

class TelegramKernel
{
    private Container $container;
    private Router $router;
    private Request $request;
    protected array $middleware = [];
    protected bool $increaseUpdateId;

    public static function bindBot(Container $container, string $botToken): void
    {
        $container->singleton(
            'bot',
            function () use ($botToken) {
                $api = new Api($botToken);
                return $api;
            }
        );
        $container->singleton(
            Request::class,
            function (Container $app) {
                return new Request($app);
            }
        );
    }

    public function __construct(Container $container, string $botToken, bool $increaseUpdateId = TRUE)
    {
        $this->container = $container;
        self::bindBot($container, $botToken);

        $this->increaseUpdateId = $increaseUpdateId;
        $this->router = $container->make(Router::class);
        $this->request = $this->container->make(Request::class);
        $this->middleware = config('telegram-library.middlewares.globals');
    }

    public function handle(string $routeFileName): void
    {
        $this->router->load($routeFileName);
        $this->request->getUpdates();
        $endCallBack = function (Update $update) {
            $this->router->dispatch($update);
        };
        if ($this->request->haveUpdate()) {
            while ($this->request->haveUpdate()) {
                $update = $this->request->getUpdate($this->increaseUpdateId);
                $this->container->instance(Update::class, $update);
                try {
                    //run global middlewares and then router dispatcher
                    $pipeline = new PipeLine($this->container, $update, $endCallBack);
                    $pipeline->run($this->middleware);
                } catch (Throwable $e) {
                    dd($e);
                    //todo implement error handling
                    $handles = $this->container->make(ErrorHandler::class);
                    $handles->report($e, $update);
                    $handles->render($e, $update);
                }
            }
        } else {
            if (config('telegram-library.debug_mode')) {
                dump("no telegram update available.");
            }
        }
    }
}