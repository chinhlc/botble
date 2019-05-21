<?php

namespace Botble\Page\Providers;

use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\Page\Repositories\Interfaces\PageInterface;
use Botble\SeoHelper\SeoOpenGraph;
use Eloquent;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Menu;
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
     */
    public function boot()
    {
        if (defined('MENU_ACTION_SIDEBAR_OPTIONS')) {
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 10);
        }
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addPageStatsWidget'], 15, 2);
        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 1, 1);
    }

    /**
     * Register sidebar options in menu
     * @throws \Throwable
     */
    public function registerMenuOptions()
    {
        $pages = Menu::generateSelect([
            'model' => $this->app->make(PageInterface::class)->getModel(),
            'screen' => PAGE_MODULE_SCREEN_NAME,
            'theme' => false,
            'options' => [
                'class' => 'list-item',
            ],
        ]);
        echo view('core.page::partials.menu-options', compact('pages'));
    }

    /**
     * @param array $widgets
     * @param Collection $widget_settings
     * @return array
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function addPageStatsWidget($widgets, $widget_settings)
    {
        if (!Auth::user()->hasPermission('pages.list')) {
            return $widgets;
        }

        $widget = $widget_settings->where('name', 'widget_total_pages')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_total_pages']);
        }

        $widget->title = trans('core.page::pages.total_pages');
        $widget->icon = 'far fa-file-alt';
        $widget->color = '#32c5d2';

        $pages = $this->app->make(PageInterface::class)->count(['status' => 1]);

        $widgets['pages'] = [
            'id' => $widget->id,
            'view' => view('core.page::partials.widgets.stats', compact('pages', 'widget_setting'))->render(),
        ];
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
            if ($slug->reference === PAGE_MODULE_SCREEN_NAME) {
                $page = $this->app->make(PageInterface::class)->getFirstBy(['id' => $slug->reference_id, 'status' => 1]);
                if (!empty($page)) {
                    SeoHelper::setTitle($page->name)->setDescription($page->description);

                    $meta = new SeoOpenGraph();
                    if ($page->image) {
                        $meta->setImage(url($page->image));
                    }
                    $meta->setDescription($page->description);
                    $meta->setUrl(route('public.single', $slug->key));
                    $meta->setTitle($page->name);
                    $meta->setType('article');

                    SeoHelper::setSeoOpenGraph($meta);

                    if ($page->template) {
                        Theme::uses(setting('theme'))->layout($page->template);
                    }

                    admin_bar()
                        ->registerLink(trans('core.page::pages.edit_this_page'), route('pages.edit', $page->id));

                    Theme::breadcrumb()
                        ->add(__('Home'), route('public.index'))
                        ->add($page->name, route('public.single', $slug));

                    do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PAGE_MODULE_SCREEN_NAME, $page);

                    $data = [
                        'view' => 'page',
                        'default_view' => 'core.page::themes.page',
                        'data' => compact('page'),
                        'slug' => $page->slug,
                    ];
                }
                if (!empty($data)) {
                    return $data;
                }
            }
        }

        return $slug;
    }
}
