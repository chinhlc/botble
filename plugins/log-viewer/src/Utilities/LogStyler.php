<?php

namespace Botble\LogViewer\Utilities;

use Botble\LogViewer\Contracts\Utilities\LogStyler as LogStylerContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class LogStyler implements LogStylerContract
{
    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create a new instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     * @author ARCANEDEV
     */
    public function __construct(ConfigContract $config)
    {
        $this->config = $config;
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
    private function get($key, $default = null)
    {
        return $this->config->get('plugins.log-viewer.general.' . $key, $default);
    }

    /**
     * Make level icon.
     *
     * @param  string $level
     * @param  string|null $default
     *
     * @return string
     * @author ARCANEDEV
     */
    public function icon($level, $default = null)
    {
        return '<i class="' . $this->get('icons.' . $level, $default) . '"></i>';
    }

    /**
     * Get level color.
     *
     * @param  string $level
     * @param  string|null $default
     *
     * @return string
     * @author ARCANEDEV
     */
    public function color($level, $default = null)
    {
        return $this->get('colors.levels.' . $level, $default);
    }
}
