<?php

namespace Jiannius\Atom\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'atom:install
                            {--force : Force publishing}';

    protected $description = 'Install Atom';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Publishing Jiannius\Atom\AtomServiceProvider');
        $this->newLine();
        $this->call('vendor:publish', [
            '--provider' => 'Jiannius\Atom\AtomServiceProvider',
            '--force' => $this->option('force'),
        ]);
        $this->newLine(2);

        // Laravel Sanctum
        $this->info('Publishing Laravel\Sanctum\SanctumServiceProvider');
        $this->newLine();
        $this->call('vendor:publish', [
            '--provider' => 'Laravel\Sanctum\SanctumServiceProvider',
            '--force' => $this->option('force'),
        ]);
        $this->newLine(2);

        // App settings
        $this->info('App Settings');
        $this->newLine();

        $this->replaceInFile(
            'public const HOME = \'/home\';',
            'public const HOME = \'/app\';',
            app_path('Providers/RouteServiceProvider.php')
        );

        // NPM packages
        $this->info('Update node packages');
        $this->updateNodePackages(function ($packages) {
            return [
                '@inertiajs/inertia' => '^0.10.0',
                '@inertiajs/inertia-vue' => '^0.7.2',
                '@inertiajs/progress' => '^0.2.6',
                '@popperjs/core' => '^2.9.3',
                '@simonwep/pickr' => '^1.8.2',
                '@tailwindcss/forms' => '^0.2.1',
                '@tailwindcss/typography' => '^0.3.0',
                'dayjs' => '^1.10.6',
                'flatpickr' => '^4.6.9',
                'postcss-import' => '^12.0.1',
                'postcss-nesting' => '^7.0.1',
                'tailwindcss' => '^2.1.2',
                'autoprefixer' => '^10.0.2',
                'vue' => '^2.6.12',
                'vue-loader' => '^15.9.6',
                'vue-meta' => '^2.4.0',
                'vue-template-compiler' => '^2.6.10',
            ] + $packages;
        });

        // Webpack and tailwindcss config
        $this->info('Copy over tailwind and webpack configs');
        copy(__DIR__.'/../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/../../stubs/webpack.mix.js', base_path('webpack.mix.js'));
        copy(__DIR__.'/../../stubs/webpack.config.js', base_path('webpack.config.js'));
        copy(__DIR__.'/../../stubs/jsconfig.json', base_path('jsconfig.json'));

        $this->newLine(2);
        $this->info('Atom installation done');
        $this->comment('Please execute "npm install && npm run dev" to build your assets.');
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }
}