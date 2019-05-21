<?php

namespace Botble\Note\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Support\Services\Cache\CacheInterface;
use Botble\Note\Repositories\Interfaces\NoteInterface;

class NoteCacheDecorator extends CacheAbstractDecorator implements NoteInterface
{
    /**
     * @var NoteInterface
     */
    protected $repository;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * NoteCacheDecorator constructor.
     * @param NoteInterface $repository
     * @param CacheInterface $cache
     * @author Sang Nguyen
     */
    public function __construct(NoteInterface $repository, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }
}
