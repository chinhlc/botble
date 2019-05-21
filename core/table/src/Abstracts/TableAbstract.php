<?php

namespace Botble\Table\Abstracts;

use Assets;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Botble\Table\Supports\TableExportHandler;
use Carbon\Carbon;
use Form;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Query\Builder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\DataTables;

abstract class TableAbstract extends DataTable
{

    const TABLE_TYPE_ADVANCED = 'advanced';

    const TABLE_TYPE_SIMPLE = 'simple';

    /**
     * @var bool
     */
    protected $bStateSave = true;

    /**
     * @var DataTables
     */
    protected $table;

    /**
     * @var string
     */
    protected $type = self::TABLE_TYPE_ADVANCED;

    /**
     * @var string
     */
    protected $ajax_url;

    /**
     * @var int
     */
    protected $page_length = 10;

    /**
     * @var string
     */
    protected $view = 'core.table::table';

    /**
     * @var string
     */
    protected $filter_template = 'core.table::filter';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var bool
     */
    protected $has_checkbox = true;

    /**
     * @var bool
     */
    protected $has_operations = true;

    /**
     * @var bool
     */
    protected $has_actions = false;

    /**
     * @var string
     */
    protected $bulk_change_url = '';

    /**
     * @var bool
     */
    protected $has_filter = false;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var bool
     */
    protected $use_default_sorting = true;

    /**
     * @var string
     */
    protected $exportClass = TableExportHandler::class;

    /**
     * TableAbstract constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @author Sang Nguyen
     */
    public function __construct(Datatables $table, UrlGenerator $urlGenerator)
    {
        $this->table = $table;
        $this->ajax_url = $urlGenerator->current();

        if ($this->type == self::TABLE_TYPE_SIMPLE) {
            $this->page_length = -1;
        }

        if (!$this->getOption('id')) {
            $this->setOption('id', 'table_' . md5(get_class($this)));
        }

        if (!$this->getOption('class')) {
            $this->setOption('class', 'table table-striped table-hover vertical-middle');
        }

        $this->bulk_change_url = route('tables.bulk-change.save');
    }

    /**
     * @return bool
     * @author Sang Nguyen
     */
    public function isHasFilter(): bool
    {
        return $this->has_filter;
    }

