<?php

namespace Blashbrook\PAPIClient\Livewire\Examples;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\{Layout, On};
use Livewire\Component;

/**
 * Component to test and receive data from PostalCodeSelectFlux.
 * It binds to the selected PostalCodeID and captures the full data
 * payload when an update occurs.
 */
class PostalCodeSelectFluxExample extends Component
{
    /**
     * @var string|null The selected Postal Code ID, bound via wire:model to the child component.
     */
    public $selectedPostalCodeID = null;

    /**
     * @var array Holds the full details of the selected postal code (City, State, County, etc.)
     *            received via the 'postalCodeUpdated' event.
     */
    public $postalCodeData = [];

    /**
     * Initializes the component, loading a demonstration value from the session if available.
     *
     * @return void
     */
    public function mount()
    {
        session(['PostalCodeID' => '12']);
        $this->selectedPostalCodeID = session('PostalCodeID');
    }

    /**
     * Handles the 'postalCodeUpdated' event dispatched by PostalCodeSelectFlux.
     *
     * @param  array  $data  The event payload containing full postal code details (id, City, State, etc.).
     * @return void
     */
    #[On('postalCodeUpdated')]
    public function handlePostalCodeUpdate($data)
    {
        $this->postalCodeData = $data;
    }

    /**
     * Renders the Livewire component view.
     *
     * @return View
     */
    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('papiclient::livewire.examples.postal-code-select-flux-example');
    }
}
