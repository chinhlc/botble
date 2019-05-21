@extends('plugins.member::themes.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            {{ __('Edit profile') }}
        </div>
        <div class="panel-body">

            {!! Form::open(['route' => 'public.member.edit']) !!}
                <div class="form-group">
                    <label for="name" class="control-label">{{ __('Name') }}</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{ Auth::guard('member')->user()->name }}">
                    {!! Form::error('name', $errors) !!}
                </div>

                <div class="form-group @if ($errors->has('dob')) has-error @endif">
                    <label for="date_of_birth">{{ __('Date of birth') }}</label>
                    <input id="date_of_birth" type="date" class="form-control" name="dob" value="{{ Auth::guard('member')->user()->dob }}">
                    {!! Form::error('dob', $errors) !!}
                </div>

                <div class="form-group @if ($errors->has('email')) has-error @endif">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" type="text" class="form-control" disabled="disabled" value="{{ Auth::guard('member')->user()->email }}" name="email">
                    {!! Form::error('email', $errors) !!}
                </div>

                <div class="form-group @if ($errors->has('phone')) has-error @endif">
                    <label for="phone">{{ __('Phone') }}</label>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="{{ __('Phone') }}" value="{{ Auth::guard('member')->user()->phone }}">
                    {!! Form::error('phone', $errors) !!}
                </div>

                <div class="form-group col s12">
                    <button type="submit" class="btn btn-primary">{{ __('Update Profile') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection