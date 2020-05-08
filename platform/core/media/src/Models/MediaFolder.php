<?php

namespace Botble\Media\Models;

use Botble\Media\Services\UploadsManager;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaFolder extends BaseModel
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'media_folders';

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'user_id',
    ];

    /**
     * @return HasMany
     */
    public function files()
    {
        return $this->hasMany(MediaFile::class, 'folder_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function parentFolder()
    {
        return $this->hasOne(MediaFolder::class, 'id', 'parent');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (MediaFolder $folder) {
            if ($folder->isForceDeleting()) {
                $files = MediaFile::where('folder_id', '=', $folder->id)->onlyTrashed()->get();

                $uploadManager = new UploadsManager;

                foreach ($files as $file) {
                    /**
                     * @var MediaFile $file
                     */
                    $uploadManager->deleteFile($file->url);
                    $file->forceDelete();
                }
            } else {
                $files = MediaFile::where('folder_id', '=', $folder->id)->withTrashed()->get();

                foreach ($files as $file) {
                    /**
                     * @var MediaFile $file
                     */
                    $file->delete();
                }
            }
        });

        static::restoring(function ($folder) {
            MediaFile::where('folder_id', '=', $folder->id)->restore();
        });
    }
}
