<?php

namespace Panfu\Laravel\HCaptcha;

use GuzzleHttp\Client;

class HCaptcha
{
    /**
     * The endpoint to the hCaptcha API.
     *
     * @var string
     */
    const VERIFY_URL = 'https://hcaptcha.com/siteverify';

    /**
     * The sitekey.
     *
     * @var string
     */
    protected $sitekey;

    /**
     * The secret key.
     *
     * @var string
     */
    protected $secret;

    /**
     * The Guzzle client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The already verified responses.
     *
     * @var array
     */
    protected $verifiedResponses = [];

    /**
     * Create a new HCaptcha instance.
     */
    public function __construct(string $sitekey, string $secret)
    {
        $this->sitekey = $sitekey;
        $this->secret = $secret;
    }

    /**
     * Get the Guzzle client.
     */
    public function getClient(): Client
    {
        if (! $this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Display the hCaptcha widget.
     */
    public function display(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        return '<div '.$this->buildAttributes($attributes).'></div>';
    }

    /**
     * Display a button with a hCaptcha challenge bound to it.
     *
     * @link https://docs.hcaptcha.com/invisible
     */
    public function displayButton(string $label = 'Submit', array $attributes = []): string
    {
        if (! isset($attributes['data-callback'])) {
            $attributes['data-callback'] = 'onSubmit';
        }

        $attributes = $this->prepareAttributes($attributes);

        return '<button '.$this->buildAttributes($attributes).'>'.$label.'</button>';
    }

    /**
     * Load the hCaptcha javascript resource.
     */
    public function script(?string $locale = null, bool $render = false, ?string $onload = null, ?string $recaptchacompat = null): string
    {
        if (is_null($locale) && function_exists('app')) {
            $locale = app()->getLocale();
        }

        $data = [
            'onload' => $onload,
            'render' => $render ? 'explicit' : null,
            'hl' => $locale,
            'recaptchacompat' => $recaptchacompat ? 'on' : null,
        ];

        $parameters = http_build_query($data);

        return '<script src="https://js.hcaptcha.com/1/api.js?'.$parameters.'" async defer></script>'.PHP_EOL;
    }

    /**
     * Validate the user response.
     */
    public function validate(?string $token, ?string $remoteip = null): bool
    {
        if (empty($token)) {
            return false;
        }

        // Check if the response has already been verified.
        if (in_array($token, $this->verifiedResponses)) {
            return true;
        }

        $response = $this->getClient()->request('POST', self::VERIFY_URL, [
            'form_params' => [
                'secret' => $this->secret,
                'response' => $token,
                'remoteip' => $remoteip,
            ],
        ]);

        $data = json_decode($response->getBody());

        if (isset($data->success) && $data->success === true) {
            $this->verifiedResponses[] = $token;

            return true;
        } else {
            return false;
        }
    }

    /**
     * Build an HTML attribute string from an array.
     */
    protected function buildAttributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            $html[] = $key.'="'.$value.'"';
        }

        return implode(' ', $html);
    }

    /**
     * Prepare the attributes and apply the defaults.
     */
    protected function prepareAttributes(array $attributes = []): array
    {
        $defaults = [
            'class' => 'h-captcha',
            'data-sitekey' => $this->sitekey,
        ];

        // If the class attribute is present, expand it to the default value.
        if (isset($attributes['class']) && ! empty($attributes['class'])) {
            $defaults['class'] .= ' '.$attributes['class'];
        }

        $array = array_merge($attributes, $defaults);

        return $array;
    }
}
