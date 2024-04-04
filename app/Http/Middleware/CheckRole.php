<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user()) {
            return abort(401, 'Non autorisé');
        }

        if (!$request->user()->roles()->where('name', $role)->exists()) {
            return abort(403, 'Interdit');
        }

        return $next($request);
    }
}
