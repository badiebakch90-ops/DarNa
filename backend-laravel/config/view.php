<?php

$compiledPath = env('VIEW_COMPILED_PATH');
$hasWindowsDrivePrefix = is_string($compiledPath)
    && preg_match('/^[A-Za-z]:[\\\\\\/]/', $compiledPath) === 1;

if (! $compiledPath || (DIRECTORY_SEPARATOR === '/' && $hasWindowsDrivePrefix)) {
    $compiledPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'darna-blade-cache';
}

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => $compiledPath,
];
