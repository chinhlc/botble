<?php

namespace Botble\Media\Commands;

use Botble\Media\Repositories\Eloquent\MediaFileRepository;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\Media\Services\UploadsManager;
use Exception;
use File;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Storage;

class DeleteThumbnailCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:media:thumbnail:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete thumbnails for all images';

    /**
     * @var MediaFileRepository
     */
    protected $fileRepository;

    /**
     * @var UploadsManager
     */
    protected $uploadManager;

    /**
     * DeleteThumbnailCommand constructor.
     * @param MediaFileInterface $fileRepository
     * @param UploadsManager $uploadManager
     */
    public function __construct(
        MediaFileInterface $fileRepository,
        UploadsManager $uploadManager
    ) {
        parent::__construct();
        $this->fileRepository = $fileRepository;
        $this->uploadManager = $uploadManager;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $errors = [];

        $files = $this->fileRepository->allBy([], [], ['url', 'mime_type']);

        $this->info('Processing ' . $files->count() . ' file(s)...');

        foreach ($files as $file) {
            if (!is_image($file->mime_type) || in_array($file->mime_type, ['image/svg+xml', 'image/x-icon']) || !Storage::exists($file->url)) {
                continue;
            }

            $folderPath = File::dirname($file->url);

            $fileExtension = File::extension($file->url);

            $fileName = File::name($file->url);

            foreach (config('media.sizes', []) as $size) {
                $filePath = $folderPath . '/' . $fileName . '-' . $size . '.' . $fileExtension;
                $this->info('Processing ' . $filePath);

                try {
                    $this->uploadManager->deleteFile($filePath);
                } catch (Exception $exception) {
                    $errors[] = $filePath;
                    $this->error($exception->getMessage());
                }
            }
        }

        $errors = array_unique($errors);

        $errors = array_map(function ($item) {
            return [$item];
        }, $errors);

        $this->info('Thumbnails deleted');

        if ($errors) {
            $this->info('We are unable to regenerate thumbnail for these files:');

            $this->table(['File directory'], $errors);
        }

        return true;
    }
}
