<?php

namespace SebastienHeyd\HiddenCaptcha\Tests;

use Crypt;
use HiddenCaptcha;
use Session;
use Validator;

/**
 * Class CaptchaTest.
 *
 * @property \Illuminate\Validation\Validator $validator
 */
class CaptchaTest extends TestCase
{
    private $ts;

    public function testHiddenCaptcha()
    {
        Session::start();
        $this->app->instance('path.public', realpath(__DIR__.'/../src/public'));

        $render = HiddenCaptcha::render();
        $this->assertTrue(preg_match('#^<input type="hidden" name="_captcha" data-csrf="(.*?)" /><input type="hidden" name="(.*?)" />#', $render, $m) == true);
        $csrf = $m[1];
        $random = $m[2];

        $response = $this->post('/captcha-token', ['name' => $random], [
                'X-SIGNATURE' => hash('sha256', $random.$csrf.'hiddencaptcha'),
        ])->content();

        $json = json_decode($response);
        $ts = $json->ts;
        $token = $json->token;

        // One field is missing
        $this->assertFalse($this->check([$random => $ts]));
        $this->assertFalse($this->check(['_captcha' => $token]));

        // Invalid value
        $this->assertFalse($this->check(['_captcha'.'invalid' => $token, $random => $ts]));
        $this->assertFalse($this->check(['_captcha' => $token.'invalid', $random => $ts]));
        $this->assertFalse($this->check(['_captcha' => $token, $random.'invalid' => $ts]));
        $this->assertFalse($this->check(['_captcha' => $token, $random => $ts.'invalid']));

        // Time limit
        $this->assertFalse($this->check(['_captcha' => $token, $random => $ts], ':5,60'));
        sleep(2);
        $this->assertFalse($this->check(['_captcha' => $token, $random => $ts], ':0,1'));

        // Everything is ok
        $this->assertTrue($this->check(['_captcha' => $token, $random => $ts]));

        // Fake Token OK
        $this->ts = time();
        $ua = request()->header('User-Agent');
        
        $token = $this->fakeToken('127.0.0.1', $ua);
        $this->assertTrue($this->check(['_captcha' => $token, 'randomField' => $this->ts]));

        // Invalid token values
        $token = $this->fakeToken('1.2.3.4', $ua); // Another IP
        $this->assertFalse($this->check(['_captcha' => $token, 'randomField' => $this->ts]));
        $token = $this->fakeToken('127.0.0.1', 'Chrome'); // Another user agent
        $this->assertFalse($this->check(['_captcha' => $token, 'randomField' => $this->ts]));
        $token = $this->fakeToken('127.0.0.1', $ua); // Another session id
        session()->regenerate(true);
        $this->assertFalse($this->check(['_captcha' => $token, 'randomField' => $this->ts]));
    }

    private function check($post, $param = '')
    {
        return Validator::make($post, ['captcha' => 'hiddencaptcha'.$param])->passes();
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
