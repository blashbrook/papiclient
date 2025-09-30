{{--
/**
 * Blade view for the PatronUDFSelectFlux Livewire component.
 *
 * @var \Blashbrook\PAPIClient\Livewire\PatronUDFSelectFlux $this The component instance.
 * @var string $udfLabel The UDF label used for the select field (passed from component).
 * @var string $placeholder The placeholder text for the select field (passed from component).
 * @var array $fluxOptions Options for the flux:select component, where each item is ['value' => string, 'label' => string] (passed from component's render method).
 */
--}}
<div>
    <flux:select
            wire:model.live="selectedOption"
            name="selectedOption"
            label="{{ $udfLabel }}"
            placeholder="{{ $placeholder }}"
    >
        @foreach($fluxOptions as $option)
            {{-- Using flux:select.option assuming standard Flux component usage --}}
            <flux:select.option value="{{ $option['value'] }}">
                {{ $option['label'] }}
            </flux:select.option>
        @endforeach
    </flux:select>
</div>