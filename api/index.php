<?php

function vercel_env_default(string $key, string $value): void
{
    if (getenv($key) !== false) {
        return;
    }

    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

vercel_env_default('APP_ENV', 'production');
vercel_env_default('APP_DEBUG', 'false');
vercel_env_default('LOG_CHANNEL', 'stderr');
vercel_env_default('CACHE_STORE', 'array');
vercel_env_default('LARAVEL_STORAGE_PATH', '/tmp/storage');

foreach ([
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
] as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

require __DIR__.'/../public/index.php';
