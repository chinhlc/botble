@extends('plugins.member::themes.master')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            {{ __('Change Password') }}
        </div>
        <div class="panel-body">
            {!! Form::open(['route' => 'public.member.post.change-password', 'method' => 'post']) !!}
            <div class="form-group @if ($errors->has('old_password')) has-error @endif">
                    <label for="old_password">{{ __('Current Password') }}</label>
                    <input type="password" class="form-control" name="old_password" id="old_password" placeholder="{{ __('Current Password') }}">
                    {!! Form::error('old_password', $errors) !!}
                </div>
            <div class="form-group @if ($errors->has('password')) has-error @endif">
                    <label for="password">{{ __('New Password') }}</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="{{ __('New Password') }}">
                    {!! Form::error('password', $errors) !!}
                </div>
            <div class="form-group @if ($errors->has('password_confirmation')) has-error @endif">
                    <label for="password_confirmation">{{ __('Password Confirmation') }}</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="{{ __('Password Confirmation') }}">
                    {!! Form::error('password_confirmation', $errors) !!}
                </div>
                <button type="submit" class="btn btn-primary btn-sm">{{ __('Change My Password') }}</button>
            {!! Form::close() !!}
        </div>
    </div>

@endsection
