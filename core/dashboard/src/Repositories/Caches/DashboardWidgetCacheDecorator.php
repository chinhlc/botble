<?php

namespace Botble\Dashboard\Repositories\Caches;

use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Support\Services\Cache\CacheInterface;

class DashboardWidgetCacheDecorator extends CacheAbstractDecorator implements DashboardWidgetInterface
{
    /**
     * @var DashboardWidgetInterface
     */
    protected $repository;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * WidgetCacheDecorator constructor.
     * @param DashboardWidgetInterface $repository
     * @param CacheInterface $cache
     * @author Sang Nguyen
     */
    public function __construct(DashboardWidgetInterface $repository, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }
}
