<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if (!Auth::user()->hasPermission($permission)) {
            return response()->json(['error' => 'Accès refusé. Vous n\'avez pas les autorisations nécessaires.'], 403);
        }

        return $next($request);
    }
}