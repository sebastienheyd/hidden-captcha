# Fully hidden captcha for Laravel without reCaptcha

[![Packagist](https://img.shields.io/packagist/v/sebastienheyd/hidden-captcha?style=flat-square)](https://packagist.org/packages/sebastienheyd/hidden-captcha)
[![Build Status](https://travis-ci.org/sebastienheyd/hidden-captcha.svg?branch=master)](https://travis-ci.org/sebastienheyd/hidden-captcha)
[![StyleCI](https://github.styleci.io/repos/51009111/shield?branch=master)](https://github.styleci.io/repos/51009111)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sebastienheyd/hidden-captcha/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sebastienheyd/hidden-captcha/?branch=master)
[![Laravel](https://img.shields.io/badge/Laravel-5.x%20→%209.x-green?logo=Laravel&style=flat-square)](https://laravel.com/)
[![Nb downloads](https://img.shields.io/packagist/dt/sebastienheyd/hidden-captcha.svg)](https://packagist.org/packages/sebastienheyd/hidden-captcha)
[![MIT License](https://img.shields.io/github/license/sebastienheyd/hidden-captcha?style=flat-square)](LICENSE)

Fully hidden spam protection solution for Laravel without reCaptcha. Based on several strategies to block the vast 
majority of spam bots without interfering with the user experience.

## How does it work?

HiddenCaptcha will use three checking rules to block spam robots :

- an encrypted token containing the user's IP, current session id, current user agent and a random string
- a randomly named required field (will use the random string in the token)
- a time limit (10 minutes by default)

The token is retrieved via an ajax call signed with sha256.

## Installation

```json
composer require sebastienheyd/hidden-captcha
```

Publish public assets :

```
php artisan vendor:publish --tag=laravel-assets
```

Extra steps for Laravel < 5.5 :

- Add `SebastienHeyd\HiddenCaptcha\HiddenCaptchaServiceProvider::class,` at the end of the `provider` array in 
`config/app.php`
- Add `"HiddenCaptcha" => SebastienHeyd\HiddenCaptcha\Facades\HiddenCaptcha::class,` at the end of the `aliases` array 
in `config/app.php`

## Usage

In your forms, in the blade view :

```blade
@hiddencaptcha
```

To check your form, add the following validation rule:
```php
'captcha' => 'hiddencaptcha'
```

## Options

#### Changing time limits

By default, the time limits for submitting a form are 0 second minimum to 1200 seconds maximum (10 minutes). Beyond 
that, hiddencaptcha will not validate the form.

These limits can be changed by declaring them in the validation rule, for example:

`$rules = ['captcha' => 'hiddencaptcha:5,2400'];`

You can also publish the configuration file to edit the default time limits :

```
php artisan vendor:publish --tag=captcha-config
```

## Package update (Laravel < 8.6.9)

Hidden-captcha comes with a JS who must be publish. Since you typically will need to overwrite the assets
every time the package is updated, you may use the ```--force``` flag :

```
php artisan vendor:publish --tag=laravel-assets --force
```

To auto update assets each time package is updated, you can add this command to `post-update-cmd` into the
file `composer.json` at the root of your project.

```json
{
    "scripts": {
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --force --ansi"
        ]
    }
}
```