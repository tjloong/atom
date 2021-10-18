<?php

namespace Jiannius\Atom;

use App\Http\Middleware\AtomBoot;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

use Jiannius\Atom\Middleware\IsRole;
use Jiannius\Atom\Middleware\TrackRef;
use Jiannius\Atom\Middleware\SetLocale;
use Jiannius\Atom\Models\Ability;
use Jiannius\Atom\Console\InstallCommand;
use Jiannius\Atom\Middleware\HandleInertiaRequests;

class AtomServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../stubs/config/atom.php', 'atom');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'atom');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        require_once __DIR__.'/Helpers.php';

        // disable resource data wrapping (https://laravel.com/docs/8.x/eloquent-resources#data-wrapping)
        JsonResource::withoutWrapping();

        $this->registerBlade();
        $this->registerComponents();
        $this->registerMiddlewares();
        $this->registerRoutes();
        $this->registerGates();
        $this->registerPublishing();
        $this->registerCommands();
    }

    /**
     * Register custom blade directive
     * 
     * @return void
     */
    public function registerBlade()
    {
        Blade::if('route', function($value) {
            return collect((array)$value)->contains(function($name) {
                return request()->route()->getName() === $name
                    || request()->path() === $name
                    || request()->is($name);
            });
        });

        Blade::if('notroute', function($value) {
            return !collect((array)$value)->contains(function($name) {
                return request()->route()->getName() === $name
                    || request()->path() === $name
                    || request()->is($name);
            });
        });
    }

    /**
     * Register components
     * 
     * @return void
     */
    public function registerComponents()
    {
        $this->loadViewComponentsAs('atom', [
            'Jiannius\\Atom\\Components\\Ga',
            'Jiannius\\Atom\\Components\\Gtm',
            'Jiannius\\Atom\\Components\\Seo',
            'Jiannius\\Atom\\Components\\FbPixel',
        ]);
    }

    /**
     * Register middlewares
     * 
     * @return void
     */
    public function registerMiddlewares()
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->appendMiddlewareToGroup('web', TrackRef::class);
        $kernel->appendMiddlewareToGroup('web', SetLocale::class);
        $kernel->appendMiddlewareToGroup('web', AtomBoot::class);
        $kernel->appendMiddlewareToGroup('web', HandleInertiaRequests::class);

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('role', IsRole::class);
    }

    /**
     * Register routes
     * 
     * @return void
     */
    public function registerRoutes()
    {
        Route::group(['middleware' => 'web'], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/auth.php');
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Register Gates
     * 
     * @return void
     */
    public function registerGates()
    {
        Gate::before(function ($user, $action) {
            if ($user->isRole('root')) return true;
            if ($user->isRole('admin')) return true;

            $split = explode('.', $action);
            $module = head($split);
            $name = last($split);
            $ability = Ability::where('module', $module)->where('name', $name)->first();

            if (!$ability) return false;

            $isForbidden = $user->abilities()->where('abilities.id', $ability->id)->wherePivot('access', 'forbid')->count() > 0;
            $isGranted = $user->abilities()->where('abilities.id', $ability->id)->wherePivot('access', 'grant')->count() > 0;
            $isInRole = $user->role->abilities()->where('abilities.id', $ability->id)->count() > 0;
    
            return !$isForbidden && ($isGranted || $isInRole);
        });
    }

    /**
     * Register publishing
     * 
     * @return void
     */
    public function registerPublishing()
    {
        if (!$this->app->runningInConsole()) return;

        $this->publishes([
            __DIR__.'/../stubs/app' => base_path('app'),
            __DIR__.'/../stubs/config' => base_path('config'),
            __DIR__.'/../stubs/storage' => base_path('storage'),
            __DIR__.'/../stubs/resources' => base_path('resources'),
            __DIR__.'/../stubs/.env.prod' => base_path('.env.prod'),
            __DIR__.'/../stubs/.env.staging' => base_path('.env.staging'),
        ], 'atom');
    }

    /**
     * Register commands
     * 
     * @return void
     */
    public function registerCommands()
    {
        if (!$this->app->runningInConsole()) return;

        $this->commands([
            InstallCommand::class,
        ]);
    }
}