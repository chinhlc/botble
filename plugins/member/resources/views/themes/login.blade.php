<div class="main-login-wrapper">
    <div class="form-border-box">
        <h2 class="normal">{{ __('Login') }}</h2>
        <form role="form" method="POST" action="{{ route('public.member.login.post') }}">
            {{ csrf_field() }}

            @if ($errors->has('confirmation'))
                <div class="alert alert-info" role="alert">
                    {!! $errors->first('confirmation') !!}
                </div>
            @endif

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="control-label">{{ __('E-Mail Address') }}</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" autofocus>
                {!! Form::error('email', $errors) !!}
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control" name="password">
                {!! Form::error('password', $errors) !!}
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember"> {{ __('Remember Me') }}
                    </label>
                </div>
            </div>

            <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Login') }}
                    </button>
            </div>
            <div class="form-group">
                <a href="{{ route('public.member.password.reset') }}" rel="nofollow">{{ __('Forgot Your Password?') }}</a> |
                <a href="{{ route('public.member.register') }}" rel="nofollow">{{ __('Or create new account') }}</a>
            </div>
        </form>
    </div>
</div>
