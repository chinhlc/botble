<?php

use Botble\Gallery\Repositories\Interfaces\GalleryInterface;
use Botble\Gallery\Repositories\Interfaces\GalleryMetaInterface;

if (!function_exists('gallery_meta_data')) {
    /**
     * @param $id
     * @param $type
     * @param array $select
     * @return mixed
     * @author Sang Nguyen
     */
    function gallery_meta_data($id, $type, array $select = ['images'])
    {
        $meta = app(GalleryMetaInterface::class)->getFirstBy(['content_id' => $id, 'reference' => $type], $select);
        if (!empty($meta)) {
            return $meta->images ?? [];
        }
        return [];
    }
}

if (!function_exists('get_galleries')) {
    /**
     * @param $limit
     * @return mixed
     * @author Sang Nguyen
     */
    function get_galleries($limit)
    {
        return app(GalleryInterface::class)->getFeaturedGalleries($limit);
    }
}

if (!function_exists('render_galleries')) {
    /**
     * @param $limit
     * @return mixed
     * @author Sang Nguyen
     */
    function render_galleries($limit)
    {
        Gallery::registerAssets();
        return view('plugins.gallery::gallery', compact('limit'));
    }
}

if (!function_exists('get_list_galleries')) {
    /**
     * @param array $condition
     * @return mixed
     * @author Sang Nguyen
     */
    function get_list_galleries(array $condition)
    {
        return app(GalleryInterface::class)->allBy($condition);
    }
}

if (!function_exists('render_object_gallery')) {
    /**
     * @param array $galleries
     * @param string $category
     * @return mixed
     * @author Sang Nguyen
     * @throws Throwable
     */
    function render_object_gallery(array $galleries, $category = null): string
    {
        Theme::asset()->container('footer')->add('owl.carousel', 'vendor/core/plugins/gallery/packages/owl-carousel/owl.carousel.css');
        Theme::asset()->container('footer')->add('object-gallery-css', 'vendor/core/plugins/gallery/css/object-gallery.css');
        Theme::asset()->container('footer')->add('carousel', 'vendor/core/plugins/gallery/packages/owl-carousel/owl.carousel.js', ['jquery']);
        Theme::asset()->container('footer')->add('object-gallery-js', 'vendor/core/plugins/gallery/js/object-gallery.js', ['jquery']);
        return view('plugins.gallery::partials.object-gallery', compact('galleries', 'category'))->render();
    }
}
