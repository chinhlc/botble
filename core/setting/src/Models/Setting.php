<?php

namespace Botble\Setting\Models;

use Eloquent;

/**
 * Botble\Setting\Models\SettingModel
 *
 * @mixin \Eloquent
 */
class Setting extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
