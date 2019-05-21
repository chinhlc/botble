<?php

namespace Botble\LogViewer\Facades;

use Illuminate\Support\Facades\Facade;

class LogViewerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     * @author Sang Nguyen
     */
    protected static function getFacadeAccessor()
    {
        return 'botble::log-viewer';
    }
}
