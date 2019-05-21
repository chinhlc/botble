let mix = require('laravel-mix');

const basePath = 'public/themes/ripple/assets';

mix
    .sass(basePath + '/sass/style.scss', basePath + '/css')
    .js(basePath + '/js/ripple.js', basePath + '/js');
