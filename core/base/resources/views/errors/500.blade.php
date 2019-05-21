@extends('core.base::layouts.error')

@section('content')

    <div>
        <div class="col-md-10">
            <h3>{{ trans('core.base::errors.500_title') }}</h3>
            <p>{{ trans('core.base::errors.reasons') }}</p>
            <ul>
                {!! trans('core.base::errors.500_msg') !!}
            </ul>

            <p>{!! trans('core.base::errors.try_again') !!}</p>
        </div>
    </div>

@stop