    /**
     * @return RepositoryInterface
     * @author Sang Nguyen
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     * @author Sang Nguyen
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function getAjaxUrl(): string
    {
        return $this->ajax_url;
    }

    /**
     * @param string $ajax_url
     * @return $this
     * @author Sang Nguyen
     */
    public function setAjaxUrl(string $ajax_url): self
    {
        $this->ajax_url = $ajax_url;
        return $this;
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     * @return $this
     * @author Sang Nguyen
     */
    public function setView(string $view): self
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @return array
     * @author Sang Nguyen
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param $key
     * @return string
     * @author Sang Nguyen
     */
    public function getOption($key): string
    {
        $option = array_get($this->options, $key);
        return (string)$option;
    }

    /**
     * @param array $options
     * @return $this
     * @author Sang Nguyen
     */
    public function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     * @author Sang Nguyen
     */
    public function setOption($key, $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * @return null|string
     * @author Sang Nguyen
     */
    protected function getDom()
    {
        $dom = null;

        switch ($this->type) {
            case self::TABLE_TYPE_ADVANCED:
                $dom = "Brt<'datatables__info_wrap'pli<'clearfix'>>";
                break;
            case self::TABLE_TYPE_SIMPLE:
                $dom = "t<'datatables__info_wrap'<'clearfix'>>";
                break;
        }

        return $dom;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     * @author Sang Nguyen
     * @since 2.1
     * @throws \Throwable
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->ajax(['url' => $this->getAjaxUrl()])
            ->parameters([
                'dom' => $this->getDom(),
                'buttons' => $this->getBuilderParameters(),
                'initComplete' => $this->htmlInitComplete(),
                'drawCallback' => $this->htmlDrawCallback(),
                'paging' => true,
                'searching' => true,
                'info' => true,
                'searchDelay' => 350,
                'bStateSave' => $this->bStateSave,
                'lengthMenu' => array_sort_recursive([
                    array_values(array_unique(array_merge([10, 30, 50], [$this->page_length, -1]))),
                    array_values(array_unique(array_merge([10, 30, 50], [$this->page_length, trans('core.base::tables.all')])))
                ]),
                'pageLength' => $this->page_length,
                'processing' => true,
                'serverSide' => true,
                'bServerSide' => true,
                'bDeferRender' => true,
                'bProcessing' => true,
                'language' => [
                    'aria' => [
                        'sortAscending' => 'orderby asc',
                        'sortDescending' => 'orderby desc',
                        'paginate' => [
                            'next' => trans('pagination.next'),
                            'previous' => trans('pagination.previous'),
                        ],
                    ],
                    'emptyTable' => trans('core.base::tables.no_data'),
                    'info' => '<span class="dt-length-records"><i class="fa fa-globe"></i> <span class="d-none d-sm-inline">' . trans('core.base::tables.show_from') . '</span> _START_ ' . trans('core.base::tables.to') . ' _END_ ' . trans('core.base::tables.in') . ' <span class="badge badge-secondary bold badge-dt">_TOTAL_</span> <span class="hidden-xs">' . trans('core.base::tables.records') . '</span></span>',
                    'infoEmpty' => trans('core.base::tables.no_record'),
                    'infoFiltered' => '(' . trans('core.base::tables.filtered_from') . ' _MAX_ ' . trans('core.base::tables.records') . ')',
                    'lengthMenu' => '<span class="dt-length-style">_MENU_</span>',
                    'search' => '',
                    'zeroRecords' => trans('core.base::tables.no_record'),
                    'processing' => '<img src="' . url('/vendor/core/images/loading-spinner-blue.gif') . '" />',
                    'paginate' => [
                        'next' => trans('pagination.next'),
                        'previous' => trans('pagination.previous'),
                    ],
                ],
                'aaSorting' => $this->use_default_sorting ? [[($this->has_checkbox ? 1 : 0), 'desc']] : [],
            ]);
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function htmlInitComplete()
    {
        return 'function () {' . $this->htmlInitCompleteFunction() . '}';
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function htmlInitCompleteFunction()
    {
        return '
                $(".dataTables_wrapper").css({"width": $(this).closest(".dataTable").width()});
                
                if (jQuery().select2) {
                    $(document).find(".select-multiple").select2({
                        width: "100%",
                        allowClear: true,
                        placeholder: $(this).data("placeholder")
                    });
                    $(document).find(".select-search-full").select2({
                        width: "100%"
                    });
                    $(document).find(".select-full").select2({
                        width: "100%",
                        minimumResultsForSearch: -1
                    });
                }
            ';
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function htmlDrawCallback()
    {
        if ($this->type == self::TABLE_TYPE_SIMPLE) {
            return null;
        }

        return 'function () {' . $this->htmlDrawCallbackFunction() . '}';
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function htmlDrawCallbackFunction()
    {
        return '
            var pagination = $(this).closest(".dataTables_wrapper").find(".dataTables_paginate");
            pagination.toggle(this.api().page.info().pages > 1);
            
            var data_count = this.api().data().count();
            
            var length_select = $(this).closest(".dataTables_wrapper").find(".dataTables_length");
            var length_info = $(this).closest(".dataTables_wrapper").find(".dataTables_info");
            length_select.toggle(data_count >= 10);
            length_info.toggle(data_count > 0);
                
            if (jQuery().select2) {
                $(document).find(".select-multiple").select2({
                    width: "100%",
                    allowClear: true,
                    placeholder: $(this).data("placeholder")
                });
                $(document).find(".select-search-full").select2({
                    width: "100%"
                });
                $(document).find(".select-full").select2({
                    width: "100%",
                    minimumResultsForSearch: -1
                });
            }
        ';
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     * @throws \Throwable
     */
    public function getBuilderParameters()
    {
        $params = [
            'stateSave' => true,
        ];

        if ($this->type == self::TABLE_TYPE_SIMPLE) {
            return $params;
        }

        $buttons = array_merge($this->getButtons(), $this->getActionsButton());

        $buttons = array_merge($buttons, $this->getDefaultButtons());
        if (!$buttons) {
            return $params;
        }

        return $params + compact('buttons');
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function getDefaultButtons()
    {
        return [
            'reload',
        ];
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function getActionsButton()
    {
        if (!$this->getActions()) {
            return [];
        }

        return [
            [
                'extend' => 'collection',
                'text' => '<span>' . trans('core.base::forms.actions') . ' <span class="caret"></span></span>',
                'buttons' => $this->getActions(),
            ],
        ];
    }

    /**
     * @return mixed
     * @author Sang Nguyen
     * @since 2.1
     */
    public function actions()
    {
        return [];
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     */
    public function buttons()
    {
        return [];
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     */
    public function getButtons()
    {
        $buttons = [];
        if (!$this->buttons()) {
            return $buttons;
        }
        foreach ($this->buttons() as $key => $button) {
            if (array_get($button, 'extend') == 'collection') {
                $buttons[] = $button;
            } else {
                $buttons[] = [
                    'className' => 'action-item',
                    'text' => Html::tag('span', $button['text'], [
                        'data-action' => $key,
                        'data-href' => array_get($button, 'link'),
                    ])->toHtml(),
                ];
            }
        }
        return $buttons;
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     * @throws \Throwable
     */
    public function getActions()
    {
        if ($this->type == self::TABLE_TYPE_SIMPLE || !$this->actions()) {
            return [];
        }

        $actions = [];

        foreach ($this->actions() as $key => $action) {
            $actions[] = [
                'className' => 'action-item',
                'text' => '<span data-action="' . $key . '" data-href="' . $action['link'] . '"> ' . $action['text'] . '</span>',
            ];
        }
        return $actions;
    }

    /**
     * @return mixed
     * @author Sang Nguyen
     * @since 2.1
     */
    abstract public function columns();

    /**
     * Get columns.
     *
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     */
    public function getColumns()
    {
        $columns = $this->columns();

        if ($this->type == self::TABLE_TYPE_SIMPLE) {
            return $columns;
        }

        foreach ($columns as $key => &$column) {
            $column['class'] = array_get($column, 'class') . ' column-key-' . $key;
        }

        if ($this->repository && $this->repository->getScreen()) {
            $columns = apply_filters(BASE_FILTER_TABLE_HEADINGS, $columns, $this->repository->getScreen());
        }

        if ($this->has_operations) {
            $columns = array_merge($columns, $this->getOperationsHeading());
        }

        if ($this->has_checkbox) {
            $columns = array_merge($this->getCheckboxColumnHeading(), $columns);
        }

        return $columns;
    }

    /**
     * @return array
     * @author Sang Nguyen
     */
    public function getCheckboxColumnHeading()
    {
        return [
            'checkbox' => [
                'width' => '10px',
                'class' => 'text-left no-sort',
                'title' => '<input type="checkbox" class="table-check-all" data-set=".dataTable .checkboxes" />',
                'orderable' => false,
                'searchable' => false,
                'exportable' => false,
                'printable' => false,
            ],
        ];
    }

    /**
     * @return array
     * @author Sang Nguyen
     */
    public function getOperationsHeading()
    {
        return [
            'operations' => [
                'title' => trans('core.base::tables.operations'),
                'width' => '134px',
                'class' => 'text-center',
                'orderable' => false,
                'searchable' => false,
                'exportable' => false,
                'printable' => false,
            ],
        ];
    }

    /**
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @return mixed
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function render($view, $data = [], $mergeData = [])
    {
        Assets::addJavascript(['datatables', 'moment', 'datepicker'])
            ->addStylesheets(['datatables', 'datepicker'])
            ->addStyleSheetsDirectly([
                'vendor/core/css/components/table.css',
            ])
            ->addJavascriptDirectly([
                'vendor/core/packages/bootstrap3-typeahead.min.js',
            ])
            ->addAppModule(['table', 'filter']);

        $data['id'] = array_get($data, 'id', $this->getOption('id'));
        $data['class'] = array_get($data, 'class', $this->getOption('class'));

        $this->setAjaxUrl($this->ajax_url . '?' . http_build_query(request()->input()));

        $this->setOptions($data);

        $data['actions'] = $this->has_actions ? $this->bulkActions() : [];

        $data['table'] = $this;

        return parent::render($view, $data, $mergeData);
    }

    /**
     * @param array $data
     * @param array $mergeData
     * @param string $view
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @since 2.4
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function renderTable($data = [], $mergeData = [])
    {
        return $this->render($this->view, $data, $mergeData);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @return mixed
     * @author Sang Nguyen
     */
    public function applyScopes($query)
    {
        $request = request();

        $request_filters = [];

        if ($request->has('filter_columns') && ($request->input('filter_table_id') == $this->getOption('id'))) {
            $request_filters = [];
            foreach ($request->input('filter_columns') as $key => $item) {
                $request_filters[] = [
                    'column' => $item,
                    'operator' => $request->input('filter_operators.' . $key),
                    'value' => $request->input('filter_values.' . $key),
                ];
            }
        }

        foreach ($request_filters as $request_filter) {
            if (isset($request_filter['column']) && !empty($request_filter['column'])) {
                $query = $this->applyFilterCondition($query, $request_filter['column'], $request_filter['operator'], $request_filter['value']);
            }
        }

        return parent::applyScopes($query);
    }

    /**
     * @return array
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function bulkActions(): array
    {
        $actions = [];
        if ($this->getBulkChanges()) {
            $actions['bulk-change'] = view('core.table::bulk-changes', [
                'bulk_changes' => $this->getBulkChanges(),
                'class' => get_class($this),
                'url' => $this->bulk_change_url,
            ])->render();
        }

        return $actions;
    }

    /**
     * @return array
     * @author Sang Nguyen
     */
    public function getBulkChanges(): array
    {
        return [];
    }

    /**
     * @param $title
     * @param null $value
     * @param $type
     * @param null $data
     * @return array
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function getValueInput($title, $value = null, $type, $data = null)
    {
        $input_name = 'value';

        if (empty($title)) {
            $input_name = 'filter_values[]';
        }
        $attributes = [
            'class' => 'form-control input-value filter-column-value',
            'placeholder' => trans('core.table::general.value'),
            'autocomplete' => 'off',
        ];

        switch ($type) {
            case 'select':
                $attributes['class'] = $attributes['class'] . ' select';
                $attributes['placeholder'] = trans('core.table::general.select_option');
                $html = call_user_func_array([Form::class, 'customSelect'], [$input_name, $data, $value, $attributes])->toHtml();
                break;
            case 'select-search':
                $attributes['class'] = $attributes['class'] . ' select-search-full';
                $attributes['placeholder'] = trans('core.table::general.select_option');
                $html = Form::select($input_name, $data, $value, $attributes)->toHtml();
                break;
            case 'number':
                $html = Form::number($input_name, $value, $attributes)->toHtml();
                break;
            case 'date':
                $attributes['class'] = $attributes['class'] . ' datepicker';
                $attributes['data-date-format'] = config('core.base.general.date_format.js.date');
                $html = view('core.table::partials.date-field', ['content' => Form::text($input_name, $value, $attributes)->toHtml()])->render();
                break;
            default:
                $html = Form::text($input_name, $value, $attributes)->toHtml();
                break;
        }

        return compact('html', 'data');
    }

    /**
     * @return array
     * @author Sang Nguyen
     */
    protected function getYesNoSelect()
    {
        return [
            0 => __('No'),
            1 => __('Yes'),
        ];
    }

    /**
     * @param $key
     * @param $value
     * @return string
     * @author Sang Nguyen
     */
    public function prepareBulkChangeValue($key, $value)
    {
        if (strpos($key, '.') !== -1) {
            $key = array_last(explode('.', $key));
        }
        switch ($key) {
            case 'created_at':
            case 'updated_at':
                $value = Carbon::createFromFormat(config('core.base.general.date_format.date'), $value)->toDateTimeString();
                break;
        }

        return $value;
    }

    /**
     * @param $ids
     * @param $input_key
     * @param $input_value
     * @return boolean
     */
    public function saveBulkChanges($ids, $input_key, $input_value)
    {
        foreach ($ids as $id) {
            $item = $this->repository->findById($id);
            if ($item) {
                $this->saveBulkChangeItem($item, $input_key, $input_value);
                event(new UpdatedContentEvent($this->repository->getScreen(), request(), $item));
            }
        }
        return true;
    }

    /**
     * @param $item
     * @param $input_key
     * @param $input_value
     * @return false|\Illuminate\Database\Eloquent\Model
     */
    public function saveBulkChangeItem($item, $input_key, $input_value)
    {
        $item->{$input_key} = $this->prepareBulkChangeValue($input_key, $input_value);
        return $this->repository->createOrUpdate($item);
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->getBulkChanges();
    }

    /**
     * @return null
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function renderFilter()
    {
        $table_id = $this->getOption('id');
        $class = get_class($this);
        $columns = $this->getFilters();

        $request = request();
        $request_filters = [
            '-1' => [
                'column' => '',
                'operator' => '=',
                'value' => '',
            ],
        ];

        if ($request->input('filter_columns')) {
            $request_filters = [];
            foreach ($request->input('filter_columns', []) as $key => $item) {
                $request_filters[] = [
                    'column' => $item,
                    'operator' => $request->input('filter_operators.' . $key),
                    'value' => $request->input('filter_values.' . $key),
                ];
            }
        }

        return view($this->filter_template, compact('columns', 'class', 'table_id', 'request_filters'))->render();
    }

    /**
     * @param Builder $query
     * @param $key
     * @param $operator
     * @param $value
     * @return string
     * @author Sang Nguyen
     */
    public function applyFilterCondition($query, $key, $operator, $value)
    {
        if (strpos($key, '.') !== -1) {
            $key = array_last(explode('.', $key));
        }

        switch ($key) {
            case 'created_at':
            case 'updated_at':
                $value = Carbon::createFromFormat(config('core.base.general.date_format.date'), $value)->toDateString();
                $query = $query->whereDate($key, $operator, $value);
                break;
            default:
                if ($operator !== '=') {
                    $value = (float) $value;
                }
                $query = $query->where($key, $operator, $value);
        }

        return $query;
    }
}
