<?php

namespace Botble\Gallery\Providers;

use Assets;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @throws \Throwable
     */
    public function boot()
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addGalleryBox'], 13, 3);

        if (function_exists('shortcode')) {
            add_shortcode('gallery', trans('plugins/gallery::gallery.gallery_images'),
                trans('plugins/gallery::gallery.add_gallery_short_code'), [$this, 'render']);
            shortcode()->setAdminConfig('gallery', view('plugins/gallery::partials.short-code-admin-config')->render());
        }
    }

    /**
     * @param string $screen
     * @param $priority
     * @param $object
     */
    public function addGalleryBox(string $screen, $priority, $object)
    {
        if ($object && in_array(get_class($object), config('plugins.gallery.general.supported'))) {
            Assets::addStylesDirectly(['vendor/core/plugins/gallery/css/admin-gallery.css'])
                ->addScripts(['sortable']);

            add_meta_box('gallery_wrap', trans('plugins/gallery::gallery.gallery_box'), [$this, 'galleryMetaField'], $screen, 'advanced', 'default');
        }
    }

    /**
     * @throws \Throwable
     * @return string
     */
    public function galleryMetaField()
    {
        $value = null;
        $args = func_get_args();
        if ($args[0] && $args[0]->id) {
            $value = gallery_meta_data($args[0]);
        }

        return view('plugins/gallery::gallery-box', compact('value'))->render();
    }

    /**
     * @param $shortcode
     * @return string
     */
    public function render($shortcode)
    {
        return render_galleries($shortcode->limit ? $shortcode->limit : 6);
    }
}
