<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    //get isAdmin method and user from User model
    public function handle(Request $request, Closure $next){
       if(!auth()->check() || !auth()->user()->isAdmin()){
        abort(403, 'Access Denied, Admin only');
       }
       // Allow request to proceed to the controller
       return $next($request);
    }
       
    }
    

