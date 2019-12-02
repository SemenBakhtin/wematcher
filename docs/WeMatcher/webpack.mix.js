const mix = require('laravel-mix');

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

mix.react('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .sass('node_modules/flag-icon-css/sass/flag-icon.scss', 'public/css')
   .postCss('node_modules/@fortawesome/fontawesome-free/css/all.css', 'public/css/fontawesome')
   .less('node_modules/bootstrap-select/less/bootstrap-select.less', 'public/css')
   .styles([
      'node_modules/gijgo/css/gijgo.css'
   ], 'public/css/all.css');
