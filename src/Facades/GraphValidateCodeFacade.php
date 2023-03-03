<?php

namespace Kanelli\GraphValidateCode\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Facade
 * @package Kanelli\ImageVerifyCode
 */
class GraphValidateCodeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gvc';
    }
}