<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'ext_transfer',
        'js_transfer',
        'ext_wordpress', 
        'webhook-virtual',
        'cardrecordlog',
        'check-business',
        'payment-submit/{id}',
        'payment-pin-submit/{id}',
        'payment-avs-submit/{id}',
        'payment-otp-submit/{id}',
        'api/js_transfer',
        'api/popup_transfer',
    ];
}
