<?php

namespace Botble\Language\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Support\Services\Cache\CacheInterface;
use Botble\Language\Repositories\Interfaces\LanguageMetaInterface;

class LanguageMetaCacheDecorator extends CacheAbstractDecorator implements LanguageMetaInterface
{
    /**
     * @var LanguageMetaInterface
     */
    protected $repository;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * LanguageCacheDecorator constructor.
     * @param LanguageMetaInterface $repository
     * @param CacheInterface $cache
     * @author Sang Nguyen
     */
    public function __construct(LanguageMetaInterface $repository, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }
}
