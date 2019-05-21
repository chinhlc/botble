let mix = require('laravel-mix');

const publicPath = 'public/vendor/core/plugins/social-login';
const resourcePath = './plugins/social-login';

mix
    .js(resourcePath + '/resources/assets/js/social-login.js', publicPath + '/js')
    .copy(publicPath + '/js/social-login.js', resourcePath + '/public/js');