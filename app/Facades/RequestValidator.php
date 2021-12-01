<?php

namespace App\Facades;

class RequestValidator extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'request_validator';
    }
}