<input type="hidden" name="_captcha" data-csrf="{{ csrf_token() }}" /><input type="hidden" name="{{ $random }}" />
<input type="text" name="search_username" aria-label="Name" style="position:fixed;left:-{{ rand(10000,30000) }}px" autocomplete="off"/>
<script>
@if(!defined('LOAD_HIDDEN_CAPTCHA'))
    if(document.getElementById('cptch-js')===null) {var s=document.createElement('script');s.id="cptch-js";s.src="{{ mix('captcha.min.js', '/assets/vendor/hidden-captcha') }}";document.head.appendChild(s);}
    @php(define('LOAD_HIDDEN_CAPTCHA', true))
    if(typeof hdCptch!=='undefined') { hdCptch() }
@endif
</script>
