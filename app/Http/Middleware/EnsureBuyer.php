<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBuyer
{
    //get isBuyer method and user from User model
    public function handle(Request $request, Closure $next){
        if(!auth()->check() || !auth()->user()->isBuyer()){
          abort(403, 'Access Denied, Only buyer');
        }
        // Allow request to proceed to the controller
        return $next($request);
      }
      
    }

