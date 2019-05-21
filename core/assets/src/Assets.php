<?php

namespace Botble\Assets;

use Botble\Base\Supports\Language;
use Collective\Html\HtmlBuilder;
use File;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class Assets
 * @package Botble\Assets
 * @author Sang Nguyen
 * @since 22/07/2015 11:23 PM
 */
class Assets
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var HtmlBuilder
     */
    protected $htmlBuilder;

    /**
     * @var array
     */
    protected $javascript = [];

    /**
     * @var array
     */
    protected $stylesheets = [];

    /**
     * @var array
     */
    protected $appModules = [];

    /**
     * @var mixed
     */
    protected $build;

    /**
     * @var array
     */
    protected $appendedJs = [
        'top' => [],
        'bottom' => [],
    ];

    /**
     * @var array
     */
    protected $appendedCss = [];

    /**
     * Assets constructor.
     * @author Sang Nguyen
     * @param Repository $config
     * @param Application $application
     * @param HtmlBuilder $htmlBuilder
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __construct(Repository $config, Application $application, HtmlBuilder $htmlBuilder)
    {
        $this->config = $config;
        $this->htmlBuilder = $htmlBuilder;
        $this->javascript = $this->config->get('core.assets.general.javascript');
        $this->stylesheets = $this->config->get('core.assets.general.stylesheets');

        $version = env('ASSET_VERSION', !$application->environment('local') ? (float)get_cms_version() : time());
        $this->build = $this->config->get('core.assets.general.enable_version') ? '?v=' . $version : null;
    }

    /**
     * Add Javascript to current module
     *
     * @param array $assets
     * @return $this
     * @author Sang Nguyen
     */
    public function addJavascript($assets)
    {
        if (!is_array($assets)) {
            $assets = [$assets];
        }
        $this->javascript = array_merge($this->javascript, $assets);
        return $this;
    }

    /**
     * Add Css to current module
     *
     * @param array $assets
     * @return $this
     * @author Sang Nguyen
     */
    public function addStylesheets($assets)
    {
        if (!is_array($assets)) {
            $assets = [$assets];
        }
        $this->stylesheets = array_merge($this->stylesheets, $assets);
        return $this;
    }

    /**
     * @param $assets
     * @return $this
     * @author Sang Nguyen
     */
    public function addStylesheetsDirectly($assets)
    {
        if (!is_array($assets)) {
            $assets = func_get_args();
        }
        foreach ($assets as &$item) {
            $item = $item . $this->build;
            if (!in_array($item, $this->appendedCss)) {
                $this->appendedCss[] = ['src' => $item, 'attributes' => []];
            }
        }
        return $this;
    }

    /**
     * @param $assets
     * @param string $location
     * @return $this
     * @author Sang Nguyen
     */
    public function addJavascriptDirectly($assets, $location = 'bottom')
    {
        if (!is_array($assets)) {
            $assets = func_get_args();
        }

        foreach ($assets as &$item) {
            $item = $item . $this->build;
            if (!in_array($item, $this->appendedJs[$location])) {
                $this->appendedJs[$location][] = ['src' => $item, 'attributes' => []];
            }
        }
        return $this;
    }

    /**
     * Add Module to current module
     *
     * @param array $modules
     * @return $this;
     * @author Sang Nguyen
     */
    public function addAppModule($modules)
    {
        if (!is_array($modules)) {
            $modules = [$modules];
        }
        $this->appModules = array_merge($this->appModules, $modules);
        return $this;
    }

    /**
     * Remove Css to current module
     *
     * @param array $assets
     * @return $this;
     * @author Sang Nguyen
     */
    public function removeStylesheets($assets)
    {
        if (!is_array($assets)) {
            $assets = [$assets];
        }
        foreach ($assets as $rem) {
            unset($this->stylesheets[array_search($rem, $this->stylesheets)]);
        }
        return $this;
    }

    /**
     * Add Javascript to current module
     *
     * @param array $assets
     * @return $this;
     * @author Sang Nguyen
     */
    public function removeJavascript($assets)
    {
        if (!is_array($assets)) {
            $assets = [$assets];
        }
        foreach ($assets as $rem) {
            unset($this->javascript[array_search($rem, $this->javascript)]);
        }
        return $this;
    }

    /**
     * Get All Javascript in current module
     *
     * @param string $location : top or bottom
     * @param boolean $version : show version?
     * @return array
     * @author Sang Nguyen
     */
    public function getJavascript($location = null, $version = true)
    {
        $scripts = [];
        if (!empty($this->javascript)) {
            // get the final scripts need for page
            $this->javascript = array_unique($this->javascript);
            foreach ($this->javascript as $js) {
                $jsConfig = 'core.assets.general.resources.javascript.' . $js;

                if ($this->config->has($jsConfig)) {
                    if ($location != null && $this->config->get($jsConfig . '.location') !== $location) {
                        // Skip assets that don't match this location
                        continue;
                    }

                    $src = $this->config->get($jsConfig . '.src.local');
                    $cdn = false;
                    if ($this->config->get($jsConfig . '.use_cdn') && !$this->config->get('core.assets.general.offline')) {
                        $src = $this->config->get($jsConfig . '.src.cdn');
                        $cdn = true;
                    }

                    if ($this->config->get($jsConfig . '.include_style')) {
                        $this->addStylesheets([$js]);
                    }

                    $attributes = $this->config->get($jsConfig . '.attributes', []);
                    if ($cdn == false) {
                        array_forget($attributes, 'integrity');
                        array_forget($attributes, 'crossorigin');
                    }

                    $version = $version ? $this->build : '';
                    if (!is_array($src)) {
                        $scripts[] = ['src' => $src . $version, 'attributes' => $attributes];
                    } else {
                        foreach ($src as $s) {
                            $scripts[] = ['src' => $s . $version, 'attributes' => $attributes];
                        }
                    }

                    if (empty($src) && $cdn && $location === 'top' && $this->config->has($jsConfig . '.fallback')) {
                        // Fallback to local script if CDN fails
                        $fallback = $this->config->get($jsConfig . '.fallback');
                        $scripts[] = [
                            'src' => $src,
                            'fallback' => $fallback,
                            'fallbackURL' => $this->config->get($jsConfig . '.src.local'),
                        ];
                    }
                }
            }
        }

        if (isset($this->appendedJs[$location])) {
            $scripts = array_merge($scripts, $this->appendedJs[$location]);
        }

        return $scripts;
    }

    /**
     * Get All CSS in current module
     *
     * @param array $lastModules : append last CSS to current module
     * @param boolean $version : show version?
     * @return array
     * @author Sang Nguyen
     */
    public function getStylesheets($lastModules = [], $version = true)
    {
        $stylesheets = [];
        if (!empty($this->stylesheets)) {
            if (!empty($lastModules)) {
                $this->stylesheets = array_merge($this->stylesheets, $lastModules);
            }
            // get the final scripts need for page
            $this->stylesheets = array_unique($this->stylesheets);
            foreach ($this->stylesheets as $style) {
                if ($this->config->has('core.assets.general.resources.stylesheets.' . $style)) {
                    $src = $this->config->get('core.assets.general.resources.stylesheets.' . $style . '.src.local');
                    $cdn = false;
                    if ($this->config->get('core.assets.general.resources.stylesheets.' . $style . '.use_cdn') && !$this->config->get('core.assets.general.offline')) {
                        $src = $this->config->get('core.assets.general.resources.stylesheets.' . $style . '.src.cdn');
                        $cdn = true;
                    }

                    $attributes = $this->config->get('core.assets.general.resources.stylesheets.' . $style . '.attributes', []);
                    if ($cdn == false) {
                        array_forget($attributes, 'integrity');
                        array_forget($attributes, 'crossorigin');
                    }

                    if (!is_array($src)) {
                        $src = [$src];
                    }
                    foreach ($src as $s) {
                        $stylesheets[] = [
                            'src' => $s . ($version ? $this->build : ''),
                            'attributes' => $attributes,
                        ];
                    }
                }
            }
        }

        $stylesheets = array_merge($stylesheets, $this->appendedCss);


        return $stylesheets;
    }

    /**
     * Get all modules in current module
     * @param boolean $version : show version?
     * @return array
     * @author Sang Nguyen
     */
    public function getAppModules($version = true)
    {
        $modules = [];
        if (!empty($this->appModules)) {
            // get the final scripts need for page
            $this->appModules = array_unique($this->appModules);
            foreach ($this->appModules as $module) {
                if (($module = $this->getModule($module, $version)) !== null) {
                    $modules[] = ['src' => $module, 'attributes' => []];
                }
            }
        }

        return $modules;
    }

    /**
     * Get a modules
     * @param string $module : module's name
     * @param boolean $version : show version?
     * @return string
     */
    protected function getModule($module, $version)
    {
        $pathPrefix = public_path() . '/vendor/core/js/app_modules/' . $module;

        $file = null;

        if (file_exists($pathPrefix . '.min.js')) {
            $file = $module . '.min.js';
        } elseif (file_exists($pathPrefix . '.js')) {
            $file = $module . '.js';
        }

        if ($file) {
            return '/vendor/core/js/app_modules/' . $file . ($version ? $this->build : '');
        }
        return null;
    }

    /**
     * Get all admin themes
     * @return array
     * @author Sang Nguyen
     */
    public function getThemes()
    {
        $themes = [];
        $public_path = public_path();
        if (!File::isDirectory($public_path . '/vendor/core/css/themes')) {
            return [];
        }
        foreach (File::files($public_path . '/vendor/core/css/themes') as $file) {
            $name = '/vendor/core/css/themes/' . basename($file);
            if (!str_contains($file, '.css.map')) {
                $themes[basename($file, '.css')] = $name;
            }
        }

        return $themes;
    }

    /**
     * @return array
     * @author Sang Nguyen
     */
    public function getAdminLocales()
    {
        $languages = [];
        $locales = scan_folder(resource_path('lang'));
        if (in_array('vendor', $locales)) {
            $locales = array_merge($locales, scan_folder(resource_path('lang/vendor')));
        }

        foreach ($locales as $locale) {
            if ($locale == 'vendor') {
                continue;
            }
            foreach (Language::getListLanguages() as $key => $language) {
                if (in_array($key, [$locale, str_replace('-', '_', $locale)]) || in_array($language[0], [$locale, str_replace('-', '_', $locale)])) {
                    $languages[$locale] = [
                        'name' => $language[2],
                        'flag' => $language[4]
                    ];
                }
            }
        }
        return $languages;
    }

    /**
     * @param $name
     * @param bool $version
     * @author Sang Nguyen
     */
    public function getJavascriptItemToHtml($name, $version = true)
    {
        $config = 'core.assets.general.resources.javascript.' . $name;
        if ($this->config->has($config)) {

            $src = $this->config->get($config . '.src.local');
            if ($this->config->get($config . '.use_cdn') && !$this->config->get('core.assets.general.offline')) {
                $src = $this->config->get($config . '.src.cdn');
            }

            if (!is_array($src)) {
                $src = [$src];
            }

            $html = '';
            foreach ($src as $item) {
                $html .= $this->htmlBuilder->script($item . '?v=' . ($version ? $this->build : ''), ['class' => 'hidden'])->toHtml();
            }

            return $html;
        }

        return null;
    }

    /**
     * @param $name
     * @param bool $version
     * @author Sang Nguyen
     */
    public function getStylesheetItemToHtml($name, $version = true)
    {
        $config = 'core.assets.general.resources.stylesheets.' . $name;
        if ($this->config->has($config)) {

            $src = $this->config->get($config . '.src.local');
            if ($this->config->get($config . '.use_cdn') && !$this->config->get('core.assets.general.offline')) {
                $src = $this->config->get($config . '.src.cdn');
            }

            if (!is_array($src)) {
                $src = [$src];
            }

            $html = '';
            foreach ($src as $item) {
                $html .= $this->htmlBuilder->style($item . '?v=' . ($version ? $this->build : ''), ['class' => 'hidden'])->toHtml();
            }

            return $html;
        }

        return null;
    }

    /**
     * @param $module
     * @return null|string
     */
    public function getAppModuleItemToHtml($module)
    {
        $src = $this->getModule($module, true);

        if (!$src) {
            return null;
        }

        return $this->htmlBuilder->script($src, ['class' => 'hidden'])->toHtml();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function renderHeader()
    {
        do_action(BASE_ACTION_ENQUEUE_SCRIPTS);

        $stylesheets = $this->getStylesheets(['core']);
        $headScripts = $this->getJavascript('top');
        return view('core.assets::header', compact('stylesheets', 'headScripts'))->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function renderFooter()
    {
        $bodyScripts = array_merge($this->getJavascript('bottom'), $this->getAppModules());
        return view('core.assets::footer', compact('bodyScripts'))->render();
    }
}
