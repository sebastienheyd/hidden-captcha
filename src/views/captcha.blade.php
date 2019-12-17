<input type="hidden" name="_captcha" data-csrf="{{ csrf_token() }}" /><input type="hidden" name="{{ $random }}" />
@if(!defined('LOAD_HIDDEN_CAPTCHA'))
    <script>
        if(document.getElementById('captcha-script') === null) {
            var s = document.createElement('script');
            s.id = "captcha-script";
            s.src = "{{ mix('captcha.min.js', '/assets/vendor/hidden-captcha') }}";
            document.head.appendChild(s);
        }
    </script>
    @php(define('LOAD_HIDDEN_CAPTCHA', true))
@endif
<script>if(typeof hiddenCaptcha !== 'undefined') { hiddenCaptcha() }</script>
