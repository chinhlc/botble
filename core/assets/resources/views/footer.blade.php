@foreach ($bodyScripts as $script)
    {!! Html::script($script['src'], $script['attributes']) !!}
@endforeach