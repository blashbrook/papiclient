<?php

namespace Blashbrook\PAPIClient\Providers;

use Blashbrook\PAPIClient\Models\PatronUdf;
use Blashbrook\PAPIClient\Console\Commands\{RunSeeders, UpdatePatronCodes, UpdatePatronStatCodes, UpdatePatronUdfs};
use Blashbrook\PAPIClient\Livewire\{DeliveryOptionSelect,
    DeliveryOptionSelectFlux,
    Examples\DeliveryOptionSelectFluxExample,
    Examples\PatronUdfSelectFluxExample,
    Examples\PostalCodeSelectFluxExample,
    PatronUDFSelect,
    PatronUDFSelectFlux,
    PostalCodeSelect,
    PostalCodeSelectFlux};
use Blashbrook\PAPIClient\PAPIClient;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class PAPIClientServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/Migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'papiclient');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Livewire::component('delivery-option-select', DeliveryOptionSelect::class);
        Livewire::component('patron-udf-select', PatronUDFSelect::class);
        Livewire::component('postal-code-select', PostalCodeSelect::class);

        // Optional components for use with Livewire Flux UI
        Livewire::component('delivery-option-select-flux', DeliveryOptionSelectFlux::class);
        Livewire::component('patron-udf-select-flux', PatronUDFSelectFlux::class);
        Livewire::component('postal-code-select-flux', PostalCodeSelectFlux::class);

        //Examples using Flux components
        Livewire::component('postal-code-select-flux-example', PostalCodeSelectFluxExample::class);
        Livewire::component('patron-udf-select-flux-example', PatronUdfSelectFluxExample::class);
        Livewire::component('delivery-option-select-flux-example', DeliveryOptionSelectFluxExample::class);

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
        ], 'papiclient.tests');

        // Registering package commands.
        $this->commands([
            RunSeeders::class,
            UpdatePatronCodes::class,
            UpdatePatronUdfs::class,
            UpdatePatronStatCodes::class,
        ]);
    }
}
