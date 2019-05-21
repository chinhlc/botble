let mix = require('laravel-mix');

const publicPath = 'public/vendor/core/plugins/translation';
const resourcePath = './plugins/translation';

mix
    .js(resourcePath + '/resources/assets/js/translation.js', publicPath + '/js')
    .copy(publicPath + '/js/translation.js', resourcePath + '/public/js')

    .sass(resourcePath + '/resources/assets/sass/translation.scss', publicPath + '/css')
    .copy(publicPath + '/css/translation.css', resourcePath + '/public/css');