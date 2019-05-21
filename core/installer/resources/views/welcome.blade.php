@extends('core.installer::master')

@section('template_title')
    {{ trans('core.installer::installer.welcome.templateTitle') }}
@endsection

@section('title')
    {{ trans('core.installer::installer.welcome.title') }}
@endsection

@section('container')
    <p class="text-center">
        {{ trans('core.installer::installer.welcome.message') }}
    </p>
    <p class="text-center">
        <a href="{{ route('installers.requirements') }}" class="button">
            {{ trans('core.installer::installer.welcome.next') }}
            <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
        </a>
    </p>
@endsection
