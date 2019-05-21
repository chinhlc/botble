<?php

namespace Botble\Blog\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Eloquent;
use Exception;
use Illuminate\Database\Query\Builder;

class PostRepository extends RepositoriesAbstract implements PostInterface
{

    /**
     * @var string
     */
    protected $screen = POST_MODULE_SCREEN_NAME;

    /**
     * @param int $limit
     * @return mixed
     * @author Sang Nguyen
     */
    public function getFeatured($limit = 5)
    {
        $data = $this->model
            ->where([
                'posts.status' => 1,
                'posts.featured' => 1,
            ])
            ->limit($limit)
            ->orderBy('posts.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param array $selected
     * @param int $limit
     * @return mixed
     * @author Sang Nguyen
     */
    public function getListPostNonInList(array $selected = [], $limit = 7)
    {
        $data = $this->model
            ->where('posts.status', '=', 1)
            ->whereNotIn('posts.id', $selected)
            ->orderBy('posts.created_at', 'desc')
            ->limit($limit)
            ->orderBy('posts.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param int $id
     * @param int $limit
     * @return mixed
     * @author Sang Nguyen
     */
    public function getRelated($id, $limit = 3)
    {
        $data = $this->model
            ->where('posts.status', '=', 1)
            ->where('posts.id', '!=', $id)
            ->limit($limit)
            ->orderBy('posts.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param int|array $category_id
     * @param int $paginate
     * @param int $limit
     * @return mixed
     * @author Sang Nguyen
     */
    public function getByCategory($category_id, $paginate = 12, $limit = 0)
    {
        if (!is_array($category_id)) {
            $category_id = [$category_id];
        }
        $data = $this->model
            ->where('posts.status', '=', 1)
            ->join('post_category', 'post_category.post_id', '=', 'posts.id')
            ->join('categories', 'post_category.category_id', '=', 'categories.id')
            ->whereIn('post_category.category_id', $category_id)
            ->select('posts.*')
            ->distinct()
            ->orderBy('posts.created_at', 'desc');
        if ($paginate != 0) {
            return $this->applyBeforeExecuteQuery($data, $this->screen)->paginate($paginate);
        }
        return $this->applyBeforeExecuteQuery($data, $this->screen)->limit($limit)->get();
    }

    /**
     * @param int $user_id
     * @param int $paginate
     * @return mixed
     * @author Sang Nguyen
     */
    public function getByUserId($user_id, $paginate = 6)
    {
        $data = $this->model
            ->where(['posts.status' => 1, 'posts.user_id' => $user_id])
            ->select('posts.*')
            ->orderBy('posts.views', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->paginate($paginate);
    }

    /**
     * @return mixed
     * @author Sang Nguyen
     */
    public function getDataSiteMap()
    {
        $data = $this->model
            ->where('posts.status', '=', 1)
            ->select('posts.*')
            ->orderBy('posts.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param int $tag
     * @param int $paginate
     * @return mixed
     * @author Sang Nguyen
     */
    public function getByTag($tag, $paginate = 12)
    {
        $data = $this->model
            ->where('posts.status', '=', 1)
            ->whereHas('tags', function ($query) use ($tag) {
                /**
                 * @var Builder $query
                 */
                $query->where('tags.id', $tag);
            })
            ->select('posts.*')
            ->orderBy('posts.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->paginate($paginate);
    }

    /**
     * @param int $limit
     * @param int $category_id
     * @return mixed
     * @author Sang Nguyen
     */
    public function getRecentPosts($limit = 5, $category_id = 0)
    {
        $posts = $this->model->where(['posts.status' => 1]);

        if ($category_id != 0) {
            $posts = $posts->join('post_category', 'post_category.post_id', '=', 'posts.id')
                ->where('post_category.category_id', '=', $category_id);
        }

        $data = $posts->limit($limit)
            ->select('posts.*')
            ->orderBy('posts.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param string $query
     * @param int $limit
     * @param int $paginate
     * @return mixed
     * @author Sang Nguyen
     */
    public function getSearch($query, $limit = 10, $paginate = 10)
    {
        $posts = $this->model->where('status', 1);
        foreach (explode(' ', $query) as $term) {
            $posts = $posts->where('name', 'LIKE', '%' . $term . '%');
        }

        $data = $posts->select('posts.*')
            ->orderBy('posts.created_at', 'desc');
        if ($limit) {
            $data = $data->limit($limit);
        }

        if ($paginate) {
            return $this->applyBeforeExecuteQuery($data, $this->screen)->paginate($paginate);
        }
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param bool $active
     * @return mixed
     * @author Sang Nguyen
     */
    public function getAllPosts($active = true)
    {
        $data = $this->model->select('posts.*');
        if ($active) {
            $data = $data->where(['posts.status' => 1]);
        }

        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param int $limit
     * @param array $args
     * @return mixed
     * @author Sang Nguyen
     */
    public function getPopularPosts($limit, array $args = [])
    {
        $data = $this->model->orderBy('posts.views', 'DESC')
            ->select('posts.*')
            ->limit($limit);
        if (!empty(array_get($args, 'where'))) {
            $data = $data->where($args['where']);
        }
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param Eloquent|int $model
     * @return array
     */
    public function getRelatedCategoryIds($model)
    {
        $model = $model instanceof Eloquent ? $model : $this->findById($model);

        try {
            return $model->categories()->allRelatedIds()->toArray();
        } catch (Exception $exception) {
            return [];
        }
    }
}
