<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\StaffOnly;
use App\Http\Middleware\StudentOnly;
use \App\Http\Middleware\SetLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'checkstatus' => \App\Http\Middleware\CheckStatus::class,
            'role'        => \App\Http\Middleware\RoleMiddleware::class,
            'staff.only'   => \App\Http\Middleware\StaffOnly::class,
            'student.only' => \App\Http\Middleware\StudentOnly::class,
            'setlocale'    => \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
