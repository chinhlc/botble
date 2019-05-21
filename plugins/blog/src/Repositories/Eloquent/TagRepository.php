<?php

namespace Botble\Blog\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Blog\Repositories\Interfaces\TagInterface;

class TagRepository extends RepositoriesAbstract implements TagInterface
{

    /**
     * @var string
     */
    protected $screen = TAG_MODULE_SCREEN_NAME;

    /**
     * @return mixed
     * @author Sang Nguyen
     */
    public function getDataSiteMap()
    {
        $data = $this->model->where('tags.status', '=', 1)
            ->select('tags.*')
            ->orderBy('tags.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param int $limit
     * @return mixed
     * @author Sang Nguyen
     */
    public function getPopularTags($limit)
    {
        $data = $this->model->orderBy('tags.id', 'DESC')
            ->select('tags.*')
            ->limit($limit);
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param bool $active
     * @return mixed
     * @author Sang Nguyen
     */
    public function getAllTags($active = true)
    {
        $data = $this->model->select('tags.*');
        if ($active) {
            $data = $data->where(['tags.status' => 1]);
        }
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }
}
