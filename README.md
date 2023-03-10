# hCaptcha package for Laravel

[![Visit hCaptcha.com](https://user-images.githubusercontent.com/59781900/163660320-8209d05d-c7ed-40f3-831b-3dde16904014.png)](https://www.hcaptcha.com/)

[![Latest Version](https://img.shields.io/github/release/teampanfu/laravel-hcaptcha.svg?style=flat-square)](https://github.com/teampanfu/laravel-hcaptcha/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/teampanfu/laravel-hcaptcha.svg?style=flat-square)](https://packagist.org/packages/teampanfu/laravel-hcaptcha)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

A package specifically designed to include [hCaptcha](https://www.hcaptcha.com) directly into [Laravel](https://laravel.com).

## Installation

To install, use [Composer](https://getcomposer.org):

```sh
composer require teampanfu/laravel-hcaptcha
```

## Manual setup

As of Laravel 5.5, packages are discovered automatically via [package discovery](https://laravel.com/docs/9.x/packages#package-discovery). So if you are using a newer version, you can skip these steps.

Add the following to your `config/app.php`:

```php
'providers' => [
    ...

    /*
     * Package Service Providers...
     */
    Panfu\Laravel\HCaptcha\HCaptchaServiceProvider::class,

    ...
],

'aliases' => [
    ...
    'HCaptcha' => Panfu\Laravel\HCaptcha\Facades\HCaptcha::class,
    ...
],
```

Then publish the configuration file:

```sh
php artisan vendor:publish --provider="Panfu\Laravel\HCaptcha\HCaptchaServiceProvider"
```

## Configuration

Add your website in the [hCaptcha dashboard](https://dashboard.hcaptcha.com) to get a site key and secret key.

When you have done that, add the keys to your `.env` file:

```env
HCAPTCHA_SITEKEY=10000000-ffff-ffff-ffff-000000000001
HCAPTCHA_SECRET=0x0000000000000000000000000000000000000000
```

*These are the test keys we use by default. You should not use them in production!*

## Usage

### Display

To display the widget:

```blade
{!! HCaptcha::display() !!}
```

You can also set [custom attributes](https://docs.hcaptcha.com/configuration#hcaptcha-container-configuration) on the widget:

```blade
{!! HCaptcha::display(['data-theme' => 'dark']) !!}
```

Or extend the class:

```blade
{!! HCaptcha::display([
    'class' => $errors->has('email') ? 'is-invalid' : '',
]) !!}
```

### Script

To load the hCaptcha javascript resource:

```blade
{!! HCaptcha::script() !!}
```

You can also set the [query parameters](https://docs.hcaptcha.com/configuration):

```blade
{!! HCaptcha::script($locale, $render, $onload, $recaptchacompat) !!}
```

### Validation

To validate the hCaptcha response, use the `hcaptcha` rule:

```php
$request->validate([
    'h-captcha-response' => ['hcaptcha'],
]);
```

*You can leave out the `required` rule, because it is already checked internally.*

#### Custom validation message

Add the following values to your `validation.php` in the language folder:

```php
'custom' => [
    'h-captcha-response' => [
        'hcaptcha' => 'Please verify that you are human.',
    ]
],
```

### Invisible Captcha

You can also use an [invisible captcha](https://docs.hcaptcha.com/invisible) where the user will only be presented with a hCaptcha challenge if that user meets challenge criteria.

The easiest way is to bind a button to hCaptcha:

```blade
{!! HCaptcha::displayButton() !!}
```

This will generate a button with an `h-captcha` class and the site key. But you still need a callback for the button:

```html
<script>
    function onSubmit(token) {
        document.getElementById('my-form').submit();
    }
</script>
```

By default, `onSubmit` is specified as callback, but you can easily change this (along with the text of the button):

```blade
{!! HCaptcha::displayButton('Submit', ['data-callback' => 'myCustomCallback']) !!}
```

You can also set other [custom attributes](https://docs.hcaptcha.com/configuration#hcaptcha-container-configuration), including `class`.

## Use without Laravel

The package is designed so that it can be used without Laravel. Here is an example of how it works:

```php
<?php

require_once 'vendor/autoload.php';

use Panfu\Laravel\HCaptcha\HCaptcha;

$sitekey = '10000000-ffff-ffff-ffff-000000000001';
$secret = '0x0000000000000000000000000000000000000000';
$hCaptcha = new HCaptcha($sitekey, $secret);

if (! empty($_POST)) {
    var_dump($hCaptcha->validate($_POST['h-captcha-response']));
    exit;
}

?>

<form method="POST">
    <?= $hCaptcha->display() ?>
    <button type="submit">Submit</button>
</form>

<?= $hCaptcha->script() ?>
```

## Testing

```sh
$ ./vendor/bin/phpunit
```

## Contribute

If you find a bug or have a suggestion for a feature, feel free to create a new issue or open a pull request.

We are happy about every contribution!

## License

This package is open-source software licensed under the [MIT License](LICENSE).
