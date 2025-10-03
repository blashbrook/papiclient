{{--
/**
 * Blade view for the DeliveryOptionRadioFlux Livewire component.
 *
 * @var Blashbrook\PAPIClient\Livewire\DeliveryOptionRadioFlux $this The component instance.
 * @var array<int, array{value: string, label: string}> $fluxOptions Pre-processed options array from the component.
 */
--}}
<div>
    <flux:radio.group
            wire:model.live="selectedOption"
            name="selectedOption"
            label="Notification Method"
            placeholder="Select a Delivery Method"
    >
        @foreach($fluxOptions as $option)
            <flux:radio
                value="{{ $option['value'] }}"
                label="{{ $option['label'] }}"
            />
        @endforeach
    </flux:radio.group>
</div>