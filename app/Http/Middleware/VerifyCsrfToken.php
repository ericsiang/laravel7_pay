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
        //跳過csrf判斷的頁面
        '/callback',
        '/checkout_status',
        '/checkout_ecpay_status',
        '/checkout_opay_status',
        '/ticket/*',
        
    ];
}
