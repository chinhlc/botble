<?php

namespace Botble\Blog\Providers;

use Assets;
use Botble\Base\Events\SessionStarted;
use Botble\Base\Supports\Helper;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\SeoHelper\SeoOpenGraph;
use Eloquent;
use Event;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Botble\Blog\Repositories\Interfaces\TagInterface;
use Menu;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Auth;
use SeoHelper;
use Theme;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Boot the service provider.
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function boot()
    {
        if (defined('MENU_ACTION_SIDEBAR_OPTIONS')) {
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 2);
        }
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 21, 2);
        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 2, 1);

        Event::listen(SessionStarted::class, function () {
            admin_bar()->registerLink('Post', route('posts.create'), 'add-new');
        });

        add_shortcode('blog-posts', __('Blog posts'), __('Add blog posts'), [$this, 'renderBlogPosts']);
        shortcode()->setAdminConfig('blog-posts', view('plugins.blog::partials.posts-short-code-admin-config')->render());
    }

    /**
     * Register sidebar options in menu
     * @throws \Throwable
     */
    public function registerMenuOptions()
    {
        if (Auth::user()->hasPermission('categories.list')) {
            $categories = Menu::generateSelect([
                'model' => $this->app->make(CategoryInterface::class)->getModel(),
                'screen' => CATEGORY_MODULE_SCREEN_NAME,
                'theme' => false,
                'options' => [
                    'class' => 'list-item',
                ],
            ]);
            echo view('plugins.blog::categories.partials.menu-options', compact('categories'));
        }

        if (Auth::user()->hasPermission('tags.list')) {
            $tags = Menu::generateSelect([
                'model' => $this->app->make(TagInterface::class)->getModel(),
                'screen' => TAG_MODULE_SCREEN_NAME,
                'theme' => false,
                'options' => [
                    'class' => 'list-item',
                ],
            ]);
            echo view('plugins.blog::tags.partials.menu-options', compact('tags'));
        }
    }

    /**
     * @param array $widgets
     * @param Collection $widget_settings
     * @return array
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function registerDashboardWidgets($widgets, $widget_settings)
    {
        if (!Auth::user()->hasPermission('posts.list')) {
            return $widgets;
        }
        Assets::addJavascriptDirectly(['/vendor/core/plugins/blog/js/blog.js']);

        $widget = $widget_settings->where('name', 'widget_posts_recent')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_posts_recent']);
        }

        $widget->title = trans('plugins.blog::posts.widget_posts_recent');
        $widget->icon = 'fas fa-edit';
        $widget->color = '#f3c200';

        $data = [
            'id' => $widget->id,
            'view' => view('plugins.blog::posts.widgets.base', compact('widget', 'widget_setting'))->render(),
        ];

        if (empty($widget_setting) || array_key_exists($widget_setting->order, $widgets)) {
            $widgets[] = $data;
        } else {
            $widgets[$widget_setting->order] = $data;
        }
        return $widgets;
    }

    /**
     * @param Eloquent $slug
     * @return array|Eloquent
     * @author Sang Nguyen
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handleSingleView($slug)
    {
        if ($slug instanceof Eloquent) {
            $data = [];
            switch ($slug->reference) {
                case POST_MODULE_SCREEN_NAME:
                    $post = $this->app->make(PostInterface::class)->getFirstBy(['id' => $slug->reference_id, 'status' => 1]);
                    if (!empty($post)) {
                        Helper::handleViewCount($post, 'viewed_post');

                        SeoHelper::setTitle($post->name)->setDescription($post->description);

                        $meta = new SeoOpenGraph();
                        if ($post->image) {
                            $meta->setImage(url($post->image));
                        }
                        $meta->setDescription($post->description);
                        $meta->setUrl(route('public.single', $slug->key));
                        $meta->setTitle($post->name);
                        $meta->setType('article');

                        SeoHelper::setSeoOpenGraph($meta);

                        admin_bar()->registerLink(trans('plugins.blog::posts.edit_this_post'), route('posts.edit', $post->id));

                        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add($post->name, route('public.single', $slug->key));

                        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, POST_MODULE_SCREEN_NAME, $post);

                        $data = [
                            'view' => 'post',
                            'default_view' => 'plugins.blog::themes.post',
                            'data' => compact('post'),
                            'slug' => $post->slug,
                        ];
                    }
                    break;
                case CATEGORY_MODULE_SCREEN_NAME:
                    $category = $this->app->make(CategoryInterface::class)->getFirstBy(['id' => $slug->reference_id, 'status' => 1]);
                    if (!empty($category)) {
                        SeoHelper::setTitle($category->name)->setDescription($category->description);

                        $meta = new SeoOpenGraph();
                        if ($category->image) {
                            $meta->setImage(url($category->image));
                        }
                        $meta->setDescription($category->description);
                        $meta->setUrl(route('public.single', $slug->key));
                        $meta->setTitle($category->name);
                        $meta->setType('article');

                        SeoHelper::setSeoOpenGraph($meta);

                        admin_bar()->registerLink(trans('plugins.blog::categories.edit_this_category'), route('categories.edit', $category->id));

                        $allRelatedCategoryIds = array_unique(array_merge($this->app->make(CategoryInterface::class)->getAllRelatedChildrenIds($category), [$category->id]));

                        $posts = $this->app->make(PostInterface::class)->getByCategory($allRelatedCategoryIds, 12);

                        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add($category->name, route('public.single', $slug->key));

                        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, CATEGORY_MODULE_SCREEN_NAME, $category);

                        return [
                            'view' => 'category',
                            'default_view' => 'plugins.blog::themes.category',
                            'data' => compact('category', 'posts'),
                            'slug' => $category->slug,
                        ];
                    }
                    break;
            }
            if (!empty($data)) {
                return $data;
            }
        }

        return $slug;
    }

    /**
     * @param \stdClass $shortcode
     * @return \Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Throwable
     */
    public function renderBlogPosts($shortcode)
    {
        $query = $this->app->make(PostInterface::class)
            ->getModel()
            ->select('posts.*')
            ->where(['posts.status' => 1]);

        $posts = $this->app->make(PostInterface::class)->applyBeforeExecuteQuery($query, POST_MODULE_SCREEN_NAME)->paginate($shortcode->paginate ?? 12);

        $view = 'plugins.blog::themes.templates.posts';
        $theme_view = 'theme.' . setting('theme') . '::views.templates.posts';
        if (view()->exists($theme_view)) {
            $view = $theme_view;
        }
        return view($view, compact('posts'))->render();
    }
}
