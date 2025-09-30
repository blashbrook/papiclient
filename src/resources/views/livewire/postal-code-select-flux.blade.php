{{--
/**
 * Blade view for the PostalCodeSelectFlux Livewire component.
 *
 * @var \Blashbrook\PAPIClient\Livewire\PostalCodeSelectFlux $this The component instance.
 * @var array $fluxOptions Options for the flux:select component, where each item is ['value' => string, 'label' => string].
 * @var string|null $selectedOption The selected option value, bound via wire:model.
 */
--}}
<div>
    <flux:select
            wire:change="handleUpdate($event.target.value)"
            wire:model.live="selectedOption"
            name="selectedOption"
            label="City and Postal Code"
            placeholder="Select your city and postal code"
    >
        @foreach($fluxOptions as $option)
            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
        @endforeach
    </flux:select>
</div>
