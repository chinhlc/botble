@extends('core.installer::master')

@section('template_title')
    {{ __('Create account') }}
@endsection

@section('title')
    <i class="fa fa-magic fa-fw" aria-hidden="true"></i>
    {{ __('Create account') }}
@endsection

@section('container')

    <form method="post" action="{{ route('installers.account.save') }}">
        @csrf

        <div class="form-group {{ $errors->has('first_name') ? ' has-error ' : '' }}">
            <label for="first_name">
                {{ __('First name') }}
            </label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                   placeholder="{{ __('First name') }}"/>
            @if ($errors->has('first_name'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('first_name') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('last_name') ? ' has-error ' : '' }}">
            <label for="last_name">
                {{ __('Last name') }}
            </label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                   placeholder="{{ __('Last name') }}"/>
            @if ($errors->has('last_name'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('last_name') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('username') ? ' has-error ' : '' }}">
            <label for="username">
                {{ __('Username') }}
            </label>
            <input type="text" name="username" id="username" value="{{ old('username') }}"
                   placeholder="{{ __('Username') }}"/>
            @if ($errors->has('username'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('username') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('email') ? ' has-error ' : '' }}">
            <label for="email">
                {{ __('Email') }}
            </label>
            <input type="text" name="email" id="email" value="{{ old('email') }}"
                   placeholder="{{ __('Email') }}"/>
            @if ($errors->has('email'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('email') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('password') ? ' has-error ' : '' }}">
            <label for="password">
                {{ __('Password') }}
            </label>
            <input type="password" name="password" id="password" value="{{ old('password') }}"
                   placeholder="{{ __('Password') }}"/>
            @if ($errors->has('password'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('password') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error ' : '' }}">
            <label for="password_confirmation">
                {{ __('Password confirmation') }}
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}"
                   placeholder="{{ __('Password confirmation') }}"/>
            @if ($errors->has('password_confirmation'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('password_confirmation') }}
                </span>
            @endif
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="install_sample_data" value="1"> {{ __('Install sample data?') }}
            </label>
        </div>

        <div class="buttons">
            <button class="button" type="submit">
                {{ __('Create') }}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </button>
        </div>
    </form>

@endsection