<?php

namespace Botble\Blog\Http\Controllers;

use Auth;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Models\Tag;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Blog\Repositories\Interfaces\TagInterface;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use SeoHelper;
use Theme;

class PublicController extends Controller
{

    /**
     * @var TagInterface
     */
    protected $tagRepository;

    /**
     * @var SlugInterface
     */
    protected $slugRepository;

    /**
     * PublicController constructor.
     * @param TagInterface $tagRepository
     * @param SlugInterface $slugRepository
     */
    public function __construct(TagInterface $tagRepository, SlugInterface $slugRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->slugRepository = $slugRepository;
    }

    /**
     * @param Request $request
     * @param PostInterface $postRepository
     * @return \Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getSearch(Request $request, PostInterface $postRepository)
    {
        $query = $request->input('q');
        SeoHelper::setTitle(__('Search result for: ') . '"' . $query . '"')
            ->setDescription(__('Search result for: ') . '"' . $query . '"');

        $posts = $postRepository->getSearch($query, 0, 12);

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add(__('Search result for: ') . '"' . $query . '"', route('public.search'));

        return Theme::scope('search', compact('posts'))->render();
    }

    /**
     * @param string $slug
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getTag($slug)
    {
        $slug = $this->slugRepository->getFirstBy(['key' => $slug, 'reference_type' => Tag::class]);
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

        $tag = $this->tagRepository->getFirstBy($condition);

        if (!$tag) {
            abort(404);
        }

        SeoHelper::setTitle($tag->name)->setDescription($tag->description);

        $meta = new SeoOpenGraph();
        $meta->setDescription($tag->description);
        $meta->setUrl(route('public.tag', $slug->key));
        $meta->setTitle($tag->name);
        $meta->setType('article');

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(trans('plugins/blog::tags.edit_this_tag'), route('tags.edit', $tag->id));
        }

        $posts = get_posts_by_tag($tag->id);

        Theme::breadcrumb()->add(__('Home'), url('/'))->add($tag->name, route('public.tag', $slug->key));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, TAG_MODULE_SCREEN_NAME, $tag);

        return Theme::scope('tag', compact('tag', 'posts'), 'plugins/blog::themes.tag')->render();
    }
}
