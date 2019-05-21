@if (empty($widget_setting) || $widget_setting->status == 1)
    <div class="col-12 widget_item" id="{{ $widget->name }}" data-url="{{ route('analytics.general') }}">
        <div class="portlet light bordered portlet-no-padding widget-load-has-callback">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject font-dark">{{ trans('plugins.analytics::analytics.' . $widget->name) }}</span>
                </div>
                @include('core.dashboard::partials.tools', ['settings' => !empty($widget_setting) ? $widget_setting->settings : []])
            </div>
            <div class="row portlet-body widget-content {{ array_get(!empty($widget_setting) ? $widget_setting->settings : [], 'state') }}" style="padding: 15px;"></div>
        </div>
    </div>
@endif