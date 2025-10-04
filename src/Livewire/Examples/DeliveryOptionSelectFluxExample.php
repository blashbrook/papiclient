<?php


namespace Blashbrook\PAPIClient\Livewire\Examples;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\{Layout, On};
use Livewire\Component;

/**
 * Component to test and receive data from DeliveryOptionSelectFlux.
 * It binds to the selected DeliveryOptionID and captures the full data
 * payload when an update occurs.
 */
class DeliveryOptionSelectFluxExample extends Component
{

    /**
     * @var string|null The selected Delivery Option ID, bound via wire:model to the child component.
     */
    public $selectedDeliveryOptionID = null;

    /**
     * @var array Holds the full details of the selected delivery option
     * received via the 'deliveryOptionUpdated' event.
     */
    public $deliveryOptionData = [];

    /**
     * Initializes the component, loading a demonstration value from the session if available.
     * @return void
     */
    public function mount()
    {
        session(['DeliveryOptionID' => '3']);
        // Initialize the public property so it is displayed in the "New Value" field immediately.
        //$this->selectedDeliveryOptionID = session('DeliveryOptionID');
    }

    /**
     * Handles the 'deliveryOptionUpdated' event dispatched by DeliveryOptionSelectFlux.
     *
     * @param  array  $data  The event payload containing full delivery option details
     * @return void
     */
    #[On('deliveryOptionUpdated')]
    public function handleDeliveryOptionUpdate($data)
    {
        $this->deliveryOptionData = $data;
    }

    /**
     * Renders the Livewire component view.
     *
     * @return View
     */
    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('papiclient::livewire.examples.delivery-option-select-flux-example');
    }
}