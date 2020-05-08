let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

const resourcePath = 'platform/packages/plugin-management';
const publicPath = 'public/vendor/core/packages/plugin-management';

mix
    .js(resourcePath + '/resources/assets/js/plugin.js', publicPath + '/js')
    .copy(publicPath + '/js/plugin.js', resourcePath + '/public/js')
    .sass(resourcePath + '/resources/assets/sass/plugin.scss', publicPath + '/css')
    .copy(publicPath + '/css/plugin.css', resourcePath + '/public/css');
