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

const publicPath = 'public/vendor/core';

let fs = require('fs');

let themePath = './core/base/resources/assets/sass/base/themes';
let paths = fs.readdirSync(themePath);
for (let i = 0; i < paths.length; i++) {
    if (paths[i].indexOf('.scss') > 0 && paths[i].charAt(0) !== '_') {
        let file = themePath + '/' + paths[i];
        mix.sass(file, publicPath + '/css/themes')
            .copy(publicPath + '/css/themes/' + paths[i].replace('.scss', '.css'), './core/base/public/css/themes');
    }
}

mix
    .sass('./core/base/resources/assets/sass/core.scss', publicPath + '/css')
    .copy(publicPath + '/css/core.css', 'core/base/public/css')
    .sass('./core/base/resources/assets/sass/custom/admin-bar.scss', publicPath + '/css')
    .copy(publicPath + '/css/admin-bar.css', 'core/base/public/css')
    .sass('./core/base/resources/assets/sass/custom/system-info.scss', publicPath + '/css')
    .copy(publicPath + '/css/system-info.css', 'core/base/public/css')
    .sass('./core/base/resources/assets/sass/custom/email.scss', publicPath + '/css')
    .copy(publicPath + '/css/email.css', 'core/base/public/css')

    .js('core/base/resources/assets/js/app.js', publicPath + '/js')
    .copy(publicPath + '/js/app.js', 'core/base/public/js')
    .js('core/base/resources/assets/js/core.js', publicPath + '/js')
    .copy(publicPath + '/js/core.js', 'core/base/public/js');

// Modules Core
mix
    .js('./core/base/resources/assets/js/app_modules/editor.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/editor.js', 'core/base/public/js/app_modules')
    .js('./core/base/resources/assets/js/app_modules/plugin.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/plugin.js', 'core/base/public/js/app_modules')
    .js('./core/base/resources/assets/js/app_modules/cache.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/cache.js', 'core/base/public/js/app_modules')
    .js('./core/base/resources/assets/js/app_modules/tags.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/tags.js', 'core/base/public/js/app_modules')
    .js('./core/base/resources/assets/js/app_modules/system-info.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/system-info.js', 'core/base/public/js/app_modules')

    .js('./core/setting/resources/assets/js/app_modules/setting.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/setting.js', 'core/setting/public/js/app_modules')
    .sass('./core/setting/resources/assets/sass/setting.scss', publicPath + '/css')
    .copy(publicPath + '/css/setting.css', 'core/setting/public/css')

    .js('./core/table/resources/assets/js/app_modules/table.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/table.js', 'core/table/public/js/app_modules')
    .js('./core/table/resources/assets/js/app_modules/filter.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/filter.js', 'core/table/public/js/app_modules')
    .sass('./core/table/resources/assets/sass/table.scss', publicPath + '/css/components')
    .copy(publicPath + '/css/components/table.css', 'core/table/public/css/components')

    .js('./core/dashboard/resources/assets/js/app_modules/dashboard.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/dashboard.js', 'core/dashboard/public/js/app_modules')

    .js('./core/acl/resources/assets/js/app_modules/profile.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/profile.js', 'core/acl/public/js/app_modules')
    .js('./core/acl/resources/assets/js/app_modules/login.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/login.js', 'core/acl/public/js/app_modules')
    .js('./core/acl/resources/assets/js/app_modules/role.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/role.js', 'core/acl/public/js/app_modules')

    .js('./core/slug/resources/assets/js/app_modules/slug.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/slug.js', 'core/slug/public/js/app_modules')

    .js('./core/menu/resources/assets/js/app_modules/menu.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/menu.js', 'core/menu/public/js/app_modules')

    .js('./core/seo-helper/resources/assets/js/app_modules/seo-helper.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/seo-helper.js', 'core/seo-helper/public/js/app_modules')

    .js('./core/widget/resources/assets/js/app_modules/widget.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/widget.js', 'core/widget/public/js/app_modules')

    .js('./core/theme/resources/assets/js/app_modules/custom-css.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/custom-css.js', 'core/theme/public/js/app_modules')

    .js('./core/theme/resources/assets/js/app_modules/theme-options.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/theme-options.js', 'core/theme/public/js/app_modules')

    .js('./core/theme/resources/assets/js/app_modules/theme.js', publicPath + '/js/app_modules')
    .copy(publicPath + '/js/app_modules/theme.js', 'core/theme/public/js/app_modules')

    .sass('./core/theme/resources/assets/sass/custom-css.scss', publicPath + '/css')
    .copy(publicPath + '/css/custom-css.css', 'core/theme/public/css')

    .sass('./core/installer/resources/assets/sass/style.scss', publicPath + '/installer/css')
    .copy(publicPath + '/installer/css/style.css', 'core/installer/public/css');

// Media
mix
    .sass('./core/media/resources/assets/sass/media.scss', publicPath + '/media/css')
    .copy(publicPath + '/media/css/media.css', 'core/media/public/assets/css')
    .js('./core/media/resources/assets/js/media.js', publicPath + '/media/js')
    .copy(publicPath + '/media/js/media.js', 'core/media/public/assets/js')
    .js('./core/media/resources/assets/js/jquery.addMedia.js', publicPath + '/media/js')
    .copy(publicPath + '/media/js/jquery.addMedia.js', 'core/media/public/assets/js')
    .js('./core/media/resources/assets/js/integrate.js', publicPath + '/media/js')
    .copy(publicPath + '/media/js/integrate.js', 'core/media/public/assets/js');

// JS Validation
mix
    .copy('./vendor/proengsoft/laravel-jsvalidation/public/js/jsvalidation.min.js', publicPath + '/js/app_modules/form-validation.js')
    .copy(publicPath + '/js/app_modules/form-validation.js', 'core/base/public/js/app_modules');

