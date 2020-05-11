let mix = require('laravel-mix')
const tailwindcss = require('tailwindcss')

mix.setPublicPath('dist')
    .js('resources/js/tool.js', 'js')
    .options({
        processCssUrls: false,
        postCss: [ tailwindcss('tailwind.config.js') ],
    })
