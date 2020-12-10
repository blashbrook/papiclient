<?php

namespace Blashbrook\PAPIClient;

use Illuminate\Support\ServiceProvider;

class PAPIClientServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'papiclient');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'papiclient');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/papiclient.php', 'papiclient');

        // Register the service the package provides.
        $this->app->singleton('papiclient', function ($app) {
            return new PAPIClient;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['papiclient'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/papiclient.php' => config_path('papiclient.php'),
        ], 'papiclient.config');

        // Publishing the views.
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/blashbrook'),
         ], 'papiclient.views');

        $this->publishes([
            __DIR__ . '/../Tests/feature' => base_path('Tests/Feature'),
        ], 'papiclient.Tests');

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/blashbrook'),
        ], 'papiclient.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/blashbrook'),
        ], 'papiclient.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
