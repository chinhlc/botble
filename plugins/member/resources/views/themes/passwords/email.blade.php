<div class="main-login-wrapper">
    <div class="form-border-box">
        <h2 class="normal">{{ __('Reset Password') }}</h2>
        <form role="form" method="POST" action="{{ route('public.member.password.request') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="control-label">{{ __('E-Mail Address') }}</label>

                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                {!! Form::error('email', $errors) !!}
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>
            <div class="form-group">
                <a href="{{ route('public.member.login') }}" rel="nofollow">{{ __('Back to login page') }}</a>
            </div>
        </form>
    </div>
</div>
