<?php

namespace Jiannius\Atom\Middleware;

use Closure;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($locales = config('atom.locales')) {
            $path = request()->path();
            $params = explode('/', $path);
        
            if (in_array($params[0], $locales)) {
                app()->setLocale($params[0]);
            }
        }

        return $next($request);
    }
}
