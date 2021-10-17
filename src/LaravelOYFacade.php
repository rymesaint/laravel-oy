<?php
namespace rymesaint\LaravelOY;

use Illuminate\Support\Facades\Facade;

class LaravelOYFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'oypayment';
    }
}