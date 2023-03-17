<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramLibrary;

use Closure;
use Illuminate\Contracts\Container\Container;
use Peyman1992\TelegramLibrary\Injector\DependencyInjector;
use Telegram\Bot\Objects\Update;

class PipeLine
{
    private Container $container;
    private Update $update;
    private Closure $endClosure;
    private array $pipes;
    private int $index = 0;
    private Closure $callback;
    private string $methodName = "handle";

    public function __construct(Container $container, Update $update, Closure $endClosure)
    {
        $this->container = $container;
        $this->update = $update;
        $this->endClosure = $endClosure;
    }

    public function run(array $pipes)
    {
        $this->index = 0;
        $this->pipes = $pipes;
        $this->callback = function () {
            return $this->next();
        };

        return ($this->callback)();
    }

    public function next()
    {
        if ($this->index < count($this->pipes)) {
            $pipe = $this->pipes[$this->index++];

            return $this->runNext($pipe);
        } else {
            $this->runEnd();
        }
    }

    private function runNext($pipe)
    {
        if (is_callable($pipe)) {
            return $pipe($this->update, $this->callback);
        } elseif (is_string($pipe) && class_exists($pipe)) {
            $dependencyInjector = $this->container->make(DependencyInjector::class);
            $middleware = $dependencyInjector->createClass($pipe);
            if (method_exists($middleware, $this->methodName)) {
                $parameters = ["update" => $this->update, $this->callback];

                return $dependencyInjector->callMethod($middleware, $this->methodName, $parameters);
            } else {
                return $this->next();
            }
        }
        throw  new \Exception("The $pipe middleware is not exists.");
    }

    private function runEnd()
    {
        ($this->endClosure)($this->update);
    }

}