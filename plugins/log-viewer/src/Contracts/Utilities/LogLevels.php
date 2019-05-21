<?php

namespace Botble\LogViewer\Contracts\Utilities;

use Illuminate\Translation\Translator;

interface LogLevels
{

    /**
     * Set the Translator instance.
     *
     * @param  \Illuminate\Translation\Translator $translator
     *
     * @return self
     * @author ARCANEDEV
     */
    public function setTranslator(Translator $translator);

    /**
     * Get the selected locale.
     *
     * @return string
     * @author ARCANEDEV
     */
    public function getLocale();

    /**
     * Set the selected locale.
     *
     * @param  string $locale
     *
     * @return self
     * @author ARCANEDEV
     */
    public function setLocale($locale);

    /**
     * Get the log levels.
     *
     * @param  bool $flip
     *
     * @return array
     * @author ARCANEDEV
     */
    public function lists($flip = false);

    /**
     * Get translated levels.
     *
     * @param  string|null $locale
     *
     * @return array
     * @author ARCANEDEV
     */
    public function names($locale = null);

    /**
     * Get PSR log levels.
     *
     * @param  bool $flip
     *
     * @return array
     * @author ARCANEDEV
     */
    public static function all($flip = false);

    /**
     * Get the translated level.
     *
     * @param  string $key
     * @param  string|null $locale
     *
     * @return string
     * @author ARCANEDEV
     */
    public function get($key, $locale = null);
}
