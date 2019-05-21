<?php

namespace Botble\Base\Models;

use Eloquent;

class MetaBox extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'meta_boxes';

    /**
     * @param $value
     * @author Sang Nguyen
     */
    public function setMetaValueAttribute($value)
    {
        $this->attributes['meta_value'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed
     * @author Sang Nguyen
     */
    public function getMetaValueAttribute($value)
    {
        return json_decode($value, true);
    }
}
