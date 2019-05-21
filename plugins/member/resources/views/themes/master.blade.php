<div class="member-page">
    <div class="container">
        <div class="row profile">
            <div class="col-md-3">
                <div class="profile-sidebar">
                    <div class="profile-userpic">
                        <a href="{{ route('public.member.avatar') }}">
                            <img src="@if (Auth::guard('member')->user()->avatar) {{ url(Auth::guard('member')->user()->avatar) }} @else https://placehold.it/200x200 @endif" class="userpic-avatar img-fluid" alt="{{ Auth::guard('member')->user()->name }}">
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
                                <i class="fa fa-info float-right" aria-hidden="true"></i>
                            </li>
                            <li class="list-group-item">
                                <a href="{{ route('public.member.edit') }}" class="collection-item @if (Route::currentRouteName() == 'public.member.edit') active @endif">{{ __('Edit Account') }}</a>
                                <i class="fa fa-edit float-right" aria-hidden="true"></i>
                            </li>
                            <li class="list-group-item">
                                <a href="{{ route('public.member.avatar') }}" class="collection-item @if (Route::currentRouteName() == 'public.member.avatar') active @endif">{{ __('Change Avatar') }}</a>
                                <i class="far fa-image float-right" aria-hidden="true"></i>
                            </li>
                            <li class="list-group-item">
                                <a href="{{ route('public.member.password') }}" class="collection-item @if (Route::currentRouteName() == 'public.member.password') active @endif">{{ __('Change Password') }}</a>
                                <i class="fa fa-key float-right" aria-hidden="true"></i>
                            </li>
                            <li class="list-group-item">
                                <a href="{{ route('public.member.logout') }}" class="collection-item">{{ __('Logout') }}</a>
                                <i class="fa fa-sign-out float-right" aria-hidden="true"></i>
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