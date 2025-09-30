@php use Blashbrook\PAPIClient\Livewire\DeliveryOptionSelectFlux; @endphp
@php
    /**
     * @var DeliveryOptionSelectFlux $this
     * @var array $fluxOptions Pre-processed options array from the component
     */
@endphp
<div>
    {{--
        FIX: Use slots instead of :options attribute to completely avoid the trim() error.
        This prevents Laravel's ComponentAttributeBag from processing the array data.
    --}}
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
