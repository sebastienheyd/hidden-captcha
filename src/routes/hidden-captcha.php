<?php

Route::post('captcha-token', [
    'as' => 'captcha.token',
    'middleware' => 'web',
    'uses' => 'SebastienHeyd\HiddenCaptcha\Controllers\HiddenCaptchaController@getToken'
]);
