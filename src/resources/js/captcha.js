window.hdCptch = function () {
    let captchas = document.querySelectorAll('input[name="_captcha"');

    for (let i = 0; i < captchas.length; i++) {
        let captcha = captchas[i];
        if (captcha.getAttribute('value') !== null) {
            continue;
        }

        let csrf = captcha.getAttribute('data-csrf');
        let random = captcha.nextElementSibling.getAttribute('name');
        let src = document.getElementById('cptch-js').getAttribute('src');

        sha256(random + csrf + src + 'hiddencaptcha').then(function (hash) {
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

            xhr.send('name=' + random);
        })
    }
}

// Fix for MS Edge
if (typeof TextEncoder === 'undefined') {
    var TextEncoder = function TextEncoder()
    {}

    TextEncoder.prototype.encode = function (s) {
        const e = new Uint8Array(s.length);

        for (let i = 0; i < s.length; i += 1) {
            e[i] = s.charCodeAt(i);
        }

        return e;
    }
}

async function sha256(message)
{
    const msgBuffer = new TextEncoder().encode(message);
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    return hashArray.map(b => ('00' + b.toString(16)).slice(-2)).join('');
}

window.hdCptch();
