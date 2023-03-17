<?php /** @noinspection PhpUnused */

/** @noinspection PhpRedundantCatchClauseInspection */

/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/11/2019
 * Time: 8:09 PM
 */

namespace Peyman1992\TelegramFramework\Injector;

use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Peyman1992\TelegramFramework\Injector\Exceptions\CallFunctionInjectionException;
use Peyman1992\TelegramFramework\Injector\Exceptions\CallMethodInjectionException;
use Peyman1992\TelegramFramework\Injector\Exceptions\CallStaticMethodInjectionException;
use Peyman1992\TelegramFramework\Injector\Exceptions\CreateClassInjectionException;
use Peyman1992\TelegramFramework\Injector\Exceptions\EloquentInjectionException;
use Peyman1992\TelegramFramework\Injector\Exceptions\EloquentNotFoundException;
use Peyman1992\TelegramFramework\Injector\Exceptions\ResolveParameterException;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use function dump;
use function get_class;

class DependencyInjector
{

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function createClass($className, $parameters = NULL)
    {
        try {
            if ($this->container->has($className)) {
                return $this->container->make($className);
            }
            $reflection = new ReflectionClass($className);
            $constructor = $reflection->getConstructor();
            if ($constructor === NULL) {
                return new $className();
            }
            $params = $constructor->getParameters();
            $injectParameters = $this->getParametersArray($params, $parameters);

            return new $className(...$injectParameters);
        } catch (ResolveParameterException $e) {
            $paramName = $e->getParamName();
            $exceptionText = "The $paramName parameter of class  $className cant resolve";
            throw  new CreateClassInjectionException($exceptionText);
        }
    }

    public function callMethod($object, $methodName, $parameters = NULL)
    {
        try {
            $injectParameters = $this->getMethodParameters(get_class($object), $methodName, $parameters);

            return $object->{$methodName}(...$injectParameters);
        } catch (ResolveParameterException $e) {
            $paramName = $e->getParamName();
            $className = get_class($object);
            $exceptionText = "The $paramName parameter of method $methodName on class $className cant resolve";
            throw  new CallMethodInjectionException($exceptionText);
        }
    }

    public function callStaticMethod($className, $methodName, $parameters = NULL)
    {
        try {
            $injectParameters = $this->getMethodParameters($className, $methodName, $parameters);

            return $className::{$methodName}(...$injectParameters);
        } catch (ResolveParameterException $e) {
            $paramName = $e->getParamName();
            $exceptionText = "The $paramName parameter of static method $methodName on class $className cant resolve.";
            throw  new CallStaticMethodInjectionException($exceptionText);
        }
    }

    public function callClosure($function, &$parameters = NULL)
    {
        try {
            $injectParameters = $this->getFunctionParameters($function, $parameters);

            return $function(...$injectParameters);
        } catch (ResolveParameterException $e) {
            $paramName = $e->getParamName();

            $exceptionText = "The $paramName parameter of function cant resolve.";
            throw  new CallFunctionInjectionException($exceptionText);
        }
    }

    public function getMethodParameters($className, $methodName, $parameters = NULL): array
    {
        $reflectionMethod = new ReflectionMethod($className, $methodName);

        $params = $reflectionMethod->getParameters();

        return $this->getParametersArray($params, $parameters);
    }

    private function resolve(ReflectionParameter $param, &$parameters = NULL)
    {
        if ($param->getType()) {
            $argsClassName = $param->getType()->getName();
            try {
//                dump(' $this->container->make($argsClassName)');
//                dump($this->findInClassParameters($argsClassName, $parameters));
//                dump(' $this->container->make($argsClassName)');

                return $this->findInClassParameters($argsClassName, $parameters);
            } catch (Exception $e) {
            }
            if ($this->container->has($argsClassName)) {
                return $this->container->make($argsClassName);
            } elseif ((new ReflectionClass($argsClassName))->isSubclassOf(Model::class)) {
                return $this->resolverEloquent($argsClassName, $parameters);
            } else {
                return $this->createClass($argsClassName, NULL);
            }
        } else {
            $name = $param->name;
            if ($this->container->has($name)) {
                return $this->container->make($name);
            }
            if (is_array($parameters)) {
                if (isset($parameters[$name])) {
                    $parameter = $parameters[$name];
                    unset($parameters[$name]);

                    return $parameter;
                } else {
                    try {
                        return $this->findInNotClassParameters($parameters);
                    } catch (Exception $e) {
                    }
                }
            }
            if ($param->isDefaultValueAvailable()) {
                return $param->getDefaultValue();
            }
            throw new ResolveParameterException($param->name);
        }
    }

    private function resolverEloquent($argsClassName, &$parameters = NULL)
    {
        $explode = explode("\\", $argsClassName);
        $paramName = strtolower(array_pop($explode));
        if (!isset($parameters[$paramName])) {
            throw new EloquentInjectionException("The {$argsClassName} eloquent can't resolve because {$paramName} index not found in dependency injector parameters.");
        }
        $value = $parameters[$paramName];
        unset($parameters[$paramName]);

        $refClass = new ReflectionClass($argsClassName);
        $primaryKey = $refClass->getDefaultProperties()["routeKey"];
        $resolve = $argsClassName::where($primaryKey, "=", $value)->first();
        if ($resolve === NULL) {
            throw new EloquentNotFoundException("The {$argsClassName} eloquent can't resolve because {$value} primary key not exist in database.");
        }

        return $resolve;
    }

    private function findInClassParameters($className, &$parameters = NULL)
    {
        if (is_array($parameters)) {
            foreach ($parameters as $key => $parameter) {
                if (is_object($parameter) && $className === get_class($parameter)) {
                    unset($parameters[$key]);

                    return $parameter;
                }
            }
        }

        throw new Exception();
    }

    private function findInNotClassParameters(&$parameters = NULL)
    {
        if (is_array($parameters)) {
            foreach ($parameters as $key => $parameter) {
                if (!is_object($parameter) && is_integer($key)) {
                    unset($parameters[$key]);

                    return $parameter;
                }
            }
        }

        throw new Exception();
    }

    private function getParametersArray($params, &$parameters = NULL): array
    {
        $injectParameters = [];
        foreach ($params as $param) {
            /* @var ReflectionParameter $param */
            if ($param->isOptional()) {
                try {
                    $parameter = $this->resolve($param, $parameters);;
                    $injectParameters[] = $parameter;
                } catch (Exception $e) {
                    break;
                }
            } else {
                $injectParameters[] = $this->resolve($param, $parameters);
            }
        }

        return $injectParameters;
    }

    public function getFunctionParameters($function, &$parameters = NULL): array
    {
        $reflectionMethod = new ReflectionFunction($function);
        $params = $reflectionMethod->getParameters();

        return $this->getParametersArray($params, $parameters);
    }

}