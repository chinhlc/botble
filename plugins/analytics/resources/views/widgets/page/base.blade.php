@if (empty($widget_setting) || $widget_setting->status == 1)
    <div class="col-md-6 col-sm-6 col-12 widget_item" id="{{ $widget->name }}" data-url="{{ route('analytics.page') }}">
        <div class="portlet light bordered portlet-no-padding">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject font-dark">{{ trans('plugins.analytics::analytics.' . $widget->name) }}</span>
                </div>
                @include('core.dashboard::partials.tools', ['settings' => !empty($widget_setting) ? $widget_setting->settings : []])
            </div>
            <div class="portlet-body scroll-table equal-height widget-content"></div>
        </div>
    </div>
@endif