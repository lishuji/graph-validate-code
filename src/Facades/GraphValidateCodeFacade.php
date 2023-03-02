<?php

namespace Kanelli\GraphValidateCode\Facades;


use Kanelli\GraphValidateCode\Services\GraphValidateCodeServer;

/**
 * Class Facade
 * @package Kanelli\ImageVerifyCode
 */
class GraphValidateCodeFacade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return GraphValidateCodeServer::class;
    }
}