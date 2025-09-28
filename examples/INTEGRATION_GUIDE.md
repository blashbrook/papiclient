# PostalCodeSelectFlux Integration Guide

This guide shows you exactly how to integrate the `PostalCodeSelectFlux` component into your notifications-test component.

## Quick Start

### 1. Component Integration (PHP)

Add these properties to your notifications-test component:

```php
class NotificationsTest extends Component
{
    // Postal Code Properties
    public $selectedPostalCode = null;
    public $userCity = '';
    public $userState = '';
    public $userPostalCode = '';
    public $userCounty = '';

    public function mount()
    {
        // Initialize from session
        $this->selectedPostalCode = session('PostalCodeID', null);
        
        // Load existing postal code details if available
        if ($this->selectedPostalCode) {
            $this->loadPostalCodeDetails();
        }
    }

    /**
     * CRITICAL: Add this listener to receive postal code updates
     */
    #[On('postalCodeUpdated')]
    public function handlePostalCodeUpdate($data)
    {
        $this->userCity = $data['city'];
        $this->userState = $data['state'];
        $this->userPostalCode = $data['postalCode'];
        $this->userCounty = $data['county'] ?? '';
        
        // Your custom logic here
        $this->updateNotificationSettings($data);
    }
}
```

### 2. Blade Template Integration

Add this to your Blade template:

```blade
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Select Location
    </label>
    
    {{-- PostalCodeSelectFlux Component --}}
    <livewire:postal-code-select-flux 
        wire:model="selectedPostalCode"
        :selected-postal-code-changed="$selectedPostalCode"
        display-format="city_state_zip"
        placeholder="Choose your city and postal code"
        :attrs="['class' => 'w-full']"
    />
</div>

{{-- Display selected location --}}
@if($userCity && $userState)
    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
        <p><strong>Selected:</strong> {{ $userCity }}, {{ $userState }} {{ $userPostalCode }}</p>
    </div>
@endif
```

## Advanced Configuration

### Display Formats

Choose how postal codes are displayed:

```blade
{{-- Full format: "Denver, CO 80202 (Denver County)" --}}
<livewire:postal-code-select-flux display-format="full" />

{{-- City + ZIP: "Denver 80202" --}}
<livewire:postal-code-select-flux display-format="city_zip" />

{{-- Default: "Denver, CO 80202" --}}
<livewire:postal-code-select-flux display-format="city_state_zip" />
```

### Geographic Filtering

Limit options to specific states or counties:

```blade
{{-- Only Colorado postal codes --}}
<livewire:postal-code-select-flux 
    :filters="['State' => 'CO']"
    placeholder="Select Colorado location"
/>

{{-- Multiple filters --}}
<livewire:postal-code-select-flux 
    :filters="[
        'State' => 'CO',
        'County' => 'Denver County'
    ]"
/>
```

### Custom Styling

Apply custom CSS classes:

```blade
<livewire:postal-code-select-flux 
    :attrs="[
        'class' => 'w-full border-2 border-blue-300 rounded-lg',
        'data-testid' => 'postal-code-selector'
    ]"
/>
```

## Event Data Structure

When a postal code is selected, the `postalCodeUpdated` event provides:

```php
$data = [
    'id' => 123,                    // Database ID
    'postalCodeId' => 456,          // PostalCodeID field
    'city' => 'Denver',             // City name
    'state' => 'CO',                // State abbreviation
    'postalCode' => '80202',        // ZIP code
    'county' => 'Denver County',    // County name
    'countryId' => 1,               // Country ID
    'displayText' => 'Denver, CO 80202'  // Formatted display string
];
```

## Common Use Cases

### 1. Form Validation

```php
public function updatedSelectedPostalCode($value)
{
    $this->validateOnly('selectedPostalCode');
}

protected $rules = [
    'selectedPostalCode' => 'required|exists:postal_codes,id',
];
```

### 2. Service Area Logic

```php
#[On('postalCodeUpdated')]
public function handlePostalCodeUpdate($data)
{
    // Update service availability
    $this->updateServiceArea($data['state'], $data['county']);
    
    // Adjust delivery options based on location
    if ($data['state'] === 'CO') {
        $this->enableExpressDelivery();
    }
}
```

### 3. Auto-populate Address Fields

```php
#[On('postalCodeUpdated')]
public function handlePostalCodeUpdate($data)
{
    $this->address['city'] = $data['city'];
    $this->address['state'] = $data['state'];
    $this->address['zip'] = $data['postalCode'];
    
    // Update shipping calculations
    $this->calculateShippingCost();
}
```

### 4. Notification Routing

```php
#[On('postalCodeUpdated')]
public function handlePostalCodeUpdate($data)
{
    // Route notifications based on location
    $this->notificationMessage = match($data['state']) {
        'CO' => "Notification for Colorado residents: {$data['city']}",
        'CA' => "California-specific notification: {$data['city']}",
        default => "Standard notification for {$data['city']}, {$data['state']}"
    };
}
```

## Troubleshooting

### Component Not Showing Options

1. **Check Database**: Ensure `postal_codes` table has data
2. **Verify Model**: Confirm `PostalCode` model exists and is accessible
3. **Database Connection**: Test database connectivity

### Session Not Persisting

1. **Laravel Sessions**: Verify session configuration
2. **Session Driver**: Check session driver settings  
3. **Session Middleware**: Ensure session middleware is active

### Event Not Firing

1. **Listener Syntax**: Use `#[On('postalCodeUpdated')]` attribute
2. **Method Name**: Ensure method name matches the attribute
3. **Component Registration**: Verify component is properly registered

## Testing

Test the integration with:

```bash
# Run postal code component tests
make test-postal-code

# Run all component tests
make test-components

# Test with coverage
vendor/bin/phpunit --filter PostalCodeSelectFlux --coverage-html coverage
```

## Production Checklist

- [ ] Remove debug information from Blade template
- [ ] Add proper validation rules
- [ ] Test with real postal code data
- [ ] Verify session persistence
- [ ] Test event handling
- [ ] Add error handling for missing data
- [ ] Test with large datasets (performance)
- [ ] Verify CSS/styling matches your design system