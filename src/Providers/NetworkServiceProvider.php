<?php

namespace SimonHamp\NetworkElements\Providers;

use Illuminate\Support\ServiceProvider;
use SimonHamp\NetworkElements\Console\Commands\NetworkUserCommand;
use SimonHamp\NetworkElements\Console\Commands\NetworkConfigCommand;

class NetworkServiceProvider extends ServiceProvider
{
    private $namespace = 'network';

    public function register()
    {
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                NetworkConfigCommand::class,
                NetworkUserCommand::class,
            ]);
        }

        // Load migrations
        $this->loadMigrationsFrom($this->packagePathTo('database/migrations'));

        // Merge config
        $this->mergeConfigFrom($this->packagePathTo('config/network.php'), 'network');

        // Load routes
        $this->loadRoutes();

        // Load translations
        $this->loadTranslationsFrom($this->packagePathTo('resources/lang'), $this->namespace);

        // Load views
        $this->loadViewsFrom($this->packagePathTo('resources/views'), $this->namespace);

        // Register publishable assets
        $this->registerPublishableAssets();
    }

    private function loadRoutes()
    {
        // Load web routes
        $this->loadRoutesFrom($this->packagePathTo('routes/web.php'));

        // Load API routes
        $this->loadRoutesFrom($this->packagePathTo('routes/api.php'));

        // Load Broadcast channel auth routes
        $this->loadRoutesFrom($this->packagePathTo('routes/channels.php'));
    }

    private function registerPublishableAssets()
    {
        // Translations
        $this->publishes([
            $this->packagePathTo('resources/lang') => resource_path('lang/vendor/'.$this->namespace),
        ], 'translations');

        // Config
        $this->publishes([
            $this->packagePathTo('config/network.php') => config_path($this->namespace.'.php'),
        ], 'config');

        // Migrations
        $this->publishes([
            $this->packagePathTo('database/migrations/') => database_path('migrations'),
        ], 'migrations');

        // Views
        $this->publishes([
            $this->packagePathTo('resources/views') => resource_path('views/vendor/'.$this->namespace),
        ], 'views');

        // Source Assets
        $this->publishes([
            $this->packagePathTo('resources/assets') => public_path('resources/assets/vendor/'.$this->namespace),
        ], 'assets');

        // Compiled Assets
        $this->publishes([
            $this->packagePathTo('public') => public_path('vendor/'.$this->namespace),
        ], 'frontend');
    }

    private function packagePathTo($path)
    {
        return __DIR__.'/../../' . $path;
    }
}
