# Hidden captcha for Laravel 5

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
