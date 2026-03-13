const mix = require('laravel-mix')

mix.js('resources/js/app.js', 'public/js').vue()
    .sass('resources/sass/app.scss', 'public/css')
const path = require('path')
mix.alias({
    'ziggy-js': path.resolve('vendor/tightenco/ziggy/dist'),
})
