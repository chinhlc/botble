<style>
    .main-right {
        display : none !important;
    }
</style>
<section class="main-box">
    <div class="main-box-content">
        <div class="box-style">
        <div class="member-page" style="margin: 0;">
                <div class="col-md-3">
                    <div class="profile-sidebar">
                        <div class="profile-userpic">
                            <a href="{{ route('public.member.avatar') }}">
                                <img src="@if (Auth::guard('member')->user()->avatar) {{ url(Auth::guard('member')->user()->avatar) }} @else http://placehold.it/250x250 @endif" class="userpic-avatar img-responsive" alt="{{ Auth::guard('member')->user()->name }}">
                            </a>
                        </div>
                        <div class="text-center">
                            <div class="profile-usertitle-name">
                                <strong>{{ Auth::guard('member')->user()->name }}</strong>
                            </div>

                        </div>

                        <div class="profile-usermenu">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <a href="{{ route('public.member.overview') }}" class="collection-item @if (Route::currentRouteName() == 'public.member.overview') active @endif">{{ __('Overview') }}</a>
                                    <i class="fa fa-info pull-right" aria-hidden="true"></i>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('public.member.edit') }}" class="collection-item @if (Route::currentRouteName() == 'public.member.edit') active @endif">{{ __('Edit Account') }}</a>
                                    <i class="fa fa-edit pull-right" aria-hidden="true"></i>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('public.member.avatar') }}" class="collection-item @if (Route::currentRouteName() == 'public.member.avatar') active @endif">{{ __('Change Avatar') }}</a>
                                    <i class="fa fa-picture-o pull-right" aria-hidden="true"></i>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('public.member.password') }}" class="collection-item @if (Route::currentRouteName() == 'public.member.password') active @endif">{{ __('Change Password') }}</a>
                                    <i class="fa fa-key pull-right" aria-hidden="true"></i>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('public.member.logout') }}" class="collection-item">{{ __('Logout') }}</a>
                                    <i class="fa fa-sign-out pull-right" aria-hidden="true"></i>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="col-md-9">
                    @yield('content')
                </div>
            </div>
            </div>
        </div>
</section>