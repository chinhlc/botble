<?php

namespace Botble\LogViewer\Contracts\Utilities;

use Botble\LogViewer\Contracts\Patternable;

interface Filesystem extends Patternable
{
    const PATTERN_PREFIX = 'laravel-';
    const PATTERN_DATE = '[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]';
    const PATTERN_EXTENSION = '.log';

    /**
     * Get the files instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     * @author ARCANEDEV
     */
    public function getInstance();

    /**
     * Set the log storage path.
     *
     * @param  string $storagePath
     *
     * @return self
     * @author ARCANEDEV
     */
    public function setPath($storagePath);

    /**
     * Set the log date pattern.
     *
     * @param  string $datePattern
     *
     * @return self
     * @author ARCANEDEV
     */
    public function setDatePattern($datePattern);

    /**
     * Set the log prefix pattern.
     *
     * @param  string $prefixPattern
     *
     * @return self
     * @author ARCANEDEV
     */
    public function setPrefixPattern($prefixPattern);

    /**
     * Set the log extension.
     *
     * @param  string $extension
     *
     * @return self
     * @author ARCANEDEV
     */
    public function setExtension($extension);

    /**
     * Get all log files.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function all();

    /**
     * Get all valid log files.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function logs();

    /**
     * List the log files (Only dates).
     *
     * @param  bool $withPaths
     *
     * @return array
     * @author ARCANEDEV
     */
    public function dates($withPaths = false);

    /**
     * Read the log.
     *
     * @param  string $date
     *
     * @return string
     *
     * @throws \Botble\LogViewer\Exceptions\FilesystemException
     * @author ARCANEDEV
     */
    public function read($date);

    /**
     * Delete the log.
     *
     * @param  string $date
     *
     * @return bool
     *
     * @throws \Botble\LogViewer\Exceptions\FilesystemException
     * @author ARCANEDEV
     */
    public function delete($date);

    /**
     * Get the log file path.
     *
     * @param  string $date
     *
     * @return string
     * @author ARCANEDEV
     */
    public function path($date);
}
