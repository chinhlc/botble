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

require('./core/webpack.mix.js');

require('./plugins/backup/webpack.mix.js');
require('./plugins/translation/webpack.mix.js');
require('./plugins/log-viewer/webpack.mix.js');
require('./plugins/analytics/webpack.mix.js');
require('./plugins/audit-log/webpack.mix.js');
require('./plugins/request-log/webpack.mix.js');
require('./plugins/blog/webpack.mix.js');
require('./plugins/language/webpack.mix.js');
require('./plugins/gallery/webpack.mix.js');

require('./plugins/custom-field/webpack.mix.js');
require('./plugins/member/webpack.mix.js');
