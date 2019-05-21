<?php

namespace Botble\Blog\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Eloquent;
use Illuminate\Support\Collection;

class CategoryRepository extends RepositoriesAbstract implements CategoryInterface
{

    /**
     * @var string
     */
    protected $screen = CATEGORY_MODULE_SCREEN_NAME;

    /**
     * @return mixed
     * @author Sang Nguyen
     */
    public function getDataSiteMap()
    {
        $data = $this->model
            ->where('categories.status', '=', 1)
            ->select('categories.*')
            ->orderBy('categories.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param int $limit
     * @author Sang Nguyen
     * @return $this
     */
    public function getFeaturedCategories($limit)
    {
        $data = $this->model
            ->where(['categories.status' => 1, 'categories.featured' => 1])
            ->select('categories.id', 'categories.name', 'categories.icon')
            ->orderBy('categories.order', 'asc')
            ->select('categories.*')
            ->limit($limit);
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param array $condition
     * @return mixed
     * @author Sang Nguyen
     */
    public function getAllCategories(array $condition = [])
    {
        $data = $this->model->select('categories.*');
        if (!empty($condition)) {
            $data = $data->where($condition);
        }

        $data = $data->orderBy('order', 'DESC');

        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getCategoryById($id)
    {
        $data = $this->model->where(['categories.id' => $id, 'categories.status' => 1]);
        return $this->applyBeforeExecuteQuery($data, $this->screen, true)->first();
    }

    /**
     * @param array $select
     * @param array $orderBy
     * @return Collection
     */
    public function getCategories(array $select, array $orderBy)
    {
        $data = $this->model->select($select);
        foreach ($orderBy as $by => $direction) {
            $data = $data->orderBy($by, $direction);
        }
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function getAllRelatedChildrenIds($id)
    {
        if ($id instanceof Eloquent) {
            $model = $id;
        } else {
            $model = $this->getFirstBy(['categories.id' => $id]);
        }
        if (!$model) {
            return null;
        }

        $result = [];

        $children = $model->children()->select('categories.id')->get();

        foreach ($children as $child) {
            $result[] = $child->id;
            $result = array_merge($this->getAllRelatedChildrenIds($child), $result);
        }
        $this->resetModel();
        return array_unique($result);
    }

    /**
     * @param array $condition
     * @return mixed
     * @author Sang Nguyen
     */
    public function getAllCategoriesWithChildren(array $condition = [], array $with = [], array $select = ['*'])
    {
        $data = $this->model->where($condition)->with($with)->select($select);
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }
}
