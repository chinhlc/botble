<?php

namespace Botble\LogViewer\Bases;

use Botble\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Botble\LogViewer\Contracts\Table as TableContract;

/**
 * Class     Table
 *
 * @package  Botble\LogViewer\Bases
 * @author   ARCANEDEV
 */
abstract class Table implements TableContract
{
    /**
     * @var array
     */
    protected $header = [];

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var array
     */
    protected $footer = [];

    /**
     * @var string
     */
    protected $levels;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Create a table instance.
     *
     * @param  array $data
     * @param  \Botble\LogViewer\Contracts\Utilities\LogLevels $levels
     * @param  string|null $locale
     * @author ARCANEDEV
     */
    public function __construct(array $data, LogLevelsContract $levels, $locale = null)
    {
        $this->setLevels($levels);
        $this->setLocale(empty($locale) ? config('plugins.log-viewer.general.locale') : $locale);
        $this->setData($data);
        $this->init();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Set LogLevels instance.
     *
     * @param  \Botble\LogViewer\Contracts\Utilities\LogLevels $levels
     *
     * @return \Botble\LogViewer\Bases\Table
     * @author ARCANEDEV
     */
    protected function setLevels(LogLevelsContract $levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Set table locale.
     *
     * @param  string|null $locale
     *
     * @return \Botble\LogViewer\Bases\Table
     * @author ARCANEDEV
     */
    protected function setLocale($locale)
    {
        if (empty($locale) || $locale === 'auto') {
            $locale = app()->getLocale();
        }

        $this->locale = $locale;

        return $this;
    }

    /**
     * Get table header.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function header()
    {
        return $this->header;
    }

    /**
     * Get table rows.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function rows()
    {
        return $this->rows;
    }

    /**
     * Get table footer.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function footer()
    {
        return $this->footer;
    }

    /**
     * Get raw data.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Set table data.
     *
     * @param  array $data
     *
     * @return self
     * @author ARCANEDEV
     */
    private function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Prepare the table.
     * @author ARCANEDEV
     */
    private function init()
    {
        $this->header = $this->prepareHeader($this->data);
        $this->rows = $this->prepareRows($this->data);
        $this->footer = $this->prepareFooter($this->data);
    }

    /**
     * Prepare table header.
     *
     * @param  array $data
     *
     * @return array
     * @author ARCANEDEV
     */
    abstract protected function prepareHeader(array $data);

    /**
     * Prepare table rows.
     *
     * @param  array $data
     *
     * @return array
     * @author ARCANEDEV
     */
    abstract protected function prepareRows(array $data);

    /**
     * Prepare table footer.
     *
     * @param  array $data
     *
     * @return array
     * @author ARCANEDEV
     */
    abstract protected function prepareFooter(array $data);

    /**
     * Translate.
     *
     * @param  string $key
     *
     * @return string
     * @author ARCANEDEV
     */
    protected function translate($key)
    {
        /** @var \Illuminate\Translation\Translator $translator */
        $translator = trans();

        return $translator->get('plugins.log-viewer::' . $key, [], $this->locale);
    }

    /**
     * Get log level color.
     *
     * @param  string $level
     *
     * @return string
     * @author ARCANEDEV
     */
    protected function color($level)
    {
        return log_styler()->color($level);
    }
}
