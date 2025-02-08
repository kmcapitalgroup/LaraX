<?php

namespace MultiversX\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class MultiversX extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'multiversx';
    }
}
