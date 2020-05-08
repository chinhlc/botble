<?php

namespace Botble\Media\Services;

use Carbon\Carbon;
use File;
use Mimey\MimeTypes;
use Storage;
use Symfony\Component\Translation\TranslatorInterface;

class UploadsManager
{
    /**
     * @var MimeTypes
     */
    protected $mimeType;

    public function __construct()
    {
        $this->mimeType = new MimeTypes;
    }

    /**
     * Return an array of file details for a file
     *
     * @param $path
     * @return array
     */
    public function fileDetails($path)
    {
        return [
            'filename'  => basename($path),
            'url'       => $path,
            'mime_type' => $this->fileMimeType($path),
            'size'      => $this->fileSize($path),
            'modified'  => $this->fileModified($path),
        ];
    }

    /**
     * Return the mime type
     *
     * @param $path
     * @return mixed|null|string
     */
    public function fileMimeType($path): ?string
    {
        return $this->mimeType->getMimeType(File::extension(Storage::path($path)));
    }

    /**
     * Return the file size
     *
     * @param $path
     * @return int
     */
    public function fileSize($path)
    {
        return Storage::size($path);
    }

    /**
     * Return the last modified time
     *
     * @param $path
     * @return string
     */
    public function fileModified($path)
    {
        return Carbon::createFromTimestamp(Storage::lastModified($path));
    }

    /**
     * Create a new directory
     *
     * @param $folder
     * @return bool|string|TranslatorInterface
     */
    public function createDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);

        if (Storage::exists($folder)) {
            return trans('media::media.folder_exists', ['folder' => $folder]);
        }

        return Storage::makeDirectory($folder);
    }

    /**
     * Sanitize the folder name
     *
     * @param $folder
     * @return string
     */
    protected function cleanFolder($folder)
    {
        return DIRECTORY_SEPARATOR . trim(str_replace('..', '', $folder), DIRECTORY_SEPARATOR);
    }

    /**
     * Delete a directory
     *
     * @param $folder
     * @return bool|string|TranslatorInterface
     */
    public function deleteDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);

        $filesFolders = array_merge(
            Storage::directories($folder),
            Storage::files($folder)
        );
        if (!empty($filesFolders)) {
            return trans('media::media.directory_must_empty');
        }

        return Storage::deleteDirectory($folder);
    }

    /**
     * Delete a file
     *
     * @param $path
     * @return bool|string|TranslatorInterface
     */
    public function deleteFile($path)
    {
        $path = $this->cleanFolder($path);

        if (is_image($this->fileMimeType($path))) {
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $files = [$path];
            foreach (config('media.sizes') as $size) {
                $files[] = str_replace($filename, $filename . '-' . $size, $path);
            }

            return Storage::delete($files);
        }

        return Storage::delete([$path]);
    }

    /**
     * Save a file
     *
     * @param $path
     * @param $content
     * @return bool|string|TranslatorInterface
     */
    public function saveFile($path, $content)
    {
        $path = $this->cleanFolder($path);

        return Storage::put($path, $content);
    }
}
