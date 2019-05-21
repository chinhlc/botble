<?php

namespace Botble\RequestLog\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Support\Services\Cache\CacheInterface;
use Botble\RequestLog\Repositories\Interfaces\RequestLogInterface;

class RequestLogCacheDecorator extends CacheAbstractDecorator implements RequestLogInterface
{

    /**
     * @var RequestLogInterface
     */
    protected $repository;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * PostCacheDecorator constructor.
     * @param RequestLogInterface $repository
     * @param CacheInterface $cache
     * @author Sang Nguyen
     */
    public function __construct(RequestLogInterface $repository, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }
}
