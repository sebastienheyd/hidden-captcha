<input type="hidden" name="_captcha" data-csrf="{{ csrf_token() }}" /><input type="hidden" name="{{ $random }}" />
@if(!defined('LOAD_HIDDEN_CAPTCHA'))
    <script src="{{ mix('captcha.min.js', '/assets/vendor/hidden-captcha') }}"></script>
    @php(define('LOAD_HIDDEN_CAPTCHA', true))
@endif
