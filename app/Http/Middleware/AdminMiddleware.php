<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Only allow users with admin role (id=2) to pass.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->user_role != 2) {
            abort(403, 'Unauthorized. Administrator access required.');
        }

        return $next($request);
    }
}
