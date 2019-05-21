<?php

namespace Botble\Dashboard\Models;

use Eloquent;
use Exception;

class DashboardWidgetSetting extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dashboard_widget_settings';

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'settings',
        'widget_id',
        'user_id',
        'order',
        'status',
    ];

    /**
     * @param $value
     * @author Sang Nguyen
     */
    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed
     * @author Sang Nguyen
     */
    public function getSettingsAttribute($value)
    {
        try {
            if (empty($value)) {
                return [];
            }
            return json_decode($value, true);
        } catch (Exception $ex) {
            return [];
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Sang Nguyen
     */
    public function widget()
    {
        return $this->belongsTo(DashboardWidget::class);
    }
}
