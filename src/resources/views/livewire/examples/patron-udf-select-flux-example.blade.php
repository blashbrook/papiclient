{{--
/**
 * Blade view for the PatronUdfSelectFluxExample Livewire component.
 *
 * @var \Blashbrook\PAPIClient\Livewire\Examples\PatronUdfSelectFluxExample $this The component instance
 * @var string $udfLabel
 * @var string $placeholder
 * @var string $udfRegistrationField
 * @var string|null $selectedUdfOption
 * @var array $udfData
 */
--}}
<div class="max-w-2xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">UDF Test</h2>

    <div class="mb-2">Selected UDF Option: <span class="font-bold">{{ $selectedUdfOption ?? 'N/A' }}</span></div>
    <div class="mb-2">UDF Label: <span class="font-bold">{{ $udfData['label'] ?? 'N/A' }}</span></div>
    <div class="mb-2">Display Name: <span class="font-bold">{{ $udfData['displayName'] ?? 'N/A' }}</span></div>
    <div class="mb-2">Session {{ $udfRegistrationField }} Value: <span class="font-bold">{{ session($udfRegistrationField) ?? '' }}</span></div>
    <div class="mb-6 mt-6">
        <livewire:patron-udf-select-flux
            wire:model="selectedUdfOption"
            :selected="$selectedUdfOption"
            :udfLabel="$udfLabel"
            :placeholder="$placeholder"
        />
        <button class="mt-4 bg-gray-200 hover:bg-gray-300 p-2 rounded text-sm"
                wire:click="$set('selectedUdfOption', null)">Clear Selection
        </button>
    </div>
</div>
