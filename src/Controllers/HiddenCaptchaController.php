<?php

namespace SebastienHeyd\HiddenCaptcha\Controllers;

use App\Http\Controllers\Controller;
use Crypt;
use Illuminate\Http\Request;

class HiddenCaptchaController extends Controller
{
    public function getToken(Request $request)
    {
        $ts = time();

        if (!($name = $request->input('name'))) {
            abort(503);
        }

        if (!($signature = request()->header('x-signature'))) {
            abort(503);
        }

        $mix = mix('captcha.min.js', '/assets/vendor/hidden-captcha');

        if (hash('sha256', $name.csrf_token().$mix.'hiddencaptcha') !== $signature) {
            abort(503);
        }

        // Generate the token
        $token = [
            'timestamp'         => $ts,
            'session_id'        => session()->getId(),
            'ip'                => request()->ip(),
            'user_agent'        => request()->header('User-Agent'),
            'random_field_name' => $name,
        ];

        // Encrypt the token
        $token = Crypt::encrypt(serialize($token));

        return response()->json(['ts' => $ts, 'token' => $token]);
    }
}
