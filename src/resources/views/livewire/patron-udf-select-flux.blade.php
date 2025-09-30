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