<?php

namespace Jiannius\Atom\Middleware;

use Closure;

class IsRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $rolename)
    {
        if ($user = $request->user()) {
            if ($user->isRole($rolename)) return $next($request);
        }

        abort(403);
    }
}
