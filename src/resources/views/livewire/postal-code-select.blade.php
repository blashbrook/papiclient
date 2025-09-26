<div>
    <div>
        <select
            wire:change="handleUpdate($event.target.value)"
            id="postal_code"
            name="City and Postal Code"
            class="{{ $attrs['class'] ?? '' }}">
            <option value="">Select your city and postal code</option>
            @foreach($options as $option)
                <option value="{{ $option->id }}">
                    {{ $option->City . ', ' . $option->State . '  ' . $option->PostalCode }}
                </option>
            @endforeach
        </select>
    </div>
</div>
