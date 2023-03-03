<?php

namespace Kanelli\GraphValidateCode\Facades;

use Illuminate\Support\Facades\Facade;
use Kanelli\GraphValidateCode\GraphValidateCode;

/**
 * Class Facade
 * @package Kanelli\ImageVerifyCode
 */
class GraphValidateCodeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GraphValidateCode::class;
    }
}