<?php

namespace SebastienHeyd\HiddenCaptcha\Tests;

use Crypt;
use HiddenCaptcha;

/**
 * Class CaptchaTest.
 *
 * @property \Illuminate\Validation\Validator $validator
 */
class CaptchaTest extends TestCase
{
    public function testHiddenCaptcha()
    {
        $render = HiddenCaptcha::render();
        $this->assertTrue(preg_match('#name="_captcha" value="(.*?)"#', $render, $m) == true);
        $token = $m[1];

        $this->assertTrue(preg_match('#<input type="hidden" name="(.*?)" value="(.*?)" \/>$#', $render, $m) == true);
        $random = $m[1];
        $ts = $m[2];

        // One field is missing
        $this->assertFalse($this->check([$random => $ts, '_username' => '']));
        $this->assertFalse($this->check(['_captcha' => $token, '_username' => '']));
        $this->assertFalse($this->check(['_captcha' => $token, $random => $ts]));

        // Invalid value
        $this->assertFalse($this->check(['_captcha'.'invalid' => $token, $random => $ts, '_username' => '']));
        $this->assertFalse($this->check(['_captcha' => $token.'invalid', $random => $ts, '_username' => '']));
        $this->assertFalse($this->check(['_captcha' => $token, $random.'invalid' => $ts, '_username' => '']));
        $this->assertFalse($this->check(['_captcha' => $token, $random => $ts.'invalid', '_username' => '']));
        $this->assertFalse($this->check(['_captcha' => $token, $random => $ts, '_username'.'invalid' => '']));
        $this->assertFalse($this->check(['_captcha' => $token, $random => $ts, '_username' => 'invalid']));

        // Time limit
        $this->assertFalse($this->check(['_captcha' => $token, $random => $ts, '_username' => ''], ':5,60'));
        sleep(2);
        $this->assertFalse($this->check(['_captcha' => $token, $random => $ts, '_username' => ''], ':0,1'));

        // Everything is ok
        $this->assertTrue($this->check(['_captcha' => $token, $random => $ts, '_username' => '']));

        // Fake Token OK
        $this->ts = time();
        $token = $this->fakeToken('127.0.0.1', 'Symfony');
        $this->assertTrue($this->check(['_captcha' => $token, 'randomField' => $this->ts, '_username' => '']));

        // Invalid token values
        $token = $this->fakeToken('1.2.3.4', 'Symfony'); // Another IP
        $this->assertFalse($this->check(['_captcha' => $token, 'randomField' => $this->ts, '_username' => '']));
        $token = $this->fakeToken('127.0.0.1', 'Chrome'); // Another user agent
        $this->assertFalse($this->check(['_captcha' => $token, 'randomField' => $this->ts, '_username' => '']));
        $token = $this->fakeToken('127.0.0.1', 'Symfony'); // Another session id
        session()->regenerate(true);
        $this->assertFalse($this->check(['_captcha' => $token, 'randomField' => $this->ts, '_username' => '']));
    }

    private function check($post, $param = '')
    {
        return $this->validator->make($post, ['captcha' => 'hiddencaptcha'.$param])->passes();
    }

    private function fakeToken($ip, $userAgent)
    {
        $token = [
            'timestamp'         => $this->ts,
            'session_id'        => session()->getId(),
            'ip'                => $ip,
            'user_agent'        => $userAgent,
            'random_field_name' => 'randomField',
            'must_be_empty'     => '_username',
        ];

        return Crypt::encrypt(serialize($token));
    }
}
