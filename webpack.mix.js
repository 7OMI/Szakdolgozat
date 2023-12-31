const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .options({
        processCssUrls: false
    })
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css', {
        sassOptions: {
            outputStyle: 'compressed'
        }
    })
    .copy('resources/js/lib/', 'public/js/lib/', false)
    .copy('resources/js/plugin/', 'public/js/plugin/', false)
    .copy('resources/fonts/', 'public/fonts/', false)
;
