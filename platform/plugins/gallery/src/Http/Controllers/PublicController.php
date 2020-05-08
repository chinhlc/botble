<?php

namespace Botble\Gallery\Http\Controllers;

use Auth;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Gallery\Models\Gallery as GalleryModel;
use Botble\Gallery\Repositories\Interfaces\GalleryInterface;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Gallery;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use SeoHelper;
use Theme;

class PublicController extends Controller
{

    /**
     * @var GalleryInterface
     */
    protected $galleryRepository;

    /**
     * @var SlugInterface
     */
    protected $slugRepository;

    /**
     * PublicController constructor.
     * @param GalleryInterface $galleryRepository
     * @param SlugInterface $slugRepository
     */
    public function __construct(GalleryInterface $galleryRepository, SlugInterface $slugRepository)
    {
        $this->galleryRepository = $galleryRepository;
        $this->slugRepository = $slugRepository;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getGalleries()
    {
        Gallery::registerAssets();
        $galleries = $this->galleryRepository->getAll();

        SeoHelper::setTitle(__('Galleries'));

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add(__('Galleries'), route('public.galleries'));

        return Theme::scope('galleries', compact('galleries'), 'plugins/gallery::themes.galleries')->render();
    }

    /**
     * @param $slug
     * @return \Illuminate\Http\Response|\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getGallery($slug)
    {
        $slug = $this->slugRepository->getFirstBy(['key' => $slug, 'reference_type' => GalleryModel::class]);
        if (!$slug) {
            abort(404);
        }

        $condition = [
            'id'     => $slug->reference_id,
            'status' => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::check() && request('preview')) {
            Arr::forget($condition, 'status');
        }

        $gallery = $this->galleryRepository->getFirstBy($condition, ['*'], ['slugable']);

        if (!$gallery) {
            abort(404);
        }

        SeoHelper::setTitle($gallery->name)->setDescription($gallery->description);

        $meta = new SeoOpenGraph();
        $meta->setDescription($gallery->description);
        $meta->setUrl(route('public.gallery', $slug->key));
        $meta->setTitle($gallery->name);
        $meta->setType('article');
        if ($gallery->image) {
            $meta->setImage(get_image_url($gallery->image));
        }

        Gallery::registerAssets();

        Theme::breadcrumb()->add(__('Home'), url('/'))->add($gallery->name, route('public.gallery', $slug->key));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, GALLERY_MODULE_SCREEN_NAME, $gallery);

        return Theme::scope('gallery', compact('gallery'), 'plugins/gallery::themes.gallery')->render();
    }
}
