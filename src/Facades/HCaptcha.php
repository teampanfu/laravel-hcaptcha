<?php

namespace Panfu\Laravel\HCaptcha\Facades;

use Illuminate\Support\Facades\Facade;

class HCaptcha extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'hcaptcha';
    }
}
