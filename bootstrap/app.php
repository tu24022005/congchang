<?php

use Illuminate\Foundation\Application; 
use Illuminate\Foundation\Configuration\Exceptions; 
use Illuminate\Foundation\Configuration\Middleware; 

return Application::configure(basePath: dirname(__DIR__)) 
    ->withRouting( 
        web: __DIR__.'/../routes/web.php', 
        api: __DIR__.'/../routes/api.php', 
        commands: __DIR__.'/../routes/console.php', 
        health: '/up', 
    ) 
    ->withMiddleware(function (Middleware $middleware): void { 
        // Đăng ký alias
        $middleware->alias([ 
            'admin' => \App\Http\Middleware\AdminMiddleware::class, 
            'role'  => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        // Ép UTF-8 cho mọi web response (fix browser CốC CốC và tương tự)
        $middleware->web(append: [
            \App\Http\Middleware\ForceUtf8::class,
        ]);
    }) 
    ->withExceptions(function (Exceptions $exceptions): void { 
        // 
    })->create();