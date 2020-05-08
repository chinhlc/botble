<?php

namespace Botble\Menu\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuNode extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'menu_nodes';

    /**
     * @var array
     */
    protected $fillable = [
        'menu_id',
        'parent_id',
        'reference_id',
        'reference_type',
        'url',
        'icon_font',
        'title',
        'css_class',
        'target',
        'has_child',
    ];

    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(MenuNode::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function child()
    {
        return $this->hasMany(MenuNode::class, 'parent_id');
    }

    /**
     * @return BelongsTo
     */
    public function reference()
    {
        return $this->morphTo()->with(['slugable']);
    }

    /**
     * @param $value
     * @return string
     */
    public function getUrlAttribute($value)
    {
        if (!$this->reference_type) {
            return $value ? url($value) : url('');
        }

        if (!$this->reference) {
            return url('');
        }

        $prefix = apply_filters(FILTER_SLUG_PREFIX, $this->reference->slugable->prefix);

        return url($prefix ? $prefix . '/' . $this->reference->slug : $this->reference->slug);
    }

    /**
     * @param $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->reference_type || !$this->reference) {
            return $value;
        }

        return $this->reference->name;
    }
}
