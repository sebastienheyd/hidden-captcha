<?php namespace SebastienHeyd\HiddenCaptcha;

use Request;
use Crypt;

class HiddenCaptcha
{
    private static $_error = false;

    // Errors code
    static $CAPTCHA_IMAGE_ERROR = 10;
    static $CAPTCHA_TIME_LIMIT_ERROR = 20;
    static $CAPTCHA_SPAMBOT_AUTO_FILL = 30;
    static $CAPTCHA_HFIELD_ERROR = 40;
    static $CAPTCHA_TOKEN_ERROR = 50;
    static $CAPTCHA_VALUES_NOT_SUBMITTED = 60;

    /**
     * Set the hidden captcha tags to put in your form
     *
     * @param string $formId [optional] The id to use to generate input elements (default = "hcptch")
     * @return string
     */
    public static function render($formId = 'hcptch')
    {
        $now = time();
        $name = substr(md5(rand(0, 1000000)), 0, 16);

        // Generate the token
        $token = array(
            'timestamp' => $now,
            'session_id' => session_id(),
            'ip' => Request::ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'hfield_name' => $name
        );

        // Encrypt the token
        $token = Crypt::encrypt(serialize($token));

        // put a random invisible style, to fool spambots a little bit more
        $styles = ['position:absolute;left:-' . mt_rand(10000, 20000) . 'px;', 'display: none'];
        $style = $styles[array_rand($styles)];

        // build tags
        $tags = '<input type="hidden" name="' . $formId . '[token]" value="' . $token . '" />' . PHP_EOL;
        $tags .= '<span style="' . $style . '"><input type="text" name="' . $formId . '[name]" value=""/></span>' . PHP_EOL;
        $tags .= '<input type="hidden" name="' . $formId . '[' . $name . ']" value="' . $now . '" />' . PHP_EOL;

        echo $tags;
    }

    /**
     * Check the hidden captcha's values
     *
     * @param array $values Posted values
     * @param integer $minLimit [optional] Submission minimum time limit in seconds (default = 0)
     * @param integer $maxLimit [optional] Submission maximum time limit in seconds (default = 1200)
     * @return boolean
     */
    public static function check($values, $minLimit = 0, $maxLimit = 1200)
    {
        // Check post values
        if ($values === null || !isset($values['token']) || !isset($values['name'])) {
            self::$_error = self::$CAPTCHA_VALUES_NOT_SUBMITTED;
            return false;
        }

        // Hidden field is set
        if ($values['name'] !== '') {
            self::$_error = self::$CAPTCHA_SPAMBOT_AUTO_FILL;
            return false;
        }

        // Get the token values
        $token = Crypt::decrypt($values['token']);
        $token = @unserialize($token);

        // Token is null or unserializable
        if (!$token || !is_array($token) || empty($token)) {
            self::$_error = self::$CAPTCHA_TOKEN_ERROR;
            return false;
        }

        // Check the random posted field
        $hField = $values[$token['hfield_name']];
        if (!isset($token['captcha']) && (!isset($hField) || $hField === '')) {
            self::$_error = self::$CAPTCHA_VALUES_NOT_SUBMITTED;
            return false;
        }

        // Check time limits
        $now = time();
        if ($now - $token['timestamp'] < $minLimit || $now - $token['timestamp'] > $maxLimit) {
            self::$_error = self::$CAPTCHA_TIME_LIMIT_ERROR;
            return false;
        }

        // Check if the random field value is similar to the token value
        if (!ctype_digit($hField) || $token['timestamp'] != $hField) {
            self::$_error = self::$CAPTCHA_HFIELD_ERROR;
            return false;
        }

        // Check token values
        if (!isset($token['session_id'], $token['ip'], $token['user_agent']) &&
            $token['session_id'] !== session_id &&
            $token['ip'] !== Request::ip() &&
            $token['user_agent'] !== $_SERVER['HTTP_USER_AGENT']
        ) {
            self::$_error = self::$CAPTCHA_TOKEN_ERROR;
            return false;
        }

        // everything is ok, return true
        return true;
    }

    /**
     * Get the error code
     *
     * @return integer
     */
    public function getError()
    {
        return $this->error;
    }
}