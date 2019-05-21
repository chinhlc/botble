<?php

namespace Botble\Slug\Repositories\Caches;

use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Support\Services\Cache\CacheInterface;

class SlugCacheDecorator extends CacheAbstractDecorator implements SlugInterface
{
    /**
     * @var SlugInterface
     */
    protected $repository;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * SlugCacheDecorator constructor.
     * @param SlugInterface $repository
     * @param CacheInterface $cache
     * @author Sang Nguyen
     */
    public function __construct(SlugInterface $repository, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }
}
