<?php

namespace Botble\Blog\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Eloquent;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class PostRepository extends RepositoriesAbstract implements PostInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFeatured($limit = 5)
    {
        $data = $this->model
            ->where([
                'posts.status'      => BaseStatusEnum::PUBLISHED,
                'posts.is_featured' => 1,
            ])
            ->limit($limit)
            ->with('slugable')
            ->orderBy('posts.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getListPostNonInList(array $selected = [], $limit = 7)
    {
        $data = $this->model
            ->where('posts.status', '=', BaseStatusEnum::PUBLISHED)
            ->whereNotIn('posts.id', $selected)
            ->limit($limit)
            ->with('slugable')
            ->orderBy('posts.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getRelated($id, $limit = 3)
    {
        $data = $this->model
            ->where('posts.status', '=', BaseStatusEnum::PUBLISHED)
            ->where('posts.id', '!=', $id)
            ->limit($limit)
            ->with('slugable')
            ->orderBy('posts.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getByCategory($categoryId, $paginate = 12, $limit = 0)
    {
        if (!is_array($categoryId)) {
            $categoryId = [$categoryId];
        }

        $data = $this->model
            ->where('posts.status', '=', BaseStatusEnum::PUBLISHED)
            ->join('post_categories', 'post_categories.post_id', '=', 'posts.id')
            ->join('categories', 'post_categories.category_id', '=', 'categories.id')
            ->whereIn('post_categories.category_id', $categoryId)
            ->select('posts.*')
            ->distinct()
            ->with('slugable')
            ->orderBy('posts.created_at', 'desc');

        if ($paginate != 0) {
            return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
        }

        return $this->applyBeforeExecuteQuery($data)->limit($limit)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserId($authorId, $paginate = 6)
    {
        $data = $this->model
            ->where([
                'posts.status'    => BaseStatusEnum::PUBLISHED,
                'posts.author_id' => $authorId,
            ])
            ->with('slugable')
            ->select('posts.*')
            ->orderBy('posts.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSiteMap()
    {
        $data = $this->model
            ->with('slugable')
            ->where('posts.status', '=', BaseStatusEnum::PUBLISHED)
            ->select('posts.*')
            ->orderBy('posts.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getByTag($tag, $paginate = 12)
    {
        $data = $this->model
            ->with('slugable')
            ->where('posts.status', '=', BaseStatusEnum::PUBLISHED)
            ->whereHas('tags', function ($query) use ($tag) {
                /**
                 * @var Builder $query
                 */
                $query->where('tags.id', $tag);
            })
            ->select('posts.*')
            ->orderBy('posts.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecentPosts($limit = 5, $categoryId = 0)
    {
        $posts = $this->model->where(['posts.status' => BaseStatusEnum::PUBLISHED]);

        if ($categoryId != 0) {
            $posts = $posts->join('post_categories', 'post_categories.post_id', '=', 'posts.id')
                ->where('post_categories.category_id', '=', $categoryId);
        }

        $data = $posts->limit($limit)
            ->with('slugable')
            ->select('posts.*')
            ->orderBy('posts.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getSearch($query, $limit = 10, $paginate = 10)
    {
        $posts = $this->model->with('slugable')->where('status', BaseStatusEnum::PUBLISHED);
        foreach (explode(' ', $query) as $term) {
            $posts = $posts->where('name', 'LIKE', '%' . $term . '%');
        }

        $data = $posts->select('posts.*')
            ->orderBy('posts.created_at', 'desc');

        if ($limit) {
            $data = $data->limit($limit);
        }

        if ($paginate) {
            return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllPosts($perPage = 12, $active = true)
    {
        $data = $this->model->select('posts.*')
            ->with('slugable')
            ->orderBy('posts.created_at', 'desc');

        if ($active) {
            $data = $data->where('posts.status', BaseStatusEnum::PUBLISHED);
        }

        return $this->applyBeforeExecuteQuery($data)->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function getPopularPosts($limit, array $args = [])
    {
        $data = $this->model
            ->with('slugable')
            ->orderBy('posts.views', 'DESC')
            ->select('posts.*')
            ->where('posts.status', BaseStatusEnum::PUBLISHED)
            ->limit($limit);

        if (!empty(Arr::get($args, 'where'))) {
            $data = $data->where($args['where']);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedCategoryIds($model)
    {
        $model = $model instanceof Eloquent ? $model : $this->findOrFail($model);

        try {
            return $model->categories()->allRelatedIds()->toArray();
        } catch (Exception $exception) {
            return [];
        }
    }
}
