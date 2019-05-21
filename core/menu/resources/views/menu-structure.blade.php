@if (!empty($menu) && $menu->id)
    <input type="hidden" name="deleted_nodes">
    <textarea name="menu_nodes" id="nestable-output" class="form-control hidden"></textarea>
    <div class="row widget-menu">
        <div class="col-md-4">
            <div class="panel-group" id="accordion">

                @php do_action(MENU_ACTION_SIDEBAR_OPTIONS) @endphp

                <div class="widget meta-boxes">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseCustomLink">
                        <h4 class="widget-title">
                            <span>{{ trans('core.menu::menu.add_link') }}</span>
                            <i class="fa fa-angle-down narrow-icon"></i>
                        </h4>
                    </a>
                    <div id="collapseCustomLink" class="panel-collapse collapse">
                        <div class="widget-body">
                            <div class="box-links-for-menu">
                                <div id="external_link" class="the-box">
                                    <div class="node-content">
                                        <div class="form-group">
                                            <label for="node-title">{{ trans('core.menu::menu.title') }}</label>
                                            <input type="text" class="form-control" id="node-title" autocomplete="false">
                                        </div>
                                        <div class="form-group">
                                            <label for="node-url">{{ trans('core.menu::menu.url') }}</label>
                                            <input type="text" class="form-control" id="node-url" placeholder="http://" autocomplete="false">
                                        </div>
                                        <div class="form-group">
                                            <label for="node-icon">{{ trans('core.menu::menu.icon') }}</label>
                                            <input type="text" class="form-control" id="node-icon" placeholder="fa fa-home" autocomplete="false">
                                        </div>
                                        <div class="form-group">
                                            <label for="node-css">{{ trans('core.menu::menu.css_class') }}</label>
                                            <input type="text" class="form-control" id="node-css" autocomplete="false">
                                        </div>
                                        <div class="form-group">
                                            <label for="target">{{ trans('core.menu::menu.target') }}</label>
                                            <div class="ui-select-wrapper">
                                                <select name="target" class="ui-select" id="target">
                                                    <option value="_self">{{ trans('core.menu::menu.self_open_link') }}</option>
                                                    <option value="_blank">{{ trans('core.menu::menu.blank_open_link') }}</option>
                                                </select>
                                                <svg class="svg-next-icon svg-next-icon-size-16">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="text-right form-group node-actions hidden">
                                            <a class="btn red btn-remove" href="#">{{ trans('core.menu::menu.remove') }}</a>
                                            <a class="btn blue btn-cancel" href="#">{{ trans('core.menu::menu.cancel') }}</a>
                                        </div>

                                        <div class="form-group">
                                            <div class="text-right add-button">
                                                <div class="btn-group">
                                                    <a href="#" class="btn-add-to-menu btn btn-primary"><span class="text"><i class="fa fa-plus"></i> {{ trans('core.menu::menu.add_to_menu') }}</span></a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('core.menu::menu.structure') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="dd nestable-menu" id="nestable" data-depth="0">
                        {!!
                             Menu::generateMenu([
                                'slug' => $menu->slug,
                                'view' => 'core.menu::partials.menu',
                                'theme' => false,
                                'active' => false,
                             ])
                        !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif