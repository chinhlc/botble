<div class="table-actions">
    @foreach($actions as $action)
        <a {!! Html::attributes(array_get($action, 'attributes', [])) !!}>{{ $action['name'] }}</a>
    @endforeach
</div>