<?php

namespace Botble\Base\Providers;

use Breadcrumbs;
use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;
use Illuminate\Support\ServiceProvider;
use Route;
use URL;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * @author Sang Nguyen
     * @throws \DaveJamesMiller\Breadcrumbs\Exceptions\DuplicateBreadcrumbException
     */
    public function boot()
    {
        Breadcrumbs::register('', function (BreadcrumbsGenerator $breadcrumbs) {
            $breadcrumbs->push('', '');
        });

        Breadcrumbs::register('dashboard.index', function (BreadcrumbsGenerator $breadcrumbs) {
            $breadcrumbs->push(trans('core.base::layouts.dashboard'), route('dashboard.index'));
        });

        /**
         * Register breadcrumbs based on menu stored in session
         * @author Sang Nguyen
         */
        Breadcrumbs::register('main', function (BreadcrumbsGenerator $breadcrumbs, $defaultTitle = null) {
            $prefix = '/' . ltrim($this->app->make('request')->route()->getPrefix(), '/');
            $url = URL::current();
            $site_title = setting('admin_title', config('core.base.general.base_name'));
            $arMenu = dashboard_menu()->getAll();
            if (Route::currentRouteName() != 'dashboard.index') {
                $breadcrumbs->parent('dashboard.index');
            }
            $found = false;
            foreach ($arMenu as $menuCategory) {
                if (($url == $menuCategory->url ||
                        (str_contains($menuCategory->url, $prefix) &&
                            $prefix != '//')) &&
                    !empty($menuCategory->name)
                ) {
                    $found = true;
                    $breadcrumbs->push($menuCategory->name, $url);
                    if ($defaultTitle != $menuCategory->name && $defaultTitle != $site_title) {
                        $breadcrumbs->push($defaultTitle, $url);
                    }
                    break;
                }
            }
            if (!$found) {
                foreach ($arMenu as $menuCategory) {
                    if (!$menuCategory->children->isEmpty()) {
                        foreach ($menuCategory->children as $menuItem) {
                            if (($url == $menuItem->url ||
                                    (str_contains($menuItem->url, $prefix) &&
                                        $prefix != '//')) &&
                                !empty($menuItem->name)
                            ) {
                                $found = true;
                                $breadcrumbs->push($menuCategory->name, $menuCategory->url);
                                $breadcrumbs->push($menuItem->name, $menuItem->url);
                                if ($defaultTitle != $menuItem->name && $defaultTitle != $site_title) {
                                    $breadcrumbs->push($defaultTitle, $url);
                                }
                                break;
                            }
                        }
                    }
                }
            }

            if (!$found) {
                $breadcrumbs->push($defaultTitle, $url);
            }
        });
    }
}
