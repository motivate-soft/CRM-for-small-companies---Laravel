<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Role
{
    public function handle($request, Closure $next, $role)
    {

        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        if (!backpack_auth()->check() || !in_array(backpack_user()->role, $roles)) {
            abort(404);
        }

        return $next($request);
    }
}
