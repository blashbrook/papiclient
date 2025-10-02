{{--
/**
 * Blade view for the DeliveryOptionSelectFluxExample Livewire component.
 *
 * @var \Blashbrook\PAPIClient\Livewire\Examples\DeliveryOptionSelectFluxExample $this The Livewire component instance.
 * @var string|null $selectedDeliveryOptionID The selected Delivery Option ID bound via wire:model.
 * @var array $postalCodeData The full delivery option data received via event.
 */
--}}
<div class="max-w-2xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Delivery Option Test</h2>


    <div class="mb-2">Previous Value <span class="font-bold">{{ session('DeliveryOptionID') }}</span></div>
    <div class="mb-2">New Value <span class="font-bold">{{ $selectedDeliveryOptionID ?? '' }}</span></div>

    <div class="mb-6 mt-6">
        <livewire:delivery-option-select-flux
                wire:model="selectedDeliveryOptionID"
                :selected="$selectedDeliveryOptionID"
        />
        <button class="mt-4 bg-gray-200 hover:bg-gray-300 p-2 rounded text-sm" wire:click="$set('selectedDeliveryOptionID', null)">Clear Selection</button>
    </div>
</div>