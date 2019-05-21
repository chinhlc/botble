<?php

namespace Botble\LogViewer\Entities;

use Botble\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Botble\LogViewer\Contracts\Utilities\Filesystem;
use Botble\LogViewer\Exceptions\LogNotFoundException;
use Botble\Base\Supports\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LogCollection extends Collection
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * LogCollection constructor.
     *
     * @param  array $items
     * @author ARCANEDEV
     */
    public function __construct($items = [])
    {
        $this->setFilesystem(app('botble::log-viewer.filesystem'));
        parent::__construct($items);

        if (empty($items)) {
            $this->load();
        }
    }

    /**
     * Set the filesystem instance.
     *
     * @param  \Botble\LogViewer\Contracts\Utilities\Filesystem $filesystem
     *
     * @return \Botble\LogViewer\Entities\LogCollection
     * @author ARCANEDEV
     */
    public function setFilesystem(FilesystemContract $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Load all logs.
     *
     * @return \Botble\LogViewer\Entities\LogCollection
     * @author ARCANEDEV
     */
    private function load()
    {
        foreach ($this->filesystem->dates(true) as $date => $path) {
            $raw = $this->filesystem->read($date);

            $this->put($date, Log::make($date, $path, $raw));
        }

        return $this;
    }

    /**
     * Get a log.
     *
     * @param  string $date
     * @param  mixed|null $default
     *
     * @return \Botble\LogViewer\Entities\Log
     *
     * @throws \Botble\LogViewer\Exceptions\LogNotFoundException
     * @author ARCANEDEV
     */
    public function get($date, $default = null)
    {
        if (!$this->has($date)) {
            throw new LogNotFoundException('Log not found in this date [' . $date . ']');
        }

        return parent::get($date, $default);
    }

    /**
     * Paginate logs.
     *
     * @param  int $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @author ARCANEDEV
     */
    public function paginate($perPage = 30)
    {
        $request = request();
        $currentPage = $request->input('page', 1);
        $paginator = new LengthAwarePaginator(
            $this->slice(($currentPage * $perPage) - $perPage, $perPage),
            $this->count(),
            $perPage,
            $currentPage
        );

        return $paginator->setPath($request->url());
    }

    /**
     * Get a log (alias).
     *
     * @see get()
     *
     * @param  string $date
     *
     * @return \Botble\LogViewer\Entities\Log
     * @author ARCANEDEV
     */
    public function log($date)
    {
        return $this->get($date);
    }


    /**
     * Get log entries.
     *
     * @param  string $date
     * @param  string $level
     *
     * @return \Botble\LogViewer\Entities\LogEntryCollection
     * @author ARCANEDEV
     */
    public function entries($date, $level = 'all')
    {
        return $this->get($date)->entries($level);
    }

    /**
     * Get logs statistics.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function stats()
    {
        $stats = [];

        foreach ($this->items as $date => $log) {
            $stats[$date] = $log->stats();
        }

        return $stats;
    }

    /**
     * List the log files (dates).
     *
     * @return array
     * @author ARCANEDEV
     */
    public function dates()
    {
        return $this->keys()->toArray();
    }

    /**
     * Get entries total.
     *
     * @param  string $level
     *
     * @return int
     * @author ARCANEDEV
     */
    public function total($level = 'all')
    {
        return (int)$this->sum(function (Log $log) use ($level) {
            return $log->entries($level)->count();
        });
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
        $tree = [];

        foreach ($this->items as $date => $log) {
            $tree[$date] = $log->tree($trans);
        }

        return $tree;
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
        $menu = [];

        foreach ($this->items as $date => $log) {
            $menu[$date] = $log->menu($trans);
        }

        return $menu;
    }
}
