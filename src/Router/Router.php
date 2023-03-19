<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 1/15/2019
 * Time: 7:27 PM
 */

namespace Peyman1992\TelegramLibrary\Router;

use Exception;
use Illuminate\Contracts\Container\Container;
use Peyman1992\TelegramLibrary\PipeLine;
use ReflectionClass;
use Telegram\Bot\Objects\Update;
use function base_path;
use function class_exists;
use function compact;
use function count;
use function dd;
use function file_exists;
use function is_array;
use function is_callable;
use function json_encode;

class Router
{
    private Container $container;
    private ?Route $notFoundHandler = NULL;
    private array $routes = [];
    private array $middlewares = [];
    private int $groupIndex = 0;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    private function getValidatedChecker($checker): callable|array
    {
        if (is_callable($checker)) {
            return $checker;
        }
        if (is_array($checker) && count($checker) >= 2) {
            $className = $checker[0];
            if (!class_exists($className)) {
                throw new \Exception("The {$className} checker is not exists");
            }
            $class = new ReflectionClass($className);
            $methodName = $checker[1];
            if (!$class->hasMethod($methodName)) {
                throw new \Exception("The {$className} checker not has {$methodName} method.");
            }
            $parameters = [];
            if (isset($checker[2])) {
                if (!is_array($checker[2])) {
                    throw new \Exception("The $checker[2] not a valid parameter name value pair.");
                }
                $parameters = $checker[2];
            }

            return compact("className", "methodName", "parameters");
        }
        throw new \Exception("The " . json_encode($checker) . " checker is not valid");
    }

    private function getValidatedAction($action): callable|array|null
    {
        if ($action === NULL || is_callable($action)) {
            return $action;
        }
        if (is_array($action) && count($action) == 2) {
            $className = $action[0];
            if (!class_exists($className)) {
                throw new \Exception("The {$className} controller is not exists");
            }
            $class = new ReflectionClass($className);
            $methodName = $action[1];
            if (!$class->hasMethod($methodName)) {
                throw new \Exception("The {$className} controller not has {$methodName} method.");
            }

            return compact("className", "methodName");
        }
        throw new \Exception("The " . json_encode($action) . " controller is not valid");
    }

    private function createRoute($checker, $action): Route
    {
        $checker = $this->getValidatedChecker($checker);
        $action = $this->getValidatedAction($action);
        $route = new Route($this->container, $checker, $action);
        foreach ($this->middlewares as $middlewares) {
            $route->middleware($middlewares);
        }
        $this->resetMiddleware();

        return $route;
    }

    private function resetMiddleware(): void
    {
        if ($this->groupIndex < count($this->middlewares)) {
            array_pop($this->middlewares);
        }
    }

    public function register($checker, $action = NULL): Route
    {
        $route = $this->createRoute($checker, $action);
        $this->routes[] = $route;

        return $route;
    }

    public function registerNotFound($action): Route
    {
        $route = $this->createRoute(function () {
            return TRUE;
        }, $action);
        $this->notFoundHandler = $route;

        return $route;
    }

    public function middleware($middlewares): static
    {
        if (is_string($middlewares)) {
            $middlewareArray[] = $middlewares;
        } else {
            $middlewareArray = $middlewares;
        }

        if (!is_array($middlewareArray)) {
            throw  new \Exception("The $middlewares not valid middlewares");
        }
        if (!empty($middlewareArray)) {
            $this->middlewares[] = $middlewareArray;
        }

        return $this;
    }

    public function group(\Closure $func): void
    {
        $this->groupIndex = count($this->middlewares);
        $func();
        $this->groupIndex = --$this->groupIndex;
        $this->resetMiddleware();
    }

    public function load($name): void
    {
        $path = base_path("routes/{$name}.php");
        $callback = function () use ($path) {
            if (file_exists($path)) {
                require $path;
            } else {
                throw new \Exception("The {$path} file not exists.");
            }
        };
        $this->group($callback);
    }

    private function runNotfound(Update $update): void
    {
        $route = $this->notFoundHandler;
        if ($route) {
            $endCallBack = function (Update $update) use ($route) {
                $route->runAction($update);
            };
            $pipeline = new PipeLine($this->container, $update, $endCallBack);

            $pipeline->run($route->getMiddlewares());
        }
    }

    public function dispatch(Update $update): void
    {
        ControllerAndCheckerBinder::destroy();
        $route = $this->findRoute($update);
        if ($route instanceof Route) {
            $endCallBack = function (Update $update) use ($route) {
                try {
                    $route->runAction($update);
//                } catch (EloquentNotFoundException $e) {
                } catch (Exception $e) {
                    dd($e);

                    $this->runNotfound($update);
                }
            };
            $pipeline = new PipeLine($this->container, $update, $endCallBack);

            $pipeline->run($route->getMiddlewares());
        } else {
            $this->runNotfound($update);
        }
    }

    private function findRoute(Update $update): ?Route
    {
        /* @var Route $route */
        foreach ($this->routes as $route) {
            $result = $route->runChecker($update);
            if ($result) {
                return $route;
            }
        }

        return NULL;
    }
}