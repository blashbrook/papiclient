{{--
/**
 * Blade view for the PostalCodeSelectFluxExample Livewire component.
 *
 * @var \Blashbrook\PAPIClient\Livewire\Examples\PostalCodeSelectFluxExample $this The Livewire component instance.
 * @var string|null $selectedPostalCodeID The selected Postal Code ID bound via wire:model.
 * @var array $postalCodeData The full postal code data received via event (City, State, PostalCode, etc.).
 */
--}}
<div class="max-w-2xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Postal Code Test</h2>


    <div class="mb-2">Session ID: <span class="font-bold">{{ session('PostalCodeID') }}</span></div>
    <div class="mb-2">Selected ID: <span class="font-bold">{{ $selectedPostalCodeID }}</span></div>
    <div class="mb-2">City: <span class="font-bold">{{ $postalCodeData['City'] ?? 'N/A' }}</span></div>
    <div class="mb-2">State: <span class="font-bold">{{ $postalCodeData['State'] ?? 'N/A' }}</span></div>
    <div class="mb-2">Postal Code: <span class="font-bold">{{ $postalCodeData['PostalCode'] ?? 'N/A' }}</span></div>
    <div class="mb-2">County: <span class="font-bold">{{ $postalCodeData['County'] ?? 'N/A' }}</span></div>
    <div class="mb-2">Country ID: <span class="font-bold">{{ $postalCodeData['CountryID'] ?? 'N/A' }}</span></div>

    <div class="mb-6 mt-6">
        <livewire:postal-code-select-flux
            wire:model="selectedPostalCodeID"
            :selected="$selectedPostalCodeID"
        />
        <button class="mt-4 bg-gray-200 hover:bg-gray-300 p-2 rounded text-sm" wire:click="$set('selectedPostalCodeID', null)">Clear Selection</button>
    </div>
</div>
