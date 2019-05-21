<?php

namespace Botble\MaintenanceMode\Supports;

use Illuminate\Http\Request;

/**
 * Class to wrap Artisan Maintenance mode logic
 */
class MaintenanceMode
{
    /**
     * Bring Application out of Maintenance Mode
     *
     * @return mixed
     */
    public function up()
    {
        return unlink(storage_path('framework/down'));
    }

    /**
     * Put Application into Maintenance Mode
     *
     * @param Request $request
     * @return boolean
     */
    public function down($request)
    {
        $props = $request->only(['message', 'retry', 'allow', 'include_current_ip']);

        $retry = data_get($props, 'retry');

        $retry_seconds = is_numeric($retry) && $retry > 0 ? (int)$retry : null;

        $allowed_ips = [];

        if (!empty(data_get($props, 'allow'))) {
            $allowed_ip_list = str_replace(' ', '', data_get($props, 'allow'));
            $allowed_ips = explode(',', $allowed_ip_list);
        }

        if ($request->input('include_current_ip', true)) {
            $allowed_ips[] = $request->ip();
        }

        $payload = [
            'time'    => now()->timestamp,
            'message' => data_get($props, 'message'),
            'retry'   => $retry_seconds,
            'allowed' => array_unique($allowed_ips),
        ];

        return save_file_data(storage_path('framework/down'), $payload);
    }
}