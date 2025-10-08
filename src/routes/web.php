<?php

use Blashbrook\PAPIClient\Livewire\Examples\DeliveryOptionRadioFluxExample;
use Blashbrook\PAPIClient\Livewire\Examples\DeliveryOptionSelectFluxExample;
use Blashbrook\PAPIClient\Livewire\Examples\PatronUdfSelectFluxExample;
use Blashbrook\PAPIClient\Livewire\Examples\PostalCodeSelectFluxExample;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    // Public routes
    Route::get('papiclient/examples/postalcodeselectflux', PostalCodeSelectFluxExample::class);
    Route::get('papiclient/examples/patronudfselectflux', PatronUdfSelectFluxExample::class);
    Route::get('papiclient/examples/deliveryoptionselectflux', DeliveryOptionSelectFluxExample::class);
    Route::get('papiclient/examples/flux/deliveryoptionradio', DeliveryOptionRadioFluxExample::class);
});
