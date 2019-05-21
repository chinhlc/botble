<?php

namespace Botble\Base\Repositories\Caches;

use Botble\Base\Repositories\Interfaces\MetaBoxInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Support\Services\Cache\CacheInterface;

class MetaBoxCacheDecorator extends CacheAbstractDecorator implements MetaBoxInterface
{
    /**
     * @var MetaBoxInterface
     */
    protected $repository;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * MetaBoxCacheDecorator constructor.
     * @param MetaBoxInterface $repository
     * @param CacheInterface $cache
     * @author Sang Nguyen
     */
    public function __construct(MetaBoxInterface $repository, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }
}
