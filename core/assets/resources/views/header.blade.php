@foreach ($stylesheets as $style)
    {!! Html::style($style['src'], $style['attributes']) !!}
@endforeach

@foreach ($headScripts as $script)
    {!! Html::script($script['src'], $script['attributes']) !!}
    @if (!empty($script['fallback']))
        <script>window.{!! $script['fallback'] !!} || document.write('<script src="{{ $script['fallbackURL'] }}"><\/script>')</script>
    @endif
@endforeach