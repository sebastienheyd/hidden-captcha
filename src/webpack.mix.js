const mix = require('laravel-mix');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
require('laravel-mix-obfuscator');

mix.webpackConfig({plugins: [new CleanWebpackPlugin()],stats:'errors-only'})
    .setPublicPath("public/assets/vendor/hidden-captcha")
    .setResourceRoot('/')
    .version();

mix.js('resources/js/captcha.js', 'public/assets/vendor/hidden-captcha/captcha.min.js').obfuscator();
