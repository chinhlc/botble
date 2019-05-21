<?php

namespace Botble\ACL\Facades;

use Illuminate\Support\Facades\Facade;

class AclManagerFacade extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'AclManagerFacade';
    }
}
