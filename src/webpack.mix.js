let mix = require('laravel-mix');
let Clean = require('clean-webpack-plugin');
let JavaScriptObfuscator = require('webpack-obfuscator');

mix.webpackConfig({
    plugins: [
        //new Clean(['public'], {verbose: false}),
        //new JavaScriptObfuscator({rotateUnicodeArray: true})
    ]
})
    .setPublicPath("public/assets/vendor/hidden-captcha")
    .setResourceRoot('/');

mix.js('resources/js/captcha.js', 'public/assets/vendor/hidden-captcha/captcha.min.js').version();
