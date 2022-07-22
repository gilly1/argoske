<?php

namespace App\Http\Middleware\custom;

use Closure;

class checkRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (! $request->user()->hasAnyRole($role)) {
            return back();
        }

        return $next($request);
    }
}
