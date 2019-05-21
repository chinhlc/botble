<?php

namespace Botble\LogViewer\Contracts;

use Botble\LogViewer\Contracts\Utilities\Filesystem;

interface Patternable
{
    /**
     * Get the log pattern.
     *
     * @return string
     * @author ARCANEDEV
     */
    public function getPattern();

    /**
     * Set the log pattern.
     *
     * @param  string $date
     * @param  string $prefix
     * @param  string $extension
     *
     * @return self
     * @author ARCANEDEV
     */
    public function setPattern(
        $prefix = Filesystem::PATTERN_PREFIX,
        $date = Filesystem::PATTERN_DATE,
        $extension = Filesystem::PATTERN_EXTENSION
    );
}
