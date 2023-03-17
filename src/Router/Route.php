<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramLibrary\Router;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Config;
use Peyman1992\TelegramLibrary\Injector\DependencyInjector;
use Peyman1992\TelegramLibrary\Injector\Exceptions\DependencyInjectionException;
use Peyman1992\TelegramLibrary\Traits\CheckerTrait;
use Telegram\Bot\Objects\Update;

class Route
{
    private Container $container;
    private DependencyInjector $dependencyInjector;
    private array|Closure $checker;
    private array|Closure|null $action;
    private array $middlewares = [];
    private bool $isClassController = FALSE;
    private bool $isClassChecker = FALSE;
    private array $checkerReturnedParameters = [];

    public function __construct(Container $container, $checker, $action)
    {
        $this->container = $container;
        $this->dependencyInjector = $this->container->make(DependencyInjector::class);
        if (is_array($checker)) {
            $this->isClassChecker = TRUE;
        }
        $this->checker = $checker;

        if (is_array($action)) {
            $this->isClassController = TRUE;
        }
        $this->action = $action;
    }

    public function middleware($middlewares): static
    {
        if (is_string($middlewares)) {
            $middlewareArray = Config::get("telegram-framework.middlewares.{$middlewares}");
        } else {
            $middlewareArray = $middlewares;
        }
        if (!is_array($middlewareArray)) {
            throw  new \Exception("The $middlewares not valid middlewares");
        }

        $middlewareArray1 = [];
        foreach ($middlewareArray as $middleware) {
            if (in_array($middleware, $this->middlewares)) {
                continue;
            }
            $middlewareArray1[] = $middleware;
        }
        $this->middlewares = array_merge($this->middlewares, $middlewareArray1);

        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function runChecker(Update $update): bool
    {
        $this->checkerReturnedParameters = [];
        $checker = $this->checker;
        try {
            if ($this->isClassChecker) {
                $className = $checker["className"];
                $methodName = $checker["methodName"];
                $checkResult = $this->runMethod($className, $methodName, $checker["parameters"]);
            } else {
                $checkResult = $this->runClosure($checker, [$update]);
            }

            if ($checkResult === FALSE) {
                return FALSE;
            } else {
                if ($checkResult !== TRUE) {
                    $this->checkerReturnedParameters = $checkResult;
                }

                return TRUE;
            }
        } catch (\Exception $e) {
            if ($e instanceof DependencyInjectionException) {
                throw $e;
            }
//            if (config('telegram-framework.report_checker_exceptions')) {
            if (config('telegram-framework.debug_mode')) {
                throw $e;
            } else {
                return FALSE;
            }
        }
    }

    public function runAction(Update $update): void
    {
        $action = $this->action;
        if ($this->isClassController) {
            $className = $action["className"];
            $methodName = $action["methodName"];

            $this->runMethod($className, $methodName, $this->checkerReturnedParameters);
        } elseif ($action === NULL) {
            return;
        } else {
            $this->checkerReturnedParameters[] = $update;

            $this->runClosure($action, $this->checkerReturnedParameters);
        }
    }

    private function runMethod($className, $methodName, $parameters = NULL): mixed
    {
        if (ControllerAndCheckerBinder::hasBind($className)) {
            $object = ControllerAndCheckerBinder::make($className);
        } else {
            $object = $this->dependencyInjector->createClass($className, $parameters);
            ControllerAndCheckerBinder::bind($className, $object);
        }
        $traits = class_uses($object);
        if (in_array(CheckerTrait::class, $traits) && $object->ignore === TRUE) {
            return FALSE;
        }

        return $this->dependencyInjector->callMethod($object, $methodName, $parameters);
    }

    private function runClosure($func, $parameter = NULL)
    {
        return $this->dependencyInjector->callClosure($func, $parameter);
    }
}