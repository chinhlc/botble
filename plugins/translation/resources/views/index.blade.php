@extends('core.base::layouts.master')
@section('content')
    <div class="widget meta-boxes">
        <div class="widget-body box-translation">
            <div class="col-12">
                <p>{{ trans('plugins.translation::translation.export_warning') }}</p>
                <div class="alert alert-success success-import" style="display:none;">
                    <p>{!! trans('plugins.translation::translation.import_done') !!}</p>
                </div>
                <div class="alert alert-success success-find" style="display:none;">
                    <p>{!! trans('plugins.translation::translation.done_searching') !!}</p>
                </div>
                <div class="alert alert-success success-publish" style="display:none;">
                    <p>{{ trans('plugins.translation::translation.done_publishing') }} '{{ $group }}'!</p>
                </div>
                @if (Session::has('successPublish')) }}
                    <div class="alert alert-info">
                        {{ Session::get('successPublish') }}
                    </div>
                @endif
                <br />
                @if (!isset($group))
                    {!! Form::open(['route' => 'translations.import', 'class' => 'form-inline form-import', 'data-remote' => 'true', 'role' => 'form']) !!}
                        <div class="form-group">
                            <select name="replace" class="form-control">
                                <option value="0">{{ trans('plugins.translation::translation.append_translation') }}</option>
                                <option value="1">{{ trans('plugins.translation::translation.replace_translation') }}</option>
                            </select>
                            <button type="submit" class="btn btn-success import-groups" data-disable-with="{{ trans('plugins.translation::translation.loading') }}">{{ trans('plugins.translation::translation.import_group') }}</button>
                        </div>
                    {!! Form::close() !!}
                    <form class="form-inline form-find" method="POST" action="{{ route('translations.find') }}"
                          data-remote="true" role="form"
                          data-confirm="{{ trans('plugins.translation::translation.confirm_scan_translation') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-info find-translations" data-disable-with="{{ trans('plugins.translation::translation.searching') }}">
                            {{ trans('plugins.translation::translation.find_translation_files') }}
                        </button>
                    </form>
                @endif
                @if (isset($group))
                    <form class="form-inline form-publish" method="POST"
                          action="{{ route('translations.group.publish', $group) }}" data-remote="true"
                          role="form"
                          data-confirm="{{ trans('plugins.translation::translation.confirm_publish_group', ['group' => $group]) }}">
                        @csrf
                        <div class="form-group">
                            <button type="submit" class="btn btn-info publish-translation" data-disable-with="{{ trans('plugins.translation::translation.publishing') }}">{{ trans('plugins.translation::translation.publish_translations') }}</button>
                            <a href="{{ route('translations.list') }}" class="btn btn-secondary translation-back">{{ trans('plugins.translation::translation.back') }}</a>
                        </div>
                    </form>
                @endif
                {!! Form::open(['role' => 'form']) !!}
                    <div class="form-group">
                        <select name="group" id="group" class="form-control group-select select-search-full">
                            @foreach($groups as $key => $value)
                                <option value="{{ $key }}"{{ $key == $group ? ' selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                {!! Form::close() !!}
                @if($group)
                <form action="{{ route('translations.group.add', $group) }}" method="POST" role="form">
                    @csrf
                    <div class="form-group">
                        <textarea class="form-control" rows="3" name="keys" placeholder="{{ trans('plugins.translation::translation.add_key_description') }}"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="{{ trans('plugins.translation::translation.add_key_button') }}" class="btn btn-primary add_keys">
                    </div>
                </form>
                <hr>
                <h4>{{ trans('plugins.translation::translation.total') }}: {{ $numTranslations }}, {{ trans('plugins.translation::translation.changed') }}: {{ $numChanged }}</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th width="15%">{{ trans('plugins.translation::translation.key') }}</th>
                            @foreach($locales as $locale)
                                <th>{{ $locale }}</th>
                            @endforeach
                            @if($deleteEnabled)
                                <th>&nbsp;</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($translations as $key => $translation)
                            <tr id="{{ $key }}">
                                <td class="text-left"><code>{{ $key }}</code></td>
                                @foreach($locales as $locale)
                                    @php $item = isset($translation[$locale]) ? $translation[$locale] : null @endphp
                                    <td class="text-left">
                                        <a href="#edit" class="editable status-{{ $item ? $item->status : 0 }} locale-{{ $locale }}"
                                           data-locale="{{ $locale }}" data-name="{{ $locale . '|' . $key }}"
                                           data-type="textarea" data-pk="{{ $item ? $item->id : 0 }}" data-url="{{ $editUrl }}"
                                           data-title="{{ trans('plugins.translation::translation.edit_title') }}">{!! ($item ? htmlentities($item->value, ENT_QUOTES, 'UTF-8', false) : '') !!}</a>
                                    </td>
                                @endforeach
                                @if ($deleteEnabled)
                                    <td class="text-left">
                                        <a href="{{ route('translations.group.delete', ['groupKey' => $group, 'translationKey' => $key]) }}" class="delete-key" data-confirm="{{ trans('plugins.translation::translation.confirm_delete_key', ['key' => $key]) }}">
                                            <span class="fa fa-trash"></span>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p>{{ trans('plugins.translation::translation.choose_group_msg') }}</p>
                @endif
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@stop
