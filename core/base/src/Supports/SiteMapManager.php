<?php

namespace Botble\Base\Supports;

use AdminBar;

class SiteMapManager
{
    /**
     * @var mixed
     */
    protected $site_map;

    /**
     * SiteMapManager constructor.
     * @author Sang Nguyen
     */
    public function __construct()
    {
        // create new site map object
        $this->site_map = app()->make('sitemap');

        // set cache (key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean))
        // by default cache is disabled
        $this->site_map->setCache('public.sitemap', config('core.base.general.cache_site_map'));

        $this->site_map->add(route('public.index'), '2016-10-20 10:00', '1.0', 'daily');

        AdminBar::setIsDisplay(false);
    }

    /**
     * @param $url
     * @param $date
     * @param string $priority
     * @param string $sequence
     * @return $this
     * @author Sang Nguyen
     */
    public function add($url, $date, $priority = '1.0', $sequence = 'daily')
    {
        if (!$this->site_map->isCached()) {
            $this->site_map->add($url, $date, $priority, $sequence);
        }
        return $this;
    }

    /**
     * @param string $type
     * @return mixed
     * @author Sang Nguyen
     */
    public function render($type = 'xml')
    {
        // show your site map (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $this->site_map->render($type);
    }
}
