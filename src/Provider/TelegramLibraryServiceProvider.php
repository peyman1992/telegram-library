<?php

namespace Peyman1992\TelegramLibrary\Provider;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Peyman1992\TelegramLibrary\Injector\DependencyInjector;
use Peyman1992\TelegramLibrary\Router\Router;
use Peyman1992\TelegramLibrary\Session\TelegramSessionManager;

class TelegramLibraryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            DependencyInjector::class,
            function (Container $container) {
                return new DependencyInjector($container);
            }
        );
        $this->app->singleton(
            Router::class,
            function (Container $container) {
                return new Router($container);
            }
        );
        $this->app->bind(
            'telegram-session',
            function (Container $app, array $parameters) {
                if ($this->app->has('telegram-session-' . $parameters['id'])) {
                    return $this->app->make('telegram-session-' . $parameters['id']);
                }
                $session = new TelegramSessionManager($app, $parameters['id']);
                $this->app->instance('telegram-session-' . $parameters['id'], $session);

                return $session;
            }
        );

        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'telegram-library');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('telegram-library.php'),
        ]);
    }
}