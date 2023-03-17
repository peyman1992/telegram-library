<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 1/29/2019
 * Time: 9:01 PM
 */

namespace Peyman1992\TelegramLibrary\Injector\Exceptions;

class ResolveParameterException extends \Exception
{
    private $paramName;

    public function __construct($paramName)
    {
        $this->paramName = $paramName;
        $errorText = "The {$paramName} parameter  can't resolve.";
        parent::__construct($errorText);
    }

    /**
     * @return mixed
     */
    public function getParamName()
    {
        return $this->paramName;
    }
}