<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeller
{
   
    public function handle(Request $request, Closure $next)
{
    //get iSeller method and user from User model
    if (!auth()->check() || !auth()->user()->isSeller()) {
        abort(403, 'Access denied'); // block non-seller users
    }

    // Allow request to proceed to the controller
    return $next($request);
}
}
