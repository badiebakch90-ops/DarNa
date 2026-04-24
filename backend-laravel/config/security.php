<?php

return [
    'allow_public_registration' => env('ALLOW_PUBLIC_REGISTRATION', env('APP_ENV') !== 'production'),
    'force_https' => env('SECURITY_FORCE_HTTPS', env('APP_ENV') === 'production'),
    'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
    'referrer_policy' => env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
    'x_frame_options' => env('SECURITY_X_FRAME_OPTIONS', 'SAMEORIGIN'),
    'permissions_policy' => env('SECURITY_PERMISSIONS_POLICY', 'camera=(), geolocation=(), microphone=()'),
    'cross_origin_opener_policy' => env('SECURITY_CROSS_ORIGIN_OPENER_POLICY', 'same-origin'),
    'cross_origin_resource_policy' => env('SECURITY_CROSS_ORIGIN_RESOURCE_POLICY', 'same-origin'),
];
