@php
    /**
     * @var \Blashbrook\PAPIClient\Livewire\PostalCodeSelectFlux $this
     * @var array $fluxOptions Pre-processed options array from the component
     */
@endphp
<div>
    <flux:select
            wire:model.live="postalCodeIDChanged"
            name="postalCodeIDChanged"
            label="Postal Code"
            placeholder="Select a city and postal code"
    >
        @foreach($fluxOptions as $option)
            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
        @endforeach
    </flux:select>
</div>
