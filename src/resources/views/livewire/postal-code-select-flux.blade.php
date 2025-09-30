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
