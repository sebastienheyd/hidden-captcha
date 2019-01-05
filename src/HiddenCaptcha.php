<?php

namespace SebastienHeyd\HiddenCaptcha;

use Illuminate\Validation\Validator;
use Request;
use Crypt;

class HiddenCaptcha
{
    /**
     * Set the hidden captcha tags to put in your form
     *
     * @param string $mustBeEmptyField
     * @return string
     */
    public static function render($mustBeEmptyField = '_username')
    {
        $ts = time();
        $random = str_random(16);

        // Generate the token
        $token = array(
            'timestamp' => $ts,
            'session_id' => session()->getId(),
            'ip' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'random_field_name' => $random,
            'must_be_empty' => $mustBeEmptyField
        );

        // Encrypt the token
        $token = Crypt::encrypt(serialize($token));

        echo <<<HTML
            <input type="hidden" name="_captcha" value="$token" />
            <div style="position:fixed;transform:translateX(-10000px)">
                <label for="$mustBeEmptyField">Name</label>
                <input type="text" name="$mustBeEmptyField" value=""/>
            </div>
            <input type="hidden" name="$random" value="$ts"/>
HTML;
    }

    /**
     * Check the hidden captcha values
     *
     * @param Validator $validator
     * @param integer $minLimit
     * @param integer $maxLimit
     * @return boolean
     */
    public static function check(Validator $validator, $minLimit = 0, $maxLimit = 1200)
    {
        $formData = $validator->getData();

        // Check post values
        if (!isset($formData['_captcha'])) {
            return false;
        }

        // Get the token values
        try {
            $token = Crypt::decrypt($formData['_captcha']);
        } catch (\Exception $exception) {
            return false;
        }

        $token = @unserialize($token);

        // Token is null or unserializable
        if (!$token || !is_array($token) || empty($token)) {
            return false;
        }

        // Hidden "must be empty" field check
        if (!array_key_exists($token['must_be_empty'], $formData) || !empty($formData[$token['must_be_empty']])) {
            return false;
        }

        // Check time limits
        $now = time();
        if ($now - $token['timestamp'] < $minLimit || $now - $token['timestamp'] > $maxLimit) {
            return false;
        }

        // Check the random posted field
        if (!isset($formData[$token['random_field_name']])) {
            return false;
        }

        // Check if the random field value is similar to the token value
        $randomField = $formData[$token['random_field_name']];
        if (!ctype_digit($randomField) || $token['timestamp'] != $randomField) {
            return false;
        }

        // Check token values
        if (!isset($token['session_id'], $token['ip'], $token['user_agent']) &&
            $token['session_id'] !== session()->getId() &&
            $token['ip'] !== Request::ip() &&
            $token['user_agent'] !== Request::header('User-Agent')
        ) {
            return false;
        }

        // everything is ok, return true
        return true;
    }
}