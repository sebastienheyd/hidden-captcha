# Hidden captcha for Laravel 5

![Package](https://img.shields.io/badge/Package-sebastienheyd%2Fhidden--captcha-yellowgreen.svg)
![Laravel](https://img.shields.io/badge/For-Laravel%205.x-yellow.svg)
![Release](https://img.shields.io/github/release/sebastienheyd/hidden-captcha.svg)
![Nb downloads](https://img.shields.io/packagist/dt/sebastienheyd/hidden-captcha.svg)
![Packagist](https://img.shields.io/packagist/v/sebastienheyd/hidden-captcha.svg)
![License](https://img.shields.io/github/license/sebastienheyd/hidden-captcha.svg)

Spam protection solution for Laravel 5.x. Based on multiple strategies to provide a better user experience.

## How does it work?

Hidden captcha will use 4 checks to stop spam robots :

- "honeypot" : a field hidden by CSS who must not be filled
- a crypted token who contain the user IP, session id, user agent and a random string
- a required random named field (use the crypted random string in the token)
- time limit

## Does it work in all cases ?

No, this solution can be bypassed with curl. Curl can get the fields values simply by reading it and can post them after that.
A spammer who want to send you spams will be able to do that by writing a simple script (or really post the data ;) ), but this solution will stop a large majority of spam bots.

## Installation

Run `composer require sebastienheyd/hidden-captcha` or modify your composer.json:
```json
{
    "require": {
        "sebastienheyd/hidden-captcha": "~1.0"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

#### Laravel 5.0

In `/config/app.php`, add the following to `providers`:
```
'SebastienHeyd\HiddenCaptcha\HiddenCaptchaServiceProvider',
```
and the following to `aliases`:
```
'HiddenCaptcha' => 'SebastienHeyd\HiddenCaptcha\Facades\HiddenCaptcha',
```

#### Laravel >= 5.1

In `/config/app.php`, add the following to `providers`:
```
SebastienHeyd\HiddenCaptcha\HiddenCaptchaServiceProvider::class
```
and the following to `aliases`:
```
'HiddenCaptcha' => SebastienHeyd\HiddenCaptcha\Facades\HiddenCaptcha::class,
```

## Usage

1. In your form, use `{{ HiddenCaptcha::render() }}` to echo out the markup.
2. To validate your form, add the following rule:
```php
$rules = [
    // ...
    'hcptch' => 'required|hiddencaptcha',
];
```

## Options

#### Changing fields name

You can change the generated fields name, for that just change the value when you call render, per example : `{{ HiddenCaptcha::render('my-hidden-captcha') }}`.

Will render something like this :
```html
<input type="hidden" name="my-hidden-captcha['token']" ...>
```

To work, you need to use the same name in the rules array :

`$rules = ['my-hidden-captcha' => 'required|hiddencaptcha'];`

By default, the name is `hcptch`.

#### Changing the time limits

You can change the time limit for submitting the form. By default, the minimum time is 0 seconds and the maximum is 1200 seconds (10 minutes).

For that you can define the parameters into the rules array (here min=5 and max=2400) :

`$rules = ['my-hidden-captcha' => 'required|hiddencaptcha:5,2400'];`
