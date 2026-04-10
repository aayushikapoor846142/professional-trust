@php
// Fetching reCAPTCHA secret and site key from the database
$secret = apiKeys('RECAPTCHA_SECRET'); // Fetch the secret key from the database
$sitekey = apiKeys('RECAPTCHA_SITEKEY'); // Fetch the site key from the database
// Initialize reCAPTCHA using the fetched keys
$captcha = new \Anhskohbo\NoCaptcha\NoCaptcha($secret, $sitekey);
@endphp
{!! $captcha->renderJs() !!}
{!! $captcha->display() !!}

