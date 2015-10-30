<?php namespace Understand\UnderstandLumen;

use Illuminate\Support\Facades\Facade;

class UnderstandFieldProviderFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'understand-lumen.field-provider';
    }
}
