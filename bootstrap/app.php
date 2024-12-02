<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureHasRole;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Menambahkan middleware untuk memeriksa role
        // $middleware->append(EnsureHasRole::class);
        $middleware->alias([
            // 'api.auth' => \App\Http\Middleware\EnsureHasRole::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->routeMiddleware([

    // 'role' => App\Http\Middleware\EnsureHasRole::class,
    // 'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
    // 'roles_or_permissions' => \Spatie\Permission\Middlewares\RolesOrPermissionsMiddleware::class,
]);
// $app->middleware([
//     App\Http\Middleware\RoleMiddleware::class,
// ]);
