<?php

namespace Botble\Media\Supports;

/**
 * RepositoryInterface that needs to be implemented by every Repository
 *
 * Class RepositoryInterface
 */
interface ZipperInterface
{
    /**
     * Construct with a given path
     *
     * @param $filePath
     * @param bool $new
     * @param $archiveImplementation
     */
    public function __construct($filePath, $new = false, $archiveImplementation = null);

    /**
     * Add a file to the opened Archive
     *
     * @param $pathToFile
     * @param $pathInArchive
     */
    public function addFile($pathToFile, $pathInArchive);

    /**
     * Add a file to the opened Archive using its contents
     *
     * @param $name
     * @param $content
     */
    public function addFromString($name, $content);

    /**
     * Closes the archive and saves it
     */
    public function close();
}
