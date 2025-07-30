<?php

namespace Blashbrook\PAPIClient\Facades;

use Illuminate\Support\Facades\Facade;

class PAPIClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'papiclient';
    }
}
