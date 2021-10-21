<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Jiannius\Atom\Models\Ability;

class AtomBoot
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
        Inertia::share('app_name', config('app.name'));
        Inertia::share('app_version', app_version());
        Inertia::share('flash', $this->getFlash($request));
        Inertia::share('toast', $this->getToast($request));
        Inertia::share('alert', $this->getAlert($request));
        Inertia::share('async', $request->session()->get('async'));
        Inertia::share('referer', $this->getReferer());

        if ($request->user()) {
            Inertia::share('navs', $this->getNavs($request));
            Inertia::share('auth.can', $this->getPermissions($request));
            Inertia::share('auth.user', request()->user()->load('tenant', 'reseller') ?: null);
            Inertia::share('auth.is_root', request()->user() ? request()->user()->isRole('root') : false);
        }

        return $next($request);
    }

    /**
     * Get flash messages
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function getFlash($request)
    {
        $flash = $request->session()->get('flash');

        if ($flash) {
            $split = explode('::', $flash);

            if (count($split) < 2) return $flash;
            else {
                $message = $split[0];
                $type = $split[1];
                $persist = isset($split[2]) ? true : false;

                return compact('type', 'message', 'persist');
            }
        }
        else if ($request->user() instanceof MustVerifyEmail && !$request->user()->hasVerifiedEmail()) {
            return [
                'type' => 'warning', 
                'message' => trans('auth.unverified', ['email' => $request->user()->email]),
                'action' => [
                    'href' => route('verification.resend'),
                    'label' => 'Resend',
                ],
            ];
        }
    }

    /**
     * Get toast message
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function getToast($request)
    {
        if ($toast = $request->session()->get('toast')) {
            $split = explode('::', $toast);
            $type = count($split) === 2 ? last($split) : 'info';
            $message = count($split) === 2 ? head($split) : $toast;
    
            return compact('type', 'message');
        }
    }

    /**
     * Get alert message
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function getAlert($request)
    {
        if ($alert = $request->session()->get('alert')) {
            $split = explode('::', $alert);
            $type = count($split) === 2 ? last($split) : 'info';
            $message = count($split) === 2 ? head($split) : $alert;
    
            return compact('type', 'message');
        }
    }

    /**
     * Get user permissions
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function getPermissions($request)
    {
        $permissions = [];

        Ability::all()->each(function ($ability) use ($request, &$permissions) {
            $name = $ability->module . '.' . $ability->name;
            $permissions[$name] = $request->user() && $request->user()->can($name);
        });

        return $permissions;
    }

    /**
     * Get url referer
     * 
     * @return string
     */
    public function getReferer()
    {
        $referer = url()->previous();

        if (Str::startsWith($referer, config('app.url') . '/app')) return $referer;

        return null;
    }

    /**
     * Get the navigations
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function getNavs($request)
    {
        if (!$request->user()) return [];

        $user = $request->user();
        $route = $request->route()->getName();
        $navs = [
            [
                'label' => 'Dashboard',
                'icon' => 'home-smile',
                'url' => route('app.home'),
                'active' => $route === 'app.home',
            ],
            [
                'label' => 'Settings',
                'icon' => 'cog',
                'dropdown' => [
                    [
                        'label' => 'My Account',
                        'url' => route('user.account'),
                        'active' => $route === 'user.account',
                    ],
                    [
                        'label' => 'Teams',
                        'url' => route('team.list'),
                        'active' => in_array($route, ['team.list', 'team.create', 'team.update']),
                        'enabled' => $user && $user->can('team.manage'),
                    ],
                    [
                        'label' => 'Roles',
                        'url' => route('role.list'),
                        'active' => in_array($route, ['role.list', 'role.create', 'role.update']),
                        'enabled' => $user && $user->can('role.manage'),
                    ],
                    [
                        'label' => 'Users',
                        'url' => route('user.list'),
                        'active' => in_array($route, ['user.list', 'user.create', 'user.update']),
                        'enabled' => $user && $user->can('user.manage'),
                    ],
                    [
                        'label' => 'Files',
                        'url' => route('file.list'),
                        'active' => $route === 'file.list',
                    ],
                ],
            ],
        ];

        return collect($navs)
            ->filter(function ($nav) {
                return $nav['enabled'] ?? true;
            })->map(function ($nav) {
                if ($dropdown = $nav['dropdown'] ?? null) {
                    $nav['dropdown'] = collect($nav['dropdown'])->filter(function ($item) {
                        return $item['enabled'] ?? true;
                    })->values();
                }

                return $nav;
            })->values();
    }
}
