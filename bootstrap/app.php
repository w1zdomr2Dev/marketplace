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
    ->withMiddleware(function (Middleware $middleware){
        $middleware->alias([
            //bali si EnsureBuyer ay mapupunta or naka store na sa 'buyer'. 
            // Bali pag tatawagin mo si EnsurebBuyer is siya na si 'buyer'
            'buyer' => \App\Http\Middleware\EnsureBuyer::class,
            'seller' => \App\Http\Middleware\EnsureSeller::class,
             'admin'  => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
