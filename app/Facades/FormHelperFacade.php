<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FormHelperFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'formhelper';
    }
}
