<div class="main-login-wrapper">
    <div class="form-border-box">
        <h2 class="normal">{{ __('Reset Password') }}</h2>
        <form role="form" method="POST" action="{{ route('public.member.password.email') }}">
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="control-label">{{ __('E-Mail Address') }}</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}"
                       autofocus>
                {!! Form::error('email', $errors) !!}
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control" name="password">
                {!! Form::error('password', $errors) !!}
            </div>

            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <label for="password-confirm" class="control-label">{{ __('Confirm Password') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                {!! Form::error('password_confirmation', $errors) !!}
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
</div>
