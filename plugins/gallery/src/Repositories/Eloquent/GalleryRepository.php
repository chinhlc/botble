<?php

namespace Botble\Gallery\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Gallery\Repositories\Interfaces\GalleryInterface;

class GalleryRepository extends RepositoriesAbstract implements GalleryInterface
{

    /**
     * @var string
     */
    protected $screen = GALLERY_MODULE_SCREEN_NAME;

    /**
     * Get all galleries.c
     *
     * @return mixed
     * @author Sang Nguyen
     */
    public function getAll()
    {
        $data = $this->model->where('galleries.status', '=', 1);

        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @return mixed
     * @author Sang Nguyen
     */
    public function getDataSiteMap()
    {
        $data = $this->model->where('galleries.status', '=', 1)
            ->select('galleries.*')
            ->orderBy('galleries.created_at', 'desc');
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }

    /**
     * @param $limit
     * @return mixed
     * @author Sang Nguyen
     */
    public function getFeaturedGalleries($limit)
    {
        $data = $this->model->with(['user'])->where(['galleries.status' => 1, 'galleries.featured' => 1])
            ->select('galleries.id', 'galleries.name', 'galleries.user_id', 'galleries.image', 'galleries.created_at')
            ->orderBy('galleries.order', 'asc')
            ->limit($limit);
        return $this->applyBeforeExecuteQuery($data, $this->screen)->get();
    }
}
