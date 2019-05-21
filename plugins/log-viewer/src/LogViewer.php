<?php

namespace Botble\LogViewer;

use Botble\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Botble\LogViewer\Contracts\Utilities\Factory as FactoryContract;
use Botble\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Botble\LogViewer\Contracts\LogViewer as LogViewerContract;

class LogViewer implements LogViewerContract
{

    /**
     * The factory instance.
     *
     * @var \Botble\LogViewer\Contracts\Utilities\Factory
     */
    protected $factory;

    /**
     * The filesystem instance.
     *
     * @var \Botble\LogViewer\Contracts\Utilities\Filesystem
     */
    protected $filesystem;

    /**
     * The log levels instance.
     *
     * @var \Botble\LogViewer\Contracts\Utilities\LogLevels
     */
    protected $levels;

    /**
     * Create a new instance.
     *
     * @param  \Botble\LogViewer\Contracts\Utilities\Factory $factory
     * @param  \Botble\LogViewer\Contracts\Utilities\Filesystem $filesystem
     * @param  \Botble\LogViewer\Contracts\Utilities\LogLevels $levels
     * @author ARCANEDEV
     */
    public function __construct(
        FactoryContract $factory,
        FilesystemContract $filesystem,
        LogLevelsContract $levels
    )
    {
        $this->factory = $factory;
        $this->filesystem = $filesystem;
        $this->levels = $levels;
    }

    /**
     * Get the log levels.
     *
     * @param  bool $flip
     *
     * @return array
     * @author ARCANEDEV
     */
    public function levels($flip = false)
    {
        return $this->levels->lists($flip);
    }

    /**
     * Get the translated log levels.
     *
     * @param  string|null $locale
     *
     * @return array
     * @author ARCANEDEV
     */
    public function levelsNames($locale = null)
    {
        return $this->levels->names($locale);
    }

    /**
     * Set the log storage path.
     *
     * @param  string $path
     *
     * @return \Botble\LogViewer\LogViewer
     * @author ARCANEDEV
     */
    public function setPath($path)
    {
        $this->factory->setPath($path);

        return $this;
    }

    /**
     * Get the log pattern.
     *
     * @return string
     * @author ARCANEDEV
     */
    public function getPattern()
    {
        return $this->factory->getPattern();
    }

    /**
     * Set the log pattern.
     *
     * @param  string $date
     * @param  string $prefix
     * @param  string $extension
     *
     * @return \Botble\LogViewer\LogViewer
     * @author ARCANEDEV
     */
    public function setPattern(
        $prefix = FilesystemContract::PATTERN_PREFIX,
        $date = FilesystemContract::PATTERN_DATE,
        $extension = FilesystemContract::PATTERN_EXTENSION
    )
    {
        $this->factory->setPattern($prefix, $date, $extension);

        return $this;
    }

    /**
     * Get all logs.
     *
     * @return \Botble\LogViewer\Entities\LogCollection
     * @author ARCANEDEV
     */
    public function all()
    {
        return $this->factory->all();
    }

    /**
     * Paginate all logs.
     *
     * @param  int $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @author ARCANEDEV
     */
    public function paginate($perPage = 30)
    {
        return $this->factory->paginate($perPage);
    }

    /**
     * Get a log.
     *
     * @param  string $date
     *
     * @return \Botble\LogViewer\Entities\Log
     * @author ARCANEDEV
     */
    public function get($date)
    {
        return $this->factory->log($date);
    }

    /**
     * Get the log entries.
     *
     * @param  string $date
     * @param  string $level
     *
     * @return \Botble\LogViewer\Entities\LogEntryCollection
     * @author ARCANEDEV
     */
    public function entries($date, $level = 'all')
    {
        return $this->factory->entries($date, $level);
    }

    /**
     * Download a log file.
     *
     * @param  string $date
     * @param  string|null $filename
     * @param  array $headers
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @author ARCANEDEV
     */
    public function download($date, $filename = null, $headers = [])
    {
        if (empty($filename)) {
            $filename = 'laravel-' . $date . '.log';
        }

        $path = $this->filesystem->path($date);

        return response()->download($path, $filename, $headers);
    }

    /**
     * Get logs statistics.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function stats()
    {
        return $this->factory->stats();
    }

    /**
     * Get logs statistics table.
     *
     * @param  string|null $locale
     *
     * @return \Botble\LogViewer\Tables\StatsTable
     * @author ARCANEDEV
     */
    public function statsTable($locale = null)
    {
        return $this->factory->statsTable($locale);
    }

    /**
     * Delete the log.
     *
     * @param  string $date
     *
     * @return bool
     * @author ARCANEDEV
     * @throws Exceptions\FilesystemException
     */
    public function delete($date)
    {
        return $this->filesystem->delete($date);
    }

    /**
     * Get all valid log files.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function files()
    {
        return $this->filesystem->logs();
    }

    /**
     * List the log files (only dates).
     *
     * @return array
     * @author ARCANEDEV
     */
    public function dates()
    {
        return $this->factory->dates();
    }

    /**
     * Get logs count.
     *
     * @return int
     * @author ARCANEDEV
     */
    public function count()
    {
        return $this->factory->count();
    }

    /**
     * Get entries total from all logs.
     *
     * @param  string $level
     *
     * @return int
     * @author ARCANEDEV
     */
    public function total($level = 'all')
    {
        return $this->factory->total($level);
    }

    /**
     * Get logs tree.
     *
     * @param  bool $trans
     *
     * @return array
     * @author ARCANEDEV
     */
    public function tree($trans = false)
    {
        return $this->factory->tree($trans);
    }

    /**
     * Get logs menu.
     *
     * @param  bool $trans
     *
     * @return array
     * @author ARCANEDEV
     */
    public function menu($trans = true)
    {
        return $this->factory->menu($trans);
    }

    /**
     * Determine if the log folder is empty or not.
     *
     * @return bool
     * @author ARCANEDEV
     */
    public function isEmpty()
    {
        return $this->factory->isEmpty();
    }
}
