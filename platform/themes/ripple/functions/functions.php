<?php

use Illuminate\Contracts\Filesystem\FileNotFoundException;

require_once __DIR__ . '/../vendor/autoload.php';

register_sidebar([
    'id'          => 'top_sidebar',
    'name'        => __('Top sidebar'),
    'description' => __('This is top sidebar section'),
]);

register_sidebar([
    'id'          => 'footer_sidebar',
    'name'        => __('Footer sidebar'),
    'description' => __('This is footer sidebar section'),
]);

add_shortcode('google-map', 'Google map', 'Custom map', 'add_google_map_shortcode');

/**
 * @param $shortcode
 * @return mixed
 * @throws \Botble\Theme\Exceptions\UnknownPartialFileException
 * @throws FileNotFoundException
 */
function add_google_map_shortcode($shortcode)
{
    return Theme::partial('google-map', ['address' => $shortcode->content]);
}

try {
    shortcode()->setAdminConfig('google-map', Theme::partial('google-map-admin-config'));
} catch (FileNotFoundException $exception) {
    info($exception->getMessage());
}

add_shortcode('youtube-video', 'Youtube video', 'Add youtube video', 'add_youtube_video_shortcode');

/**
 * @param $shortcode
 * @return mixed
 * @throws \Botble\Theme\Exceptions\UnknownPartialFileException
 * @throws FileNotFoundException
 */
function add_youtube_video_shortcode($shortcode)
{
    return Theme::partial('video', ['url' => $shortcode->content]);
}

try {
    shortcode()->setAdminConfig('youtube-video', Theme::partial('youtube-admin-config'));
} catch (FileNotFoundException $exception) {
    info($exception->getMessage());
}

theme_option()
    ->setArgs(['debug' => config('app.debug')])
    ->setSection([
        'title'      => __('General'),
        'desc'       => __('General settings'),
        'id'         => 'opt-text-subsection-general',
        'subsection' => true,
        'icon'       => 'fa fa-home',
    ])
    ->setField([
        'id'         => 'site_description',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Site description'),
        'attributes' => [
            'name'    => 'site_description',
            'value'   => __('A young team in Vietnam'),
            'options' => [
                'class'        => 'form-control',
                'data-counter' => 255,
            ],
        ],
    ])
    ->setField([
        'id'         => 'address',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Address'),
        'attributes' => [
            'name'    => 'address',
            'value'   => __('Go Vap District, HCM City, Vietnam'),
            'options' => [
                'class'        => 'form-control',
                'data-counter' => 255,
            ],
        ],
    ])
    ->setField([
        'id'         => 'website',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'url',
        'label'      => __('Website'),
        'attributes' => [
            'name'    => 'website',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'data-counter' => 255,
            ],
        ],
    ])
    ->setField([
        'id'         => 'contact_email',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'email',
        'label'      => __('Email'),
        'attributes' => [
            'name'    => 'contact_email',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'data-counter' => 120,
            ],
        ],
    ])
    ->setSection([
        'title'      => __('Logo'),
        'desc'       => __('Change logo'),
        'id'         => 'opt-text-subsection-logo',
        'subsection' => true,
        'icon'       => 'fa fa-image',
        'fields'     => [
            [
                'id'         => 'logo',
                'type'       => 'mediaImage',
                'label'      => __('Logo'),
                'attributes' => [
                    'name'  => 'logo',
                    'value' => null,
                ],
            ],
        ],
    ])
    ->setSection([
        'title'      => __('Social'),
        'desc'       => __('Social links'),
        'id'         => 'opt-text-subsection-social',
        'subsection' => true,
        'icon'       => 'fa fa-share-alt',
    ])
    ->setField([
        'id'         => 'facebook',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Facebook',
        'attributes' => [
            'name'    => 'facebook',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'twitter',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Twitter',
        'attributes' => [
            'name'    => 'twitter',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'youtube',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Youtube',
        'attributes' => [
            'name'    => 'youtube',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ]);

if (!function_exists('register_custom_image_size')) {
    function register_custom_image_size()
    {
        config([
            'media.sizes.featured' => '560x380',
            'media.sizes.medium'   => '540x360',
        ]);
    }
}
add_action('init', 'register_custom_image_size', 178);
