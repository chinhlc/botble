<?php

namespace Botble\LogViewer\Utilities;

use Botble\LogViewer\Contracts\Utilities\LogMenu as LogMenuContract;
use Botble\LogViewer\Contracts\Utilities\LogStyler as LogStylerContract;
use Botble\LogViewer\Entities\Log;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class LogMenu implements LogMenuContract
{
    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The log styler instance.
     *
     * @var \Botble\LogViewer\Contracts\Utilities\LogStyler
     */
    protected $styler;

    /**
     * LogMenu constructor.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     * @param  \Botble\LogViewer\Contracts\Utilities\LogStyler $styler
     * @author ARCANEDEV
     */
    public function __construct(ConfigContract $config, LogStylerContract $styler)
    {
        $this->setConfig($config);
        $this->setLogStyler($styler);
    }

    /**
     * Set the config instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     *
     * @return \Botble\LogViewer\Utilities\LogMenu
     * @author ARCANEDEV
     */
    public function setConfig(ConfigContract $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set the log styler instance.
     *
     * @param  \Botble\LogViewer\Contracts\Utilities\LogStyler $styler
     *
     * @return \Botble\LogViewer\Utilities\LogMenu
     * @author ARCANEDEV
     */
    public function setLogStyler(LogStylerContract $styler)
    {
        $this->styler = $styler;

        return $this;
    }

    /**
     * Make log menu.
     *
     * @param  \Botble\LogViewer\Entities\Log $log
     * @param  bool $trans
     *
     * @return array
     * @author ARCANEDEV
     */
    public function make(Log $log, $trans = true)
    {
        $items = [];
        $route = $this->config('menu.filter-route');

        foreach ($log->tree($trans) as $level => $item) {
            $items[$level] = array_merge($item, [
                'url' => route($route, [$log->date, $level]),
                'icon' => $this->isIconsEnabled() ? $this->styler->icon($level) : '',
            ]);
        }

        return $items;
    }

    /**
     * Check if the icons are enabled.
     *
     * @return bool
     * @author ARCANEDEV
     */
    private function isIconsEnabled()
    {
        return (bool)$this->config('menu.icons-enabled', false);
    }

    /**
     * Get config.
     *
     * @param  string $key
     * @param  mixed $default
     *
     * @return mixed
     * @author ARCANEDEV
     */
    private function config($key, $default = null)
    {
        return $this->config->get('plugins.log-viewer.general.' . $key, $default);
    }
}
