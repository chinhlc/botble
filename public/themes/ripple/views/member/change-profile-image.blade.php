@extends('theme.' . setting('theme') . '::views.member.master')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            {{ __('Change avatar') }}
        </div>
        <div class="panel-body">
            {!! Form::open(['route' => 'public.member.change-avatar', 'files' => true]) !!}
                <div class="form-group @if ($errors->has('avatar')) has-error @endif">
                    <label for="avatar">{{ __('Avatar') }}</label>
                    <input type="file" id="avatar" name="avatar">
                    {!! Form::error('avatar', $errors) !!}
                </div>
                <button type="submit" class="btn btn-primary btn-sm">{{ __('Change') }}</button>
            {!! Form::close() !!}
        </div>
    </div>
@endsection