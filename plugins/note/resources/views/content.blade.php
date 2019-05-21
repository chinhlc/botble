<div class="tab-pane" id="tab_note">
    <div class="form-group row">
        <label class="col-sm-2 control-label text-right">{{ trans('plugins.blog::posts.form.note') }}</label>
        <div class="col-sm-10">
            {!! Form::textarea('note', null , ['class' => 'form-control', 'rows' => 4]) !!}
            @if (isset($notes) && count($notes) > 0)
                <br/>
                <table class="table table-bordered table-striped" id="table">
                    <thead>
                    <tr>
                        <th>{{ trans('core.base::tables.author') }}</th>
                        <th>{{ trans('core.base::tables.notes') }}</th>
                        <th>{{ trans('core.base::tables.created_at') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($notes as $note)
                        <tr>
                            <td style="min-width: 145px;">{{ $note->createdBy->getFullName() }}</td>
                            <td>{{ $note->note }}</td>
                            <td style="min-width: 145px;">{{ date_from_database($note->created_at, 'Y-m-d h:i A') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <div class="clearfix"></div>
</div>