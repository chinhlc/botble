<?php

namespace Botble\Page\Repositories\Eloquent;

use Botble\Page\Repositories\Interfaces\PageInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class PageRepository extends RepositoriesAbstract implements PageInterface
{

    /**
     * @var string
     */
    protected $screen = PAGE_MODULE_SCREEN_NAME;

    /**
     * @return mixed
     * @author Sang Nguyen
     */
    public function getDataSiteMap()
    {
        $data = $this->model
            ->where('pages.status', 1)
            ->select('pages.*')
            ->orderBy('pages.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param $limit
     * @author Sang Nguyen
     * @return $this
     */
    public function getFeaturedPages($limit)
    {
        $data = $this->model
            ->where(['pages.status' => 1, 'pages.featured' => 1])
            ->orderBy('pages.created_at', 'asc')
            ->select('pages.*')
            ->limit($limit)
            ->orderBy('pages.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param $array
     * @param array $select
     * @return mixed
     * @author Sang Nguyen
     */
    public function whereIn($array, $select = [])
    {
        $pages = $this->model
            ->whereIn('pages.id', $array)
            ->where('pages.status', 1);
        if (empty($select)) {
            $select = 'pages.*';
        }
        $data = $pages
            ->select($select)
            ->orderBy('pages.created_at', 'ASC');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param $query
     * @param int $limit
     * @return mixed
     * @author Sang Nguyen
     */
    public function getSearch($query, $limit = 10)
    {
        $pages = $this->model->where('pages.status', 1);
        foreach (explode(' ', $query) as $term) {
            $pages = $pages->where('pages.name', 'LIKE', '%' . $term . '%');
        }

        $data = $pages->select('pages.*')->orderBy('pages.created_at', 'desc')
            ->limit($limit);
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param bool $active
     * @return mixed
     * @author Sang Nguyen
     */
    public function getAllPages($active = true)
    {
        $data = $this->model->select('pages.*');
        if ($active) {
            $data = $data->where(['pages.status' => 1]);
        }
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }
}
