{{--
/**
 * Blade view for the DeliveryOptionSelectFlux Livewire component.
 *
 * @var Blashbrook\PAPIClient\Livewire\DeliveryOptionSelectFlux $this The component instance.
 * @var array<int, array{value: string, label: string}> $fluxOptions Pre-processed options array from the component.
 */
--}}
<div>
    <flux:select
            wire:model.live="deliveryOptionIDChanged"
            name="deliveryOptionIDChanged"
            label="Notification Method"
            placeholder="Select a Delivery Method"
    >
        @foreach($fluxOptions as $option)
            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
        @endforeach
    </flux:select>
</div>