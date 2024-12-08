<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PHPX extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'phpx';
    }
}
