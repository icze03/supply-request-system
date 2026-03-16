<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
    'admin'       => \App\Http\Middleware\EnsureUserIsAdmin::class,
    'manager'     => \App\Http\Middleware\EnsureUserIsManager::class,
    'employee'    => \App\Http\Middleware\EnsureUserIsEmployee::class,
    'hr_manager'  => \App\Http\Middleware\EnsureUserIsHrManager::class,
    'super_admin' => \App\Http\Middleware\EnsureUserIsSuperAdmin::class,
    'permission'  => \App\Http\Middleware\CheckRolePermission::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
