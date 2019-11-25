// const mix = require('laravel-mix');

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

// mix.js('resources/js/app.js', 'public/js')
//     .sass('resources/sass/app.scss', 'public/css');

const path = require('path');
const mix = require('laravel-mix');
const webpack = require('webpack');

mix
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps()
    .version()
    .disableNotifications();

// if (mix.inProduction()) {
//     mix.version();
//
//     mix.extract([
//         // 'vue',
//         // 'vform',
//         'axios',
//         // 'vuex',
//         'jquery',
//         'popper.js',
//         // 'vue-i18n',
//         // 'vue-meta',
//         // 'js-cookie',
//         'bootstrap',
//         // 'vue-router',
//         // 'sweetalert2',
//         // 'vuex-router-sync',
//         // '@fortawesome/vue-fontawesome',
//         // '@fortawesome/fontawesome-svg-core',
//         // 'selectize',
//         // 'dropzone',
//         // 'vanilla-lazyload',
//         // '@fancyapps/fancybox',
//         // 'cropperjs',
//         'toastr'
//     ]);
// }

mix.webpackConfig({
    plugins: [
        new webpack.ProvidePlugin({
            jQuery: 'jquery',
            $:      'jquery'
        })
    ],
    resolve: {
        extensions: ['.js', '.json', '.vue'],
        alias:      {
            '~': path.join(__dirname, './resources/js')
        }
    },
    output:  {
        chunkFilename: 'js/[name].[chunkhash].js',
        publicPath:    mix.config.hmr ? '//localhost:8080' : '/'
    },
    module:  {
        rules: []
    }
});

// mix.browserSync({
//     proxy:
//                {
//                    target: 'https://admin.skidvis.ru',
//                    ws:     true
//                },
//     https:     true,
//     logPrefix: 'admin.skidvis.ru',
//     host:      'admin.skidvis.ru',
//     port:      3015,
//     open:      false,
//     notify:    false,
//     ghostMode: {
//         clicks: true,
//         forms:  true,
//         scroll: true
//     }
// });

// mix.browserSync({proxy: 'localhost:8080'});
