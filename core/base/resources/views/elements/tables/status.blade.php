@foreach($statuses as $key => $status)
    @if ($key == $selected)
        <span class="{{ array_get($status, 'class', 'label-info') }} status-label" data-value="{{ $key }}" data-text="{{ ucfirst(array_get($status, 'text')) }}">
            {{ array_get($status, 'text') }}
        </span>
    @endif
@endforeach