<div>
    <flux:select
        wire:model="selectedPatronUDFChanged"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge($attrs) }}
    >
        @foreach($options as $option)
            <flux:option value="{{ $option->value }}">
                {{ $option->label }}
            </flux:option>
        @endforeach
    </flux:select>
</div>
