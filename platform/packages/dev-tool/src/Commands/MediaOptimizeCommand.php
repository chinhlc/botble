<?php

namespace Botble\DevTool\Commands;

use Botble\Media\Repositories\Eloquent\MediaFileRepository;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Exception;
use File;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Spatie\ImageOptimizer\OptimizerChain;
use Storage;

class MediaOptimizeCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:media:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize media images';

    /**
     * @var MediaFileRepository
     */
    protected $fileRepository;

    /**
     * @var OptimizerChain
     */
    protected $optimizer;

    /**
     * MediaOptimizeCommand constructor.
     * @param MediaFileInterface $fileRepository
     * @param OptimizerChain $optimizer
     */
    public function __construct(
        MediaFileInterface $fileRepository,
        OptimizerChain $optimizer
    ) {
        parent::__construct();
        $this->fileRepository = $fileRepository;
        $this->optimizer = $optimizer;
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
            if (!is_image($file->mime_type) || $file->mime_type === 'image/svg+xml' || !Storage::exists($file->url)) {
                continue;
            }

            $folderPath = File::dirname($file->url);

            $fileExtension = File::extension($file->url);

            $fileName = File::name($file->url);

            foreach (config('media.sizes', []) as $size) {
                $filePath = $folderPath . '/' . $fileName . '-' . $size . '.' . $fileExtension;
                $this->info('Processing ' . $filePath);

                try {
                    $this->optimizer->optimize(Storage::path($filePath));
                    $file->size = Storage::size($filePath);
                    $file->save();
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

        $this->info('Optimized ' . $files->count() * count(config('media.sizes', [])) . ' image(s)');

        if ($errors) {
            $this->info('We are unable to optimize for these images:');

            $this->table(['File directory'], $errors);
        }

        return true;
    }
}
