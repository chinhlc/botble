<?php

return [
    'admin_dir' => env('ADMIN_DIR', 'admin'),
    'plugin-default-img' => '/vendor/core/images/plugin.png',
    'plugin_path' => base_path('plugins'),
    'media-default-img' => '/vendor/core/images/default-image.jpg',
    'upload' => [
        'base_dir' => public_path('uploads'),
    ],
    'default-theme' => env('DEFAULT_THEME', 'default'),
    'base_name' => env('APP_NAME', 'Botble Technologies / <a target="_blank" href="https://bit.ly/2GoaF65">Free script`s</a>'),
    'logo' => '/vendor/core/images/logo_white.png',
    'favicon' => '/vendor/core/images/favicon.png',
    'editor' => [
        'ckeditor' => [
            'js' => [
                '/vendor/core/packages/ckeditor/ckeditor.js',
            ],
        ],
        'tinymce' => [
            'js' => [
                '/vendor/core/packages/tinymce/tinymce.min.js',
            ],
        ],
        'primary' => env('PRIMARY_EDITOR', 'ckeditor'),
    ],
    'email_template' => 'core.base::system.email',
    'error_reporting' => [
        'to' => null,
        'via_slack' => env('SLACK_REPORT_ENABLED', false),
        'ignored_bots' => [
            'googlebot',        // Googlebot
            'bingbot',          // Microsoft Bingbot
            'slurp',            // Yahoo! Slurp
            'ia_archiver',      // Alexa
        ],
    ],
    'enable_https_support' => env('ENABLE_HTTPS_SUPPORT', false),
    'date_format' => [
        'date' => 'Y-m-d',
        'date_time' => 'Y-m-d H:i:s',
        'js' => [
            'date' => 'yyyy-mm-dd',
            'date_time' => 'yyyy-mm-dd H:i:s',
        ],
    ],
    'cache_site_map' => env('ENABLE_CACHE_SITE_MAP', false),
    'public_single_ending_url' => env('PUBLIC_SINGLE_ENDING_URL', null),
];
