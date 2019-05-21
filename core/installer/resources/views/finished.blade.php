@extends('core.installer::master')

@section('template_title')
    {{ trans('core.installer::installer.final.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-flag-checkered fa-fw" aria-hidden="true"></i>
    {{ trans('core.installer::installer.final.title') }}
@endsection

@section('container')

    <p>{{ __('Install Botble CMS successfully!') }}</p>

    <div class="buttons">
        <a href="{{ route('public.index') }}" class="button">{{ trans('core.installer::installer.final.exit') }}</a>
    </div>

@endsection
