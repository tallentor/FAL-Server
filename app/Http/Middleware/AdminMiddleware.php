<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow only role = 2 (admin)
        if (auth()->check() && auth()->user()->role === 2) {
            return $next($request);
        }

        return response()->json(['message' => 'Access denied. Admins only.'], 403);
    }
}