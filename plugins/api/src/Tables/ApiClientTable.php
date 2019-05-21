<?php

namespace Botble\Api\Tables;

use Botble\Table\Abstracts\TableAbstract;
use DB;

class ApiClientTable extends TableAbstract
{

    /**
     * @var string
     */
    protected $type = self::TABLE_TYPE_SIMPLE;

    /**
     * @var string
     */
    protected $view = 'core.base::elements.simple-table';

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Sang Nguyen
     * @throws \Exception
     */
    public function ajax()
    {
        return $this->table
            ->query($this->query())
            ->addColumn('operations', function ($item) {
                return simple_table_actions([
                    [
                        'name' => trans('plugins.api::api.edit'),
                        'attributes' => [
                            'href' => 'javascript:;',
                            'data-fancybox' => true,
                            'data-type' => 'ajax',
                            'data-src' => route('api.clients.edit', $item->id),
                        ],
                    ],
                    [
                        'name' => trans('plugins.api::api.delete'),
                        'attributes' => [
                            'data-fancybox' => true,
                            'data-type' => 'ajax',
                            'data-src' => route('api.clients.delete', $item->id),
                            'class' => 'text-danger',
                        ],
                    ],
                ]);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     * @author Sang Nguyen
     */
    public function query()
    {
        $query = DB::table('oauth_clients')
            ->where(['revoked' => false])
            ->select(['id', 'name', 'secret']);
        return $this->applyScopes($query);
    }

    /**
     * @return mixed
     * @author Sang Nguyen
     */
    public function columns()
    {
        return [
            'id' => [
                'name' => 'id',
                'title' => trans('plugins.api::api.client_id'),
                'width' => '60px',
                'orderable' => false,
                'class' => 'no-sort text-center',
            ],
            'name' => [
                'name' => 'name',
                'title' => trans('plugins.api::api.name'),
                'orderable' => false,
                'class' => 'text-center',
            ],
            'secret' => [
                'name' => 'secret',
                'title' => trans('plugins.api::api.secret'),
                'orderable' => false,
                'class' => 'text-center',
            ],
        ];
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function buttons()
    {
        return [];
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function actions()
    {
        return [];
    }

    /**
     * Get columns.
     *
     * @return array
     * @author Sang Nguyen
     */
    public function getColumns()
    {
        return array_merge($this->columns(), $this->getOperationsHeading());
    }
}