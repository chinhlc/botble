<?php

namespace Botble\LogViewer\Utilities;

use Botble\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Botble\LogViewer\Contracts\Utilities\LogChecker as LogCheckerContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class LogChecker implements LogCheckerContract
{
    /**
     * Log handler mode.
     *
     * @var string
     */
    protected $handler = '';

    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The filesystem instance.
     *
     * @var \Botble\LogViewer\Contracts\Utilities\Filesystem
     */
    protected $filesystem;

    /**
     * The check status.
     *
     * @var bool
     */
    protected $status = true;

    /**
     * The check messages.
     *
     * @var array
     */
    protected $messages;

    /**
     * Log files statuses.
     *
     * @var array
     */
    protected $files;

    /**
     * LogChecker constructor.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     * @param  \Botble\LogViewer\Contracts\Utilities\Filesystem $filesystem
     * @author ARCANEDEV
     */
    public function __construct(ConfigContract $config, FilesystemContract $filesystem)
    {
        $this->files = [];
        $this->setConfig($config);
        $this->setFilesystem($filesystem);
        $this->refresh();
    }

    /**
     * Set the config instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     *
     * @return \Botble\LogViewer\Utilities\LogChecker
     * @author ARCANEDEV
     */
    public function setConfig(ConfigContract $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set the Filesystem instance.
     *
     * @param  \Botble\LogViewer\Contracts\Utilities\Filesystem $filesystem
     *
     * @return \Botble\LogViewer\Utilities\LogChecker
     * @author ARCANEDEV
     */
    public function setFilesystem(FilesystemContract $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Refresh the checks.
     *
     * @return \Botble\LogViewer\Utilities\LogChecker
     * @author ARCANEDEV
     */
    private function refresh()
    {
        $this->setHandler($this->config->get('app.log', 'single'));

        $this->messages = [
            'handler' => '',
            'files' => [],
        ];
        $this->files = [];

        $this->checkHandler();
        $this->checkLogFiles();

        return $this;
    }

    /**
     * Set the log handler mode.
     *
     * @param  string $handler
     *
     * @return \Botble\LogViewer\Utilities\LogChecker
     * @author ARCANEDEV
     */
    protected function setHandler($handler)
    {
        $this->handler = strtolower($handler);

        return $this;
    }

    /**
     * Check the handler mode.
     * @author ARCANEDEV
     */
    private function checkHandler()
    {
        if ($this->isDaily()) {
            return;
        }

        $this->messages['handler'] = 'You should set the log handler to `daily` mode. Please check the LogViewer wiki page (Requirements) for more details.';
    }

    /**
     * Is a daily handler mode ?
     *
     * @return bool
     * @author ARCANEDEV
     */
    protected function isDaily()
    {
        return $this->isSameHandler(self::HANDLER_DAILY);
    }

    /**
     * Is the handler is the same as the application log handler.
     *
     * @param  string $handler
     *
     * @return bool
     * @author ARCANEDEV
     */
    protected function isSameHandler($handler)
    {
        return $this->handler === $handler;
    }

    /**
     * Check all log files.
     * @author ARCANEDEV
     */
    protected function checkLogFiles()
    {
        foreach ($this->filesystem->all() as $path) {
            $this->checkLogFile($path);
        }
    }

    /**
     * Check a log file.
     *
     * @param  string $path
     * @author ARCANEDEV
     */
    protected function checkLogFile($path)
    {
        $status = true;
        $file = basename($path);
        $message = 'The log file [' . $file . '] is valid.';

        if ($this->isSingleLogFile($file)) {
            $this->status = $status = false;
            $this->messages['files'][$file] = $message =
                'You have a single log file in your application, you should split the [' . $file . '] into seperate log files.';
        } elseif ($this->isInvalidLogDate($file)) {
            $this->status = $status = false;
            $this->messages['files'][$file] = $message =
                'The log file [' . $file . '] has an invalid date, the format must be like laravel-YYYY-MM-DD.log.';
        }

        $this->files[$file] = compact('status', 'message', 'path');
    }

    /**
     * Check if it's not a single log file.
     *
     * @param  string $file
     *
     * @return bool
     * @author ARCANEDEV
     */
    protected function isSingleLogFile($file)
    {
        return $file === 'laravel.log';
    }

    /**
     * Check the date of the log file.
     *
     * @param  string $file
     *
     * @return bool
     * @author ARCANEDEV
     */
    protected function isInvalidLogDate($file)
    {
        $pattern = '/laravel-(\d){4}-(\d){2}-(\d){2}.log/';

        if ((bool)preg_match($pattern, $file) === false) {
            return true;
        }

        return false;
    }

    /**
     * Get messages.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function messages()
    {
        $this->refresh();

        return $this->messages;
    }

    /**
     * Check if the checker fails.
     *
     * @return bool
     * @author ARCANEDEV
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * Check if the checker passes.
     *
     * @return bool
     * @author ARCANEDEV
     */
    public function passes()
    {
        $this->refresh();

        return $this->status;
    }

    /**
     * Get the requirements.
     *
     * @return array
     * @author ARCANEDEV
     */
    public function requirements()
    {
        $this->refresh();

        return $this->isDaily() ? [
            'status' => 'success',
            'header' => 'Application requirements fulfilled.',
            'message' => 'Are you ready to rock ?',
        ] : [
            'status' => 'failed',
            'header' => 'Application requirements failed.',
            'message' => $this->messages['handler']
        ];
    }
}
