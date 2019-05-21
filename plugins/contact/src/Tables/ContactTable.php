<?php

namespace Botble\Contact\Tables;

use Botble\Contact\Repositories\Interfaces\ContactInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class ContactTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $has_actions = true;

    /**
     * @var bool
     */
    protected $has_filter = true;

    /**
     * TagTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ContactInterface $contactRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ContactInterface $contactRepository)
    {
        $this->repository = $contactRepository;
        $this->setOption('id', 'table-contacts');
        parent::__construct($table, $urlGenerator);
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Sang Nguyen
     * @since 2.1
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                return anchor_link(route('contacts.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return table_checkbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return date_from_database($item->created_at, config('core.base.general.date_format.date'));
            })
            ->editColumn('is_read', function ($item) {
                return table_status($item->is_read, [
                    1 => [
                        'class' => 'label-success',
                        'text' => trans('plugins.contact::contact.read'),
                    ],
                    0 => [
                        'class' => 'label-warning',
                        'text' => trans('plugins.contact::contact.unread'),
                    ],
                ]);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, CONTACT_MODULE_SCREEN_NAME)
            ->addColumn('operations', function ($item) {
                return table_actions('contacts.edit', 'contacts.delete', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     * @author Sang Nguyen
     * @since 2.1
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $query = $model
            ->select([
                'contacts.id',
                'contacts.name',
                'contacts.phone',
                'contacts.email',
                'contacts.created_at',
                'contacts.is_read',
            ]);
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, CONTACT_MODULE_SCREEN_NAME));
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     */
    public function columns()
    {
        return [
            'id' => [
                'name' => 'contacts.id',
                'title' => trans('core.base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name' => 'contacts.name',
                'title' => trans('core.base::tables.name'),
                'class' => 'text-left',
            ],
            'phone' => [
                'name' => 'contacts.phone',
                'title' => trans('plugins.contact::contact.tables.phone'),
            ],
            'email' => [
                'name' => 'contacts.email',
                'title' => trans('plugins.contact::contact.tables.email'),
            ],
            'created_at' => [
                'name' => 'contacts.created_at',
                'title' => trans('core.base::tables.created_at'),
                'width' => '100px',
            ],
            'is_read' => [
                'name' => 'contacts.is_read',
                'title' => trans('plugins.contact::contact.form.is_read'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     */
    public function buttons()
    {
        $buttons = [];
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, CONTACT_MODULE_SCREEN_NAME);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function bulkActions(): array
    {
        $actions = parent::bulkActions();

        $actions['delete-many'] = view('core.table::partials.delete', [
            'href' => route('contacts.delete.many'),
            'data_class' => get_class($this),
        ]);

        return $actions;
    }

    /**
     * @return mixed
     */
    public function getBulkChanges(): array
    {
        return [
            'contacts.name' => [
                'title' => trans('core.base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
                'callback' => 'getNames',
            ],
            'contacts.email' => [
                'title' => trans('core.base::tables.email'),
                'type' => 'text',
                'validate' => 'required|max:120',
                'callback' => 'getEmails',
            ],
            'contacts.phone' => [
                'title' => trans('plugins.contact::contact.sender_phone'),
                'type' => 'text',
                'validate' => 'required|max:120',
                'callback' => 'getPhones',
            ],
            'contacts.is_read' => [
                'title' => trans('core.base::tables.status'),
                'type' => 'select',
                'choices' => [
                    0 => trans('plugins.contact::contact.unread'),
                    1 => trans('plugins.contact::contact.read'),
                ],
                'validate' => 'required|in:0,1',
            ],
            'contacts.created_at' => [
                'title' => trans('core.base::tables.created_at'),
                'type' => 'date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return $this->repository->pluck('contacts.name', 'contacts.id');
    }

    /**
     * @return array
     */
    public function getEmails()
    {
        return $this->repository->pluck('contacts.email', 'contacts.id');
    }

    /**
     * @return array
     */
    public function getPhones()
    {
        return $this->repository->pluck('contacts.phone', 'contacts.id');
    }

    /**
     * @return array
     */
    public function getDefaultButtons()
    {
        return [
            'export',
            'reload',
        ];
    }
}
