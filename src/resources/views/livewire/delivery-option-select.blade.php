<div>
    <select
        wire:change="handleUpdate($event.target.value)"
        id="notification_option"
        name="Notification Option"
        class="{{ $attrs['class'] ?? '' }}">
        <option value="">Select a notification option</option>
        @foreach($options as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
</div>
