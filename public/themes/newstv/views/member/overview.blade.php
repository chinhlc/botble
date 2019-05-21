@extends('theme.newstv::views.member.master')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            {{ __('Profile Details') }}
        </div>
        <div class="panel-body">
            <p><i class="fa fa-user"></i> {{ __('Name') }}: {{ Auth::guard('member')->user()->name }}</p>
            <p><i class="fa fa-calendar"></i> {{ __('Date of birth') }}: {{ Auth::guard('member')->user()->dob }}</p>
            <p><i class="fa fa-envelope-o"></i> {{ __('Email') }}: {{ Auth::guard('member')->user()->email }}</p>
            <p><i class="fa fa-phone"></i> {{ __('Phone') }}: {{ Auth::guard('member')->user()->phone }}</p>
        </div>
    </div>
@endsection