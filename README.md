# Fully hidden captcha for Laravel 5

![Package](https://img.shields.io/badge/Package-sebastienheyd%2Fhidden--captcha-yellowgreen.svg)
![Laravel](https://img.shields.io/badge/For-Laravel%205.x-yellow.svg)
![Release](https://img.shields.io/github/release/sebastienheyd/hidden-captcha.svg)
![Nb downloads](https://img.shields.io/packagist/dt/sebastienheyd/hidden-captcha.svg)
![Packagist](https://img.shields.io/packagist/v/sebastienheyd/hidden-captcha.svg)
![License](https://img.shields.io/github/license/sebastienheyd/hidden-captcha.svg)

Spam protection solution for Laravel 5.x. Based on several strategies to block the vast majority of spam bots, without 
interfering with the user experience.

## How does it work?

HiddenCaptcha will use four checking rules to block spam robots :

- "honeypot" : a field hidden by CSS that must not be filled in
- an encrypted token containing the user's IP, current session id, current user agent and a random string
- a randomly named required field (use the random string in the token)
- a time limit (10 minutes by default)

## Does it work in any cases ?

No, this solution can be countered by retrieving data from fields by parsing the HTML. After retrieving the data and the 
session token, it's possible to use Curl to post the form with the data generated by HiddenCaptcha.

However, the session id, ip and user agent must be the same and the form must be posted within a specified time frame.

If you receive data, it's certainly because it's a human or a script made specifically to counter this solution. 
Certainly someone is angry with you! :)

## Installation

```json
composer require sebastienheyd/hidden-captcha
```

Extra steps for Laravel < 5.5 :

- Add `SebastienHeyd\HiddenCaptcha\HiddenCaptchaServiceProvider::class,` at the end of the `provider` array in 
`config/app.php`
- Add `"HiddenCaptcha" => SebastienHeyd\HiddenCaptcha\Facades\HiddenCaptcha::class,` at the end of the `aliases` array 
in `config/app.php`

## Usage

1. In your form, in the blade view :

```
@hiddencaptcha
```


2. To check your form, add the following validation rule:
```php
'captcha' => 'hiddencaptcha'
```

## Options

#### Changing time limits

By default, the time limits for submitting a form are 0 second minimum to 1200 seconds maximum (10 minutes). Beyond 
that, hiddencaptcha will not validate the form.

These limits can be changed by declaring them in the validation rule, for example:

`$rules = ['captcha' => 'hiddencaptcha:5,2400'];`

#### Change the name of the field that must be empty

It's possible to change the name of the field that must be empty. If it does not conflict with an existing field in 
your form, it is recommended that you enter a "tempting" name for the bots. Indeed, bots will be more likely to fill in 
a field called "name" than "_username" (default).

```html
@hiddencaptcha('name')
```