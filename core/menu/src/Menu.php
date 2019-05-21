<?php

namespace Botble\Menu;

use Botble\Menu\Repositories\Eloquent\MenuRepository;
use Botble\Menu\Repositories\Interfaces\MenuInterface;
use Botble\Menu\Repositories\Interfaces\MenuNodeInterface;
use Botble\Support\Services\Cache\Cache;
use Collective\Html\HtmlBuilder;
use Exception;
use Illuminate\Cache\CacheManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Schema;
use Theme;

class Menu
{
    /**
     * @var mixed
     */
    protected $menuRepository;

    /**
     * @var HtmlBuilder
     */
    protected $html;

    /**
     * @var MenuNodeInterface
     */
    protected $menuNodeRepository;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var array
     */
    protected $related_route_names = [];

    /**
     * Menu constructor.
     * @param MenuInterface $menu
     * @param HtmlBuilder $html
     * @param MenuNodeInterface $menuNodeRepository
     * @param CacheManager $cache
     * @author Sang Nguyen
     */
    public function __construct(
        MenuInterface $menu,
        HtmlBuilder $html,
        MenuNodeInterface $menuNodeRepository,
        CacheManager $cache
    )
    {
        $this->menuRepository = $menu;
        $this->html = $html;
        $this->menuNodeRepository = $menuNodeRepository;
        $this->cache = new Cache($cache, MenuRepository::class);
    }

    /**
     * @param $name
     * @param $value
     * @author Sang Nguyen
     * @return $this
     */
    public function addRelatedRouteName($name, $value): self
    {
        $this->related_route_names[$name] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getRelatedRouteNames()
    {
        return $this->related_route_names;
    }

    /**
     * @param $args
     * @return mixed|null|string
     * @author Sang Nguyen, Tedozi Manson
     * @throws \Throwable
     */
    public function generateMenu($args = [])
    {
        $slug = array_get($args, 'slug');
        if (!$slug) {
            return null;
        }

        $view = array_get($args, 'view');
        $theme = array_get($args, 'theme', true);

        $cache_key = md5('cache-menu-' . serialize($args));
        if (!$this->cache->has($cache_key) || env('CACHE_FRONTEND_MENU', false) == false) {
            $parent_id = array_get($args, 'parent_id', 0);
            $active = array_get($args, 'active', true);
            $options = $this->html->attributes(array_get($args, 'options', []));

            if ($slug instanceof Model) {
                $menu = $slug;
                if (empty($menu)) {
                    return null;
                }
                $menu_nodes = $menu->child;
            } else {
                $menu = $this->menuRepository->findBySlug($slug, $active, ['menus.id', 'menus.slug']);

                if (!$menu) {
                    return null;
                }

                $menu_nodes = $this->menuNodeRepository->getByMenuId($menu->id, $parent_id, [
                    'menu_nodes.id',
                    'menu_nodes.menu_id',
                    'menu_nodes.parent_id',
                    'menu_nodes.related_id',
                    'menu_nodes.icon_font',
                    'menu_nodes.css_class',
                    'menu_nodes.target',
                    'menu_nodes.url',
                    'menu_nodes.title',
                    'menu_nodes.type',
                    'menu_nodes.has_child',
                    'slugs.key',
                ]);
            }

            $data = compact('menu_nodes', 'menu');
            $this->cache->put($cache_key, $data);
        } else {
            $data = $this->cache->get($cache_key);
            $options = $this->html->attributes(array_get($args, 'options', []));
        }

        $data['options'] = $options;

        if ($theme && $view) {
            return Theme::partial($view, $data);
        } elseif ($view) {
            return view($view, $data)->render();
        }
        return view('core.menu::partials.default', $data)->render();
    }

    /**
     * @param array $args
     * @return mixed|null|string
     * @author Sang Nguyen, Tedozi Manson
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Throwable
     */
    public function generateSelect($args = [])
    {
        $model = array_get($args, 'model');
        if (!$model) {
            return null;
        }

        $view = array_get($args, 'view');
        $theme = array_get($args, 'theme', true);
        $screen = array_get($args, 'screen');

        $cache_key = md5('cache-menu-' . serialize($args));
        if (!$this->cache->has($cache_key) || true) {
            $parent_id = array_get($args, 'parent_id', 0);
            $active = array_get($args, 'active', true);
            $options = $this->html->attributes(array_get($args, 'options', []));

            /**
             * @var \Eloquent $object
             */
            if (Schema::hasColumn($model->getTable(), 'parent_id')) {
                $object = $model->whereParentId($parent_id)->orderBy('name', 'asc');
            } else {
                $object = $model->orderBy('name', 'asc');
            }
            if ($active) {
                $object = $object->where('status', $active);
            }
            $object = apply_filters(BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM, $object, $model, $screen)->get();

            if (empty($object)) {
                return null;
            }

            $data = compact('object', 'model', 'options', 'screen');

            $this->cache->put($cache_key, $data);
        } else {
            $data = $this->cache->get($cache_key);
        }

        if (!array_get($data, 'object') || ($data['object'] instanceof Collection && $data['object']->isEmpty())) {
            return null;
        }

        if ($theme && $view) {
            return Theme::partial($view, $data);
        } elseif ($view) {
            return view($view, $data)->render();
        }
        return view('core.menu::partials.select', $data)->render();
    }

    /**
     * @param $slug
     * @param $active
     * @return bool
     * @author Sang Nguyen
     */
    public function hasMenu($slug, $active)
    {
        $menu = $this->menuRepository->findBySlug($slug, $active);
        if (!$menu) {
            return false;
        }
        return true;
    }

    /**
     * @param $menu_nodes
     * @param $menu_id
     * @param $parent_id
     * @author Sang Nguyen, Tedozi Manson
     */
    public function recursiveSaveMenu($menu_nodes, $menu_id, $parent_id)
    {
        try {
            foreach ($menu_nodes as $row) {
                $child = array_get($row, 'children', []);
                $has_child = 0;
                if (!empty($child)) {
                    $has_child = 1;
                }
                $parent = $this->saveMenuNode($row, $menu_id, $parent_id, $has_child);
                if (!empty($parent)) {
                    $this->recursiveSaveMenu($child, $menu_id, $parent);
                }
            }
        } catch (Exception $ex) {
            info($ex->getMessage());
        }
    }

    /**
     * @param $menu_item
     * @param $menu_id
     * @param $parent_id
     * @param int $has_child
     * @return int
     * @author Sang Nguyen, Tedozi Manson
     */
    protected function saveMenuNode($menu_item, $menu_id, $parent_id, $has_child = 0)
    {
        $item = $this->menuNodeRepository->findById(array_get($menu_item, 'id'));
        if (!$item) {
            $item = $this->menuNodeRepository->getModel();
        }

        $item->title = array_get($menu_item, 'title');
        $item->url = array_get($menu_item, 'customUrl');
        $item->css_class = array_get($menu_item, 'class');
        $item->position = array_get($menu_item, 'position');
        $item->icon_font = array_get($menu_item, 'iconFont');
        $item->target = array_get($menu_item, 'target');
        $item->type = array_get($menu_item, 'type');
        $item->menu_id = $menu_id;
        $item->parent_id = $parent_id;
        $item->has_child = $has_child;

        switch ($item->type) {
            case 'custom-link':
                $item->related_id = 0;
                break;
            default:
                $item->related_id = (int)array_get($menu_item, 'relatedId');
                break;
        }
        $this->menuNodeRepository->createOrUpdate($item);

        return $item->id;
    }
}
