<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Presale extends Facade
{
    /**
     * Get facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'presale-service';
    }
}
