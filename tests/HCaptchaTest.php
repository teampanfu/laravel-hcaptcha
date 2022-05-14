<?php

namespace Panfu\Laravel\HCaptcha\Test;

use Panfu\Laravel\HCaptcha\HCaptcha;
use PHPUnit\Framework\TestCase;

class HCaptchaTest extends TestCase
{
    /**
     * @var HCaptcha
     */
    protected $hcaptcha;

    public function setUp(): void
    {
        $this->hcaptcha = new HCaptcha('{sitekey}', '{secret}');
    }

    public function testValidation(): void
    {
        $response = $this->hcaptcha->validate('mock_token');

        $this->assertEquals(false, $response);
    }

    public function testScript(): void
    {
        $default = '<script src="https://js.hcaptcha.com/1/api.js?" async defer></script>'.PHP_EOL;
        $withLocale = '<script src="https://js.hcaptcha.com/1/api.js?hl=de" async defer></script>'.PHP_EOL;
        $withCallback = '<script src="https://js.hcaptcha.com/1/api.js?onload=myCallback&render=explicit" async defer></script>'.PHP_EOL;
        $withRecaptchaCompat = '<script src="https://js.hcaptcha.com/1/api.js?recaptchacompat=on" async defer></script>'.PHP_EOL;

        $this->assertEquals($default, $this->hcaptcha->script());
        $this->assertEquals($withLocale, $this->hcaptcha->script('de'));
        $this->assertEquals($withCallback, $this->hcaptcha->script(null, true, 'myCallback'));
        $this->assertEquals($withRecaptchaCompat, $this->hcaptcha->script(null, false, null, true));
    }

    public function testDisplay(): void
    {
        $default = '<div class="h-captcha" data-sitekey="{sitekey}"></div>';
        $withAttributes = '<div data-theme="dark" class="h-captcha" data-sitekey="{sitekey}"></div>';

        $this->assertEquals($default, $this->hcaptcha->display());
        $this->assertEquals($withAttributes, $this->hcaptcha->display(['data-theme' => 'dark']));
    }

    public function testButton(): void
    {
        $default = '<button data-callback="onSubmit" class="h-captcha" data-sitekey="{sitekey}">Submit</button>';
        $withAttributes = '<button class="h-captcha mock" data-callback="onSubmit" data-sitekey="{sitekey}">Mock</button>';

        $this->assertEquals($default, $this->hcaptcha->displayButton());
        $this->assertEquals($withAttributes, $this->hcaptcha->displayButton('Mock', ['class' => 'mock']));
    }
}
