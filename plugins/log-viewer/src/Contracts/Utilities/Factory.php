<?php

namespace Botble\LogViewer\Contracts\Utilities;

use Botble\LogViewer\Contracts\Patternable;

interface Factory extends Patternable
{
    /**
     * Get the filesystem instance.
     *
     * @return \Botble\LogViewer\Contracts\Utilities\Filesystem
     * @author ARCANEDEV
     */
    public function getFilesystem();

    /**
     * Set the filesystem instance.
     *
     * @param  \Botble\LogViewer\Contracts\Utilities\Filesystem $filesystem
     *
     * @return self
     * @author ARCANEDEV
     */
    public function setFilesystem(Filesystem $filesystem);

    /**
     * Get the log levels instance.
     *
     * @return  \Botble\LogViewer\Contracts\Utilities\LogLevels  $levels
     * @author ARCANEDEV
     */
    public function getLevels();

    /**
     * Set the log levels instance.
     *
     * @param  \Botble\LogViewer\Contracts\Utilities\LogLevels $levels
     *
     * @return self
     * @author ARCANEDEV
     */
    public function setLevels(LogLevels $levels);

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
     * Get all logs.
     *
     * @return \Botble\LogViewer\Entities\LogCollection
     * @author ARCANEDEV
     */
    public function logs();

    /**
     * Get all logs (alias).
     *
     * @return \Botble\LogViewer\Entities\LogCollection
     * @author ARCANEDEV
     */
    public function all();

    /**
     * Paginate all logs.
     *
     * @param  int $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @author ARCANEDEV
     */
    public function paginate($perPage = 30);

    /**
     * Get a log by date.
     *
     * @param  string $date
     *
     * @return \Botble\LogViewer\Entities\Log
     * @author ARCANEDEV
     */
    public function log($date);

    /**
     * Get a log by date (alias).
     *
     * @param  string $date
     *
     * @return \Botble\LogViewer\Entities\Log
     * @author ARCANEDEV
     */
    public function get($date);

    /**
     * Get log entries.
     *
     * @param  string $date
     * @param  string $level
     *
     * @return \Botble\LogViewer\Entities\LogEntryCollection
     * @author ARCANEDEV
     */
    public function entries($date, $level = 'all');

    /**
     * List the log files (dates).
     *
     * @return array
     * @author ARCANEDEV
     */
    public function dates();

    /**
     * Get logs count.
     *
     * @return int
     * @author ARCANEDEV
     */
    public function count();

    /**
     * Get total log entries.
     *
     * @param  string $level
     *
     * @return int
     * @author ARCANEDEV
     */
    public function total($level = 'all');

    /**
     * Get tree menu.
     *
     * @param  bool $trans
     *
     * @return array
     * @author ARCANEDEV
     */
    public function tree($trans = false);

    /**
     * Get tree menu.
     *
     * @param  bool $trans
     *
     * @return array
     * @author ARCANEDEV
     */
    public function menu($trans = true);

    /**
     * Get logs statistics.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function stats();

    /**
     * Get logs statistics table.
     *
     * @param  string|null $locale
     *
     * @return \Botble\LogViewer\Tables\StatsTable
     * @author ARCANEDEV
     */
    public function statsTable($locale = null);

    /**
     * Determine if the log folder is empty or not.
     *
     * @return bool
     * @author ARCANEDEV
     */
    public function isEmpty();
}
