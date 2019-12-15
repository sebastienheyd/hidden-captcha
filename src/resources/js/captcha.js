document.addEventListener('DOMContentLoaded', function () {
    let captchas = document.querySelectorAll('input[name="_captcha"');

    captchas.forEach(function (captcha) {
        let csrf = captcha.getAttribute('data-csrf');
        let random = captcha.nextElementSibling.getAttribute('name');

        sha256(random+csrf+'hiddencaptcha').then(function (hash) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', "/captcha-token");
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader("X-CSRF-TOKEN", csrf);
            xhr.setRequestHeader("X-SIGNATURE", hash);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let json = JSON.parse(xhr.responseText);
                    captcha.setAttribute('value', json.token);
                    captcha.nextElementSibling.setAttribute('value', json.ts);
                }
            };

            xhr.send('name='+random);
        })

    });
});

async function sha256(message)
{
    const msgBuffer = new TextEncoder().encode(message);
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => ('00' + b.toString(16)).slice(-2)).join('');
    return hashHex;
}