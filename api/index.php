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
vercel_env_default('APP_URL', 'https://'.($_SERVER['HTTP_HOST'] ?? 'tugas-elearning.vercel.app'));
vercel_env_default('LOG_CHANNEL', 'stderr');
vercel_env_default('LOG_LEVEL', 'error');
vercel_env_default('CACHE_STORE', 'array');
vercel_env_default('SESSION_DRIVER', 'cookie');
vercel_env_default('SESSION_SECURE_COOKIE', 'true');
vercel_env_default('QUEUE_CONNECTION', 'sync');
vercel_env_default('MAIL_MAILER', 'log');
vercel_env_default('LARAVEL_STORAGE_PATH', '/tmp/storage');
vercel_env_default('APP_SERVICES_CACHE', '/tmp/cache/services.php');
vercel_env_default('APP_PACKAGES_CACHE', '/tmp/cache/packages.php');
vercel_env_default('APP_CONFIG_CACHE', '/tmp/cache/config.php');
vercel_env_default('APP_ROUTES_CACHE', '/tmp/cache/routes.php');
vercel_env_default('APP_EVENTS_CACHE', '/tmp/cache/events.php');

foreach ([
    '/tmp/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
] as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $exception) {
    if (filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');

        echo $exception::class.PHP_EOL;
        echo $exception->getMessage().PHP_EOL.PHP_EOL;
        echo $exception->getTraceAsString();
        exit;
    }

    throw $exception;
}
