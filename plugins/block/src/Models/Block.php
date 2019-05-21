<?php

namespace Botble\Block\Models;

use Eloquent;

/**
 * Botble\Block\Models\Block
 *
 * @mixin \Eloquent
 */
class Block extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'blocks';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'content',
        'status',
    ];
}
