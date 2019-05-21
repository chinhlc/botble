<?php

namespace Botble\Member\Repositories\Caches;

use Botble\Member\Repositories\Interfaces\MemberInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Support\Services\Cache\CacheInterface;

class MemberCacheDecorator extends CacheAbstractDecorator implements MemberInterface
{
    /**
     * @var MemberInterface
     */
    protected $repository;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * MemberCacheDecorator constructor.
     * @param MemberInterface $repository
     * @param CacheInterface $cache
     * @author Sang Nguyen
     */
    public function __construct(MemberInterface $repository, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }
}
