<?php

namespace Blashbrook\PAPIClient\Providers;

use Blashbrook\PAPIClient\PAPIClient;
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
            return new PAPIClient();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
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

        // Publishing the tests.

        $this->publishes([
            __DIR__.'/../Tests/Feature' => base_path('Tests/Feature'),
        ], 'papiclient.Tests');
    }
}
