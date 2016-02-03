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
    static $CAPTCHA_SPINNER_ERROR = 50;
    static $CAPTCHA_VALUES_NOT_SUBMITTED = 60;

    /**
     * Set the hidden captcha tags to put in your form
     *
     * @param string $formId [optional] The id to use to generate input elements (default = "hcptch")
     * @return string
     */
    public static function input($formId = 'hcptch')
    {
        // Get spinner options
        $now = time();
        $name = substr(md5(rand(0, 1000000)), 0, 16);

        // Generate the spinner
        $spinner = array(
            'timestamp' => $now,
            'session_id' => session_id(),
            'ip' => Request::ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'hfield_name' => $name
        );

        // Encrypt the spinner
        $spinner = Crypt::encrypt(serialize($spinner));

        // put a random invisible style, to fool spambots a little bit more
        $styles = ['position:absolute;left:-' . mt_rand(10000, 20000) . 'px;', 'display: none'];
        $style = $styles[array_rand($styles)];

        // build tags
        $tags = '<input type="hidden" name="' . $formId . '[spinner]" value="' . $spinner . '" />' . PHP_EOL;
        $tags .= '<span style="' . $style . '"><input type="text" name="' . $formId . '[name]" value=""/></span>' . PHP_EOL;
        $tags .= '<input type="hidden" name="' . $formId . '[' . $name . ']" value="' . $now . '" />' . PHP_EOL;

        echo $tags;
    }

    /**
     * Check the hidden captcha's values
     *
     * @param string $formId [optional] The id to use to generate input elements (default = "hcptch")
     * @param integer $minLimit [optional] Submission minimum time limit in seconds (default = 0)
     * @param integer $maxLimit [optional] Submission maximum time limit in seconds (default = 1200)
     * @return boolean              Return false if the submitter is a robot
     */
    public static function check($values, $minLimit = 0, $maxLimit = 1200)
    {
        // Check post values
        if ($values === null || !isset($values['spinner']) || !isset($values['name'])) {
            self::$_error = self::$CAPTCHA_VALUES_NOT_SUBMITTED;
            return false;
        }

        // Hidden field is set
        if ($values['name'] !== '') {
            self::$_error = self::$CAPTCHA_SPAMBOT_AUTO_FILL;
            return false;
        }

        // Get the spinner values
        $spinner = Crypt::decrypt($values['spinner']);
        $spinner = @unserialize($spinner);

        // Spinner is null or unserializable
        if (!$spinner || !is_array($spinner) || empty($spinner)) {
            self::$_error = self::$CAPTCHA_SPINNER_ERROR;
            return false;
        }

        // Check the random posted field
        $hField = $values[$spinner['hfield_name']];
        if (!isset($spinner['captcha']) && (!isset($hField) || $hField === '')) {
            self::$_error = self::$CAPTCHA_VALUES_NOT_SUBMITTED;
            return false;
        }

        // Check time limits
        $now = time();

        if ($now - $spinner['timestamp'] < $minLimit || $now - $spinner['timestamp'] > $maxLimit) {
            self::$_error = self::$CAPTCHA_TIME_LIMIT_ERROR;
            return false;
        }

        // We have a classic captcha with an image
        if (isset($spinner['captcha'])) {
            if (strtolower($hField) !== $spinner['captcha']) {
                self::$_error = self::$CAPTCHA_IMAGE_ERROR;
                return false;
            }
        } else {
            // Check if the random field value is similar to the spinner value
            if (!ctype_digit($hField) || $spinner['timestamp'] != $hField) {
                self::$_error = self::$CAPTCHA_HFIELD_ERROR;
                return false;
            }
        }

        // Check spinner values
        if (!isset($spinner['session_id'], $spinner['ip'], $spinner['user_agent']) &&
            $spinner['session_id'] !== session_id &&
            $spinner['ip'] !== Request::ip() &&
            $spinner['user_agent'] !== $_SERVER['HTTP_USER_AGENT']
        ) {
            self::$_error = self::$CAPTCHA_SPINNER_ERROR;
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