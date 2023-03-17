<?php

namespace Peyman1992\TelegramFramework\Provider;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Peyman1992\TelegramFramework\Injector\DependencyInjector;
use Peyman1992\TelegramFramework\Router\Router;
use Peyman1992\TelegramFramework\Session\TelegramSessionManager;

class TelegramFrameworkServiceProvider extends ServiceProvider
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

        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'telegram-framework');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('telegram-framework.php'),
        ]);
    }
}