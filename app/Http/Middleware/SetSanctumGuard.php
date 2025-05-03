<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetSanctumGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Str::startsWith($request->getRequestUri(), '/api/admin/')) {
            config(['sanctum.guard' => 'admin']);
        } elseif (Str::startsWith($request->getRequestUri(), '/api/')) {
            config(['sanctum.guard' => 'customer']);
        }else {
            abort(403, 'Unauthorized route');
        }
        return $next($request);
    }

}
