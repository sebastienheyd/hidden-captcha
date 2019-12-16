<?php

namespace SebastienHeyd\HiddenCaptcha;

use Crypt;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class HiddenCaptcha
{
    /**
     * Set the hidden captcha tags to put in your form.
     *
     * @return string
     */
    public static function render()
    {
        return (string) view('hiddenCaptcha::captcha', ['random' => Str::random(16)]);
    }

    /**
     * Check the hidden captcha values.
     *
     * @param Validator $validator
     * @param int       $minLimit
     * @param int       $maxLimit
     *
     * @return bool
     */
    public static function check(Validator $validator, $minLimit = null, $maxLimit = null)
    {
        $formData = $validator->getData();

        // Check post values
        if (!isset($formData['_captcha']) || !($token = self::getToken($formData['_captcha']))) {
            return false;
        }

        // Check time limits
        $now = time();
        $min = $minLimit ? $minLimit : config('hidden_captcha.min_submit_time');
        $max = $maxLimit ? $maxLimit : config('hidden_captcha.max_submit_time');
        if ($now - $token['timestamp'] < $min || $now - $token['timestamp'] > $max) {
            return false;
        }

        // Check the random posted field
        if (empty($formData[$token['random_field_name']])) {
            return false;
        }

        // Check if the random field value is similar to the token value
        $randomField = $formData[$token['random_field_name']];
        if (!ctype_digit($randomField) || $token['timestamp'] != $randomField) {
            return false;
        }

        // Everything is ok, return true
        return true;
    }

    /**
     * Get and check the token values.
     *
     * @param string $captcha
     *
     * @return string|bool
     */
    private static function getToken($captcha)
    {
        // Get the token values
        try {
            $token = Crypt::decrypt($captcha);
        } catch (\Exception $exception) {
            return false;
        }

        $token = @unserialize($token);

        // Token is null or unserializable
        if (!$token || !is_array($token) || empty($token)) {
            return false;
        }

        // Check token values
        if (empty($token['session_id']) ||
            empty($token['ip']) ||
            empty($token['user_agent']) ||
            $token['session_id'] !== session()->getId() ||
            $token['ip'] !== request()->ip() ||
            $token['user_agent'] !== request()->header('User-Agent')
        ) {
            return false;
        }

        return $token;
    }
}
