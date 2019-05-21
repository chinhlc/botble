<?php

namespace Botble\SeoHelper\Entities;

use Botble\SeoHelper\Bases\MetaCollection as BaseMetaCollection;
use Botble\SeoHelper\Helpers\Meta;

class MetaCollection extends BaseMetaCollection
{
    /**
     * Ignored tags, they have dedicated class.
     *
     * @var array
     */
    protected $ignored = [
        'description', 'keywords'
    ];

    /**
     * Add a meta to collection.
     *
     * @param  string $name
     * @param  string $content
     *
     * @return \Botble\SeoHelper\Entities\MetaCollection
     * @author ARCANEDEV
     */
    public function add($name, $content)
    {
        $meta = Meta::make($name, $content);

        if ($meta->isValid() && !$this->isIgnored($name)) {
            $this->put($meta->key(), $meta);
        }

        return $this;
    }
}
