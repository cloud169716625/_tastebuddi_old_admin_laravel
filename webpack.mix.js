const mix = require('laravel-mix');
mix.disableNotifications();
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

mix.webpackConfig(webpack => {
    return {
        plugins: [
            new webpack.EnvironmentPlugin (
                ['GOOGLE_PLACES_API_KEY']
            )
        ]
    };
});

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');


mix.js('resources/js/pages/users.js', 'public/js/users');
mix.js('resources/js/pages/categories.js', 'public/js/categories');
mix.js('resources/js/pages/cities.js', 'public/js/cities');
mix.js('resources/js/pages/countries.js', 'public/js/countries');
mix.js('resources/js/pages/items.js', 'public/js/items');
mix.js('resources/js/pages/locations.js', 'public/js/locations');
mix.js('resources/js/pages/reports.js', 'public/js/reports');
mix.js('resources/js/pages/settings.js', 'public/js/settings');
mix.js('resources/js/pages/landing/privacy.js', 'public/js/landing');
mix.js('resources/js/pages/landing/terms.js', 'public/js/landing');
mix.js('resources/js/pages/common/header.js', 'public/js/common');