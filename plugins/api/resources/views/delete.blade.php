<div class="modal-box-container">
    {!! Form::open(['url' => route('api.clients.delete.post', $client->id)]) !!}
        {!! method_field('DELETE') !!}
        <div class="modal-title">
            <i class="til_img"></i> <strong>{{ trans('plugins.api::api.confirm_delete_title', ['name' => $client->name]) }}</strong>
        </div>
        <div class="modal-body modal-xs">
            <p>
                {{ trans('plugins.api::api.confirm_delete_description', ['name' => $client->name]) }}
            </p>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" class="btn btn-warning float-left" data-fancybox-close>{{ trans('plugins.api::api.cancel_delete') }}</a>
            <button type="submit" class="btn btn-danger float-right">{{ trans('plugins.api::api.continue_delete') }}</button>
        </div>
    {!! Form::close() !!}
</div>