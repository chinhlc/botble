@if (empty($widget_setting) || $widget_setting->status == 1)
    <div class="col-lg-3 col-md-3 col-sm-6 col-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="{{ route('users.list') }}">
            <div class="visual">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{ $users }}">0</span>
                </div>
                <div class="desc"> {{ trans('core.acl::users.users') }} </div>
            </div>
        </a>
    </div>
@endif