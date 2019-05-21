<?php

namespace Botble\LogViewer\Entities;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use SplFileInfo;

class Log implements Arrayable, Jsonable, JsonSerializable
{

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var LogEntryCollection
     */
    protected $entries;

    /**
     * @var SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $raw;

    /**
     * Log constructor.
     *
     * @param  string $date
     * @param  string $path
     * @param  string $raw
     * @author ARCANEDEV
     */
    public function __construct($date, $path, $raw)
    {
        $this->entries = new LogEntryCollection;
        $this->date = $date;
        $this->path = $path;
        $this->file = new SplFileInfo($path);
        $this->raw = $raw;

        $this->entries->load($raw);
    }

    /**
     * Get log path.
     *
     * @return string
     * @author ARCANEDEV
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get raw log content.
     *
     * @return string
     * @author ARCANEDEV
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Get file info.
     *
     * @return \SplFileInfo
     * @author ARCANEDEV
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * Get file size.
     *
     * @return string
     * @author ARCANEDEV
     */
    public function size()
    {
        return $this->formatSize($this->file->getSize());
    }

    /**
     * Get file creation date.
     *
     * @return \Carbon\Carbon
     * @author ARCANEDEV
     */
    public function createdAt()
    {
        return Carbon::createFromTimestamp($this->file()->getATime());
    }

    /**
     * Get file modification date.
     *
     * @return \Carbon\Carbon
     * @author ARCANEDEV
     */
    public function updatedAt()
    {
        return Carbon::createFromTimestamp($this->file()->getMTime());
    }

    /**
     * Make a log object.
     *
     * @param  string $date
     * @param  string $path
     * @param  string $raw
     *
     * @return self
     * @author ARCANEDEV
     */
    public static function make($date, $path, $raw)
    {
        return new self($date, $path, $raw);
    }

    /**
     * Get log entries.
     *
     * @param  string $level
     *
     * @return \Botble\LogViewer\Entities\LogEntryCollection
     * @author ARCANEDEV
     */
    public function entries($level = 'all')
    {
        if ($level === 'all') {
            return $this->entries;
        }

        return $this->getByLevel($level);
    }

    /**
     * Get filtered log entries by level.
     *
     * @param  string $level
     *
     * @return \Botble\LogViewer\Entities\LogEntryCollection
     * @author ARCANEDEV
     */
    public function getByLevel($level)
    {
        return $this->entries->filterByLevel($level);
    }

    /**
     * Get log stats.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function stats()
    {
        return $this->entries->stats();
    }

    /**
     * Get the log navigation tree.
     *
     * @param  bool $trans
     *
     * @return array
     * @author ARCANEDEV
     */
    public function tree($trans = false)
    {
        return $this->entries->tree($trans);
    }

    /**
     * Get log entries menu.
     *
     * @param  bool $trans
     *
     * @return array
     * @author ARCANEDEV
     */
    public function menu($trans = true)
    {
        return log_menu()->make($this, $trans);
    }

    /**
     * Get the log as a plain array.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function toArray()
    {
        return [
            'date' => $this->date,
            'path' => $this->path,
            'entries' => $this->entries->toArray()
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     * @author ARCANEDEV
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Serialize the log object to json data.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Format the file size.
     *
     * @param  int $bytes
     * @param  int $precision
     *
     * @return string
     * @author ARCANEDEV
     */
    protected function formatSize($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
