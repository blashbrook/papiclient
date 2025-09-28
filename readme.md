# PAPIClient

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![PHP Composer](https://github.com/blashbrook/papiclient/actions/workflows/php.yml/badge.svg)](https://github.com/blashbrook/papiclient/actions/workflows/php.yml)
[![Node.js CI](https://github.com/blashbrook/papiclient/actions/workflows/node.js.yml/badge.svg)](https://github.com/blashbrook/papiclient/actions/workflows/node.js.yml)
[![Dependency Review](https://github.com/blashbrook/papiclient/actions/workflows/dependency-review.yml/badge.svg)](https://github.com/blashbrook/papiclient/actions/workflows/dependency-review.yml)
[![Semantic-Release](https://github.com/blashbrook/papiclient/actions/workflows/semantic-release.yml/badge.svg)](https://github.com/blashbrook/papiclient/actions/workflows/semantic-release.yml)
![StyleCI](https://github.styleci.io/repos/318002634/shield)

A Laravel package for integrating with Polaris API (PAPI) services. Provides API client functionality and pre-built Livewire components for common library operations.

**Key Features:**
- Fluent API client for Polaris ILS integration
- Pre-built Livewire components (Delivery Options, etc.)
- Session management and user preference handling
- Comprehensive testing suite

Take a look at [contributing.md](contributing.md) to see a to do list.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Components](#components)
  - [DeliveryOptionSelectFlux](#deliveryoptionselectflux)
    - [Features](#features)
    - [Configuration](#configuration)
    - [Usage Examples](#usage-examples)
    - [Troubleshooting](#troubleshooting)
  - [PatronUDFSelectFlux](#patronudfselectflux)
  - [PostalCodeSelectFlux](#postalcodeselectflux)
- [Change log](#change-log)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Installation

Via Composer

``` bash
$ composer require blashbrook/papiclient
```
Add the following variables to the project .env file
``` bash
# Access ID found under PAPI Key Management in the Polaris Web Admin Tool i.e. https://catalog.yourlibrary.org/webadmin/PAPIKeyManagement.aspx
PAPI_ACCESS_ID=

# Access Key found under PAPI Key Management in the Polaris Web Admin Tool i.e. https://catalog.yourlibrary.org/webadmin/PAPIKeyManagement.aspx
PAPI_ACCESS_KEY=

# Polaris API base URL i.e. https://catalog/yourlibrary.org/PAPIService/REST
PAPI_BASE_URL=  

# default
PAPI_PROTECTED_SCOPE=protected	

# default	
PAPI_PUBLIC_SCOPE=public  
		
# default
PAPI_VERSION=v1 	

# default				
PAPI_LANGID=1033	

# default				
PAPI_APPID=100	

# default					
PAPI_ORGID=3

# default protected PAPI URL constructor
PAPI_PROTECTED_URI="${PAPI_BASE_URL}/${PAPI_PROTECTED_SCOPE}/${PAPI_VERSION}/${PAPI_LANGID}/${PAPI_APPID}/${PAPI_ORGID}/"

# default public PAPI URL constructor
PAPI_PUBLIC_URI="${PAPI_BASE_URL}/${PAPI_PUBLIC_SCOPE}/${PAPI_VERSION}/${PAPI_LANGID}/${PAPI_APPID}/${PAPI_ORGID}/"

# Polaris branch ID found in Polaris under Administration > Explorer > Branches
# Right-click branch name and select Properties > About
PAPI_LOGONBRANCHID=

# Search under Administration > Staff Member in Polaris
# Right-click staff member name and select Properties > About
PAPI_LOGONUSERID=

# Search under Administration > Workstation in Polaris
# Right-click workstation name and select Properties > About
PAPI_LOGONWORKSTATIONID=

# Active Directory Domain used to log into Polaris i.e. Domain\Username
PAPI_DOMAIN=

# Polaris username (or if no Domain, user's email address)
PAPI_STAFF=

# Polaris user password in double quotes
PAPI_PASSWORD=

# Email to receive staff notifications in double quotes
PAPI_ADMIN_EMAIL="ecard@dcplibrary.org"

# Display name for staff email in double quotes
PAPI_ADMIN_NAME=
```
## Usage

* Use Injection to instantiate PAPIClient in a class:
````
  use Blashbrook\PAPIClient\PAPIClient;

  protected PAPIClient $papiclient;

  public function __construct(PAPIClient $papiclient) {
        $this->papiclient = $papiclient;
  }
````

* In a Livewire component, use the boot method:
```
  use Blashbrook\PAPIClient\PAPIClient;

  protected PAPIClient $papiclient;

  public function boot(PAPIClient $papiclient) {
    $this->papiclient = $papiclient;
  }
```
* To make an API call to your Polaris server:
````
// Validate PAPI Access Key
$response = $this->papiclient->method('GET')->uri('apikeyvalidate')->execRequest();
````
* functions include:
  * method('GET|PUT')
  * protected() // Uses the protected API base URI instead of the default public URI.
  * patron('BARCODE') // Allows you to insert a patron's barcode into the URI.
  * uri('API Endpoint') // The part of the URI that performs the desired function (i.e 'authenicator/patron' or 'apikeyvalidate').
  * params(array) // Used for form submissions (i.e. ['Barcode'=>'55555555555555', 'Password'=> '1234'] is sent to log in a patron).
  * auth('AccessSecret') // Inserts a patron's temporary authentication token in the request headers.
  * MORE TO COME!

## Components

### DeliveryOptionSelectFlux

A Livewire component that provides a filtered, customizable select dropdown for delivery options using the Flux UI framework.

#### Features
- **Filtered Options**: Only displays allowed delivery options from your database
- **Custom Display Names**: Override database values with user-friendly labels
- **Session Integration**: Remembers user's selection across page visits
- **Flux UI Integration**: Seamlessly works with Flux select components
- **Two-way Data Binding**: Integrates with parent Livewire components via `wire:model`

#### Quick Start

To use the delivery options component with session integration:

1. **In your Livewire component:**
```php
class YourComponent extends Component
{
    public $deliveryOptionIDChanged;
    
    public function mount()
    {
        $this->deliveryOptionIDChanged = session('DeliveryOptionID', 8);
    }
    
    public function updatedDeliveryOptionIDChanged($value)
    {
        session(['DeliveryOptionID' => $value]);
    }
}
```

2. **In your Blade template:**
```blade
<livewire:delivery-option-select-flux 
    wire:model="deliveryOptionIDChanged" 
    :delivery-option-i-d-changed="$deliveryOptionIDChanged" 
/>
```

That's it! The component will show: Mail, Email, Phone, Text Messaging with session persistence.

**Important:** The component includes an `updatedDeliveryOptionIDChanged()` listener that automatically:
- Updates the session with the new selection
- Dispatches a `deliveryOptionUpdated` event to notify parent components

#### Configuration

##### 1. Available Delivery Options

The component filters delivery options using an internal array. To modify which options are shown and their display names, edit the `$availableDeliveryOptions` array in `/src/Livewire/DeliveryOptionSelectFlux.php`:

```php
private $availableDeliveryOptions = [
    'Mailing Address' => 'Mail',           // Database value => Display name
    'Email Address' => 'Email',            // Database value => Display name
    'Phone 1' => 'Phone',                  // Database value => Display name
    'TXT Messaging' => 'Text Messaging'    // Database value => Display name
];
```

**Key Points:**
- **Database values** (keys) must exactly match the `DeliveryOption` field in your database
- **Display names** (values) are what users see in the dropdown
- Only options listed in this array will appear in the select dropdown
- Any delivery options in your database NOT in this array will be filtered out

##### 2. Usage in Blade Templates

**Basic Usage:**
```blade
<livewire:delivery-option-select-flux wire:model="deliveryOptionIDChanged" />
```

**With Initial Value:**
```blade
<livewire:delivery-option-select-flux 
    wire:model="deliveryOptionIDChanged" 
    :delivery-option-i-d-changed="$initialValue" 
/>
```

##### 3. Parent Component Integration

In your parent Livewire component:

```php
class YourParentComponent extends Component
{
    public $deliveryOptionIDChanged;
    
    public function mount()
    {
        // Set from session (recommended)
        $this->deliveryOptionIDChanged = session('DeliveryOptionID', 8);
        
        // OR set from user preference
        // $this->deliveryOptionIDChanged = auth()->user()->preferred_delivery_option ?? 8;
        
        // OR set hardcoded default
        // $this->deliveryOptionIDChanged = 8;
    }
    
    // Optional: Update session when value changes
    public function updatedDeliveryOptionIDChanged($value)
    {
        session(['DeliveryOptionID' => $value]);
    }
}
```

##### 4. Database Requirements

Ensure your `delivery_options` table has the structure:

```php
// Migration example
Schema::create('delivery_options', function (Blueprint $table) {
    $table->id();
    $table->integer('DeliveryOptionID')->unique();
    $table->string('DeliveryOption');
    $table->timestamps();
});
```

**Sample Data:**
```php
// Seeder example
DeliveryOption::create(['DeliveryOptionID' => 1, 'DeliveryOption' => 'Mailing Address']);
DeliveryOption::create(['DeliveryOptionID' => 2, 'DeliveryOption' => 'Email Address']);
DeliveryOption::create(['DeliveryOptionID' => 3, 'DeliveryOption' => 'Phone 1']);
DeliveryOption::create(['DeliveryOptionID' => 8, 'DeliveryOption' => 'TXT Messaging']);
```

##### 5. Customization Examples

**Adding New Options:**
1. Add the option to your database
2. Add it to the `$availableDeliveryOptions` array:
   ```php
   private $availableDeliveryOptions = [
       'Mailing Address' => 'Mail',
       'Email Address' => 'Email',
       'Phone 1' => 'Phone',
       'TXT Messaging' => 'Text Messaging',
       'Push Notification' => 'Push Alerts',  // New option
   ];
   ```

**Changing Display Names:**
```php
private $availableDeliveryOptions = [
    'Mailing Address' => 'Postal Mail',     // Changed from 'Mail'
    'Email Address' => 'Electronic Mail',   // Changed from 'Email'
    'Phone 1' => 'Voice Call',              // Changed from 'Phone'
    'TXT Messaging' => 'SMS',               // Changed from 'Text Messaging'
];
```

**Removing Options:**
Simply remove the line from the `$availableDeliveryOptions` array. The option will be filtered out even if it exists in the database.

##### 6. Session Integration

The component automatically integrates with Laravel sessions:

- **Reading**: Gets initial value from `session('DeliveryOptionID', defaultValue)`
- **Writing**: Updates session when user changes selection (if parent component implements `updatedDeliveryOptionIDChanged()`)
- **Persistence**: User's choice persists across browser sessions

##### 7. Testing

The component includes comprehensive tests. Run them with:
```bash
php artisan test --filter=DeliveryOptionSelectFluxTest
```

##### 8. Troubleshooting

**Component not showing options:**
- Verify database has delivery options with exact names matching `$availableDeliveryOptions` keys
- Check that the `DeliveryOption` model is accessible
- Ensure database connection is working

**Trim error:**
- This should be resolved, but if it occurs, clear view cache: `php artisan view:clear`

**Session not persisting:**
- Ensure `updatedDeliveryOptionIDChanged()` method is implemented in parent component
- Verify Laravel session configuration is correct

### PatronUDFSelectFlux

A dynamic Livewire component that creates select dropdowns from Patron User Defined Fields (UDFs) stored in your database. Perfect for forms that need library-specific custom fields like School, Department, Grade Level, etc.

#### Features
- **Dynamic UDF Loading**: Automatically loads options from PatronUdf database records
- **External Label Selection**: Specify which UDF to use via the `patronUdfLabel` parameter
- **Session Integration**: Remembers user's selection with label-specific session keys
- **Flux UI Integration**: Modern, accessible UI components
- **Custom Display Names**: Override database values with user-friendly labels
- **Event Broadcasting**: Notifies parent components of selection changes
- **Automatic Listener**: Includes `updatedSelectedPatronUDFChanged()` for external variable updates

#### Quick Start

To use the PatronUDF component for a "School" selection:

1. **In your Livewire component:**
```php
class YourComponent extends Component
{
    public $selectedSchool;
    
    public function mount()
    {
        $this->selectedSchool = session('PatronUDF_School', '');
    }
    
    #[On('patronUdfUpdated')]
    public function handleUdfUpdate($data)
    {
        if ($data['label'] === 'School') {
            $this->selectedSchool = $data['value'];
            // Handle school selection logic here
        }
    }
}
```

2. **In your Blade template:**
```blade
<livewire:patron-udf-select-flux 
    wire:model="selectedSchool"
    :patron-udf-label="'School'"
    :selected-patron-udf-changed="$selectedSchool"
/>
```

**Important:** The component includes an `updatedSelectedPatronUDFChanged()` listener that automatically:
- Updates the label-specific session (`PatronUDF_School`, etc.)
- Dispatches a `patronUdfUpdated` event to notify parent components
- Supports two-way data binding with external variables

### PostalCodeSelectFlux

A comprehensive Livewire component for postal code selection with city, state, and county information. Ideal for address forms, service area selection, and location-based features.

#### Features
- **Rich Location Data**: Shows city, state, postal code, and county information
- **Multiple Display Formats**: Customizable display formats (full, city_state_zip, city_zip, etc.)
- **Geographic Filtering**: Filter by state, county, or other geographic criteria
- **Session Integration**: Remembers user's postal code selection
- **Flux UI Integration**: Modern, accessible select component
- **Event Broadcasting**: Dispatches detailed postal code information
- **Search Functionality**: Built-in search and filtering capabilities
- **Automatic Listener**: Includes `updatedSelectedPostalCodeChanged()` for external variable updates

#### Quick Start

To use the postal code component for address selection:

1. **In your Livewire component:**
```php
class AddressComponent extends Component
{
    public $selectedPostalCode;
    public $userCity;
    public $userState;
    
    public function mount()
    {
        $this->selectedPostalCode = session('PostalCodeID', null);
    }
    
    #[On('postalCodeUpdated')]
    public function handlePostalCodeUpdate($data)
    {
        $this->userCity = $data['city'];
        $this->userState = $data['state'];
        // Auto-populate address fields
        $this->updateAddressFromPostalCode($data);
    }
    
    private function updateAddressFromPostalCode($postalData)
    {
        // Handle postal code selection logic
        $this->dispatch('addressUpdated', $postalData);
    }
}
```

2. **In your Blade template:**
```blade
<livewire:postal-code-select-flux 
    wire:model="selectedPostalCode"
    :selected-postal-code-changed="$selectedPostalCode"
    display-format="city_state_zip"
/>
```

**Important:** The component includes an `updatedSelectedPostalCodeChanged()` listener that automatically:
- Updates the session with the new postal code ID
- Dispatches a comprehensive `postalCodeUpdated` event with all location data
- Supports two-way data binding with external variables

#### Listener Implementation Details

**All Flux components include automatic listeners that:**
- Update external variables when selections change (enabling `wire:model` binding)
- Persist selections in session storage
- Dispatch events to notify parent components
- Handle empty/null values gracefully
- Support real-time updates without page refresh

**Example of listener in action:**
```php
// When user selects a new postal code:
// 1. updatedSelectedPostalCodeChanged() fires automatically
// 2. Session is updated: session(['PostalCodeID' => $newValue])
// 3. Event is dispatched: postalCodeUpdated with full location data
// 4. Parent component receives event and can update other fields
```

For complete configuration and usage examples, see the comprehensive testing documentation below.

## Change log

PAPIClient has been refactored to use fluency!
Now you can chain commands together, making the client more flexible and easier to use.

Please see the [changelog](CHANGELOG.md) for more information on what has changed recently.

@TODO
Add Error catching

## Testing

PAPIClient includes a comprehensive test suite covering unit tests, integration tests, and performance tests. The package uses PHPUnit 10 with organized test suites and convenient make commands.

### Quick Start

```bash
# Run unit tests (safe, no API calls)
make test

# Run all tests
make test-all

# Generate coverage report
make test-coverage
```

### Test Suites

#### Unit Tests
Fast, isolated tests that don't make real API calls:

```bash
# Via make command (recommended)
make test-unit

# Via PHPUnit directly
vendor/bin/phpunit --testsuite=Unit
```

**Unit tests cover:**
- PAPIClient instantiation and configuration
- Method chaining and fluent interface
- HTTP request building
- Response handling and error management
- Internal state management
- Mock API responses

#### Integration Tests
Tests that make real API calls to your PAPI server:

```bash
# Via make command (requires environment setup)
make test-integration

# Via PHPUnit directly
ENABLE_INTEGRATION_TESTS=true vendor/bin/phpunit --testsuite=Integration
```

**Integration tests cover:**
- Real API connectivity
- Authentication flows
- Error handling with live API responses
- Network timeout scenarios
- Different HTTP methods

**Prerequisites for Integration Tests:**
Set these environment variables before running integration tests:

```bash
export PAPI_ACCESS_ID='your_access_id'
export PAPI_ACCESS_KEY='your_access_key'
export PAPI_BASE_URL='https://your-catalog.org/PAPIService/REST'
export PAPI_PUBLIC_URI='https://your-catalog.org/PAPIService/REST/public/v1/1033/100/1/'
export PAPI_PROTECTED_URI='https://your-catalog.org/PAPIService/REST/protected/v1/1033/100/1/'
export ENABLE_INTEGRATION_TESTS=true
```

Or use the setup helper:
```bash
make setup-integration
```

#### Feature Tests
High-level tests for Laravel integration:

```bash
make test-feature
```

#### Performance Tests
Benchmark tests for response times and memory usage:

```bash
make test-performance
```

### Coverage Reports

Generate detailed code coverage reports:

```bash
# HTML coverage report (requires Xdebug)
make test-coverage
# Opens coverage-report/index.html

# Clover XML format (for CI/CD)
make test-coverage-clover
```

### Make Commands Reference

All available testing commands via Makefile:

```bash
make help                 # Show all available commands
make test                 # Run unit tests (default)
make test-unit            # Run unit tests only
make test-integration     # Run integration tests
make test-feature         # Run feature tests
make test-all             # Run all test suites
make test-coverage        # Generate HTML coverage
make test-performance     # Run performance tests
make test-real-api        # Integration tests with confirmation
```

### Direct PHPUnit Usage

If you prefer using PHPUnit directly:

```bash
# All tests
vendor/bin/phpunit

# Specific test suite
vendor/bin/phpunit --testsuite=Unit
vendor/bin/phpunit --testsuite=Integration
vendor/bin/phpunit --testsuite=Feature

# Specific test file
vendor/bin/phpunit Tests/Unit/PAPIClientTest.php

# Specific test method
vendor/bin/phpunit --filter testCanInstantiateClient

# With coverage
vendor/bin/phpunit --coverage-html coverage-report
```

### Continuous Integration

For CI/CD pipelines, use:

```bash
make ci  # Runs all tests and code style checks
```

### Testing Configuration

The test suite uses `phpunit.xml` with separate configurations for:
- Test environment variables
- Database settings (using array drivers for speed)
- Source code coverage filtering
- Test suite organization

### Test Safety

**Important:** Integration tests are disabled by default to prevent accidental API calls. They only run when explicitly enabled via environment variables.

- ✅ **Unit tests**: Always safe to run (no network calls)
- ✅ **Feature tests**: Safe (use Laravel testing features)
- ⚠️ **Integration tests**: Require real API credentials
- ✅ **Performance tests**: Safe (use mocked responses)

### Component Testing

The package includes comprehensive tests for all Livewire components (DeliveryOptionSelectFlux, PatronUDFSelectFlux, PostalCodeSelectFlux).

#### Component Test Structure

```
Tests/
├── Unit/
│   ├── PatronUDFSelectFluxTest.php      # Unit tests for PatronUDF component
│   ├── PostalCodeSelectFluxTest.php     # Unit tests for PostalCode component
│   └── PAPIClientTest.php               # Unit tests for API client
├── Feature/
│   ├── LivewireComponentsTest.php       # Feature tests for all components
│   └── PAPIClientTest.php               # Feature tests for API client
└── Integration/
    └── PAPIClientIntegrationTest.php     # Integration tests with real API
```

#### Running Component Tests

**All component tests:**
```bash
# Run all component-related tests
vendor/bin/phpunit --filter "Component|Livewire"

# Via make command
make test-components  # If you add this to Makefile
```

**Individual component tests:**
```bash
# PatronUDFSelectFlux tests
vendor/bin/phpunit Tests/Unit/PatronUDFSelectFluxTest.php
vendor/bin/phpunit --filter PatronUDFSelectFlux

# PostalCodeSelectFlux tests
vendor/bin/phpunit Tests/Unit/PostalCodeSelectFluxTest.php
vendor/bin/phpunit --filter PostalCodeSelectFlux

# All Livewire component feature tests
vendor/bin/phpunit Tests/Feature/LivewireComponentsTest.php
```

**Test specific component features:**
```bash
# Test only session integration
vendor/bin/phpunit --filter "session|Session"

# Test only event dispatching
vendor/bin/phpunit --filter "event|Event|dispatch"

# Test only filtering and search
vendor/bin/phpunit --filter "filter|Filter|search"
```

#### Component Test Coverage

**PatronUDFSelectFlux Tests Cover:**
- Component instantiation and initialization
- UDF loading based on external label parameter
- Label-specific session management (`PatronUDF_{Label}`)
- Event dispatching (`patronUdfUpdated`)
- Option filtering and display name customization
- Edge cases (empty values, non-existent labels, whitespace handling)
- Multiple component instances working independently
- View rendering and Flux UI integration

**PostalCodeSelectFlux Tests Cover:**
- Component instantiation and configuration
- Postal code loading with geographic filtering
- Multiple display formats (full, city_state_zip, city_zip)
- Search and filtering functionality
- Session persistence (`PostalCodeID`)
- Event dispatching with comprehensive location data
- Database query ordering and optimization
- Edge cases (empty filters, invalid formats)
- Performance considerations for large datasets

**Feature Tests Cover:**
- Cross-component integration and independence
- Real database interactions with migrations
- Session state persistence across "page loads"
- Component coexistence without interference
- Flux UI template rendering
- Error handling with missing database data

#### Test Database Setup

Component tests use in-memory SQLite databases with the `RefreshDatabase` trait:

```php
// Tests automatically create test data
PatronUdf::create([
    'PatronUdfID' => 1,
    'Label' => 'School',
    'Display' => true,
    'Values' => 'Elementary School,Middle School,High School,College',
    'Required' => true
]);

PostalCode::create([
    'PostalCodeID' => 1,
    'PostalCode' => '80202',
    'City' => 'Denver',
    'State' => 'CO',
    'County' => 'Denver County',
    'CountryID' => 1
]);
```

#### Testing Component Interactions

**Test Event Handling:**
```php
// Example from tests - verifying event dispatch
$component->set('selectedPatronUDFChanged', 'College');

$component->assertDispatched('patronUdfUpdated', [
    'label' => 'School',
    'value' => 'College', 
    'displayName' => 'College'
]);
```

**Test Session Integration:**
```php
// Verify session persistence
$component->set('selectedPostalCodeChanged', 1);
$this->assertEquals(1, Session::get('PostalCodeID'));

// Verify session loading on component mount
Session::put('PatronUDF_School', 'High School');
$component = Livewire::test(PatronUDFSelectFlux::class, [
    'patronUdfLabel' => 'School'
]);
$this->assertEquals('High School', $component->get('selectedPatronUDFChanged'));
```

**Test Component Properties:**
```php
// Test dynamic option loading
$options = $component->get('options');
$this->assertCount(4, $options);

// Test filtering functionality
$component->call('filterOptions', 'Denver');
$filteredOptions = $component->get('filteredOptions');
$this->assertCount(1, $filteredOptions);
```

#### Running Tests with Coverage

```bash
# Component test coverage
vendor/bin/phpunit --coverage-html coverage-components --filter "Component|Livewire"

# Full coverage including components
make test-coverage
```

#### Component Test Performance

**Test execution speed:**
- Unit tests: ~50-100ms per test (no database I/O)
- Feature tests: ~200-500ms per test (includes database operations)
- Full component test suite: ~5-10 seconds

**Optimizing test performance:**
```bash
# Run tests in parallel (if available)
vendor/bin/phpunit --parallel 4

# Run only fast unit tests during development
vendor/bin/phpunit --testsuite=Unit --filter Component
```

### Troubleshooting Tests

**Tests not found:**
```bash
composer dump-autoload
```

**Integration tests skipped:**
Ensure `ENABLE_INTEGRATION_TESTS=true` is set

**Component tests failing:**
```bash
# Clear Laravel caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Ensure database migrations are up to date
php artisan migrate:refresh --env=testing
```

**Livewire component tests failing:**
```bash
# Verify Livewire is properly installed
composer show livewire/livewire

# Check component registration
php artisan livewire:list
```

**Coverage requires Xdebug:**
```bash
# Install Xdebug via PECL or package manager
pecl install xdebug
```

**Permission errors:**
```bash
sudo chown -R $(whoami):$(whoami) storage/
chmod -R 755 storage/
```

**Database-related test failures:**
```bash
# Ensure test database is properly configured
# Check phpunit.xml database settings
# Verify RefreshDatabase trait is being used
```

### PatronUDFSelectFlux

A dynamic Livewire component that creates select dropdowns from Patron User Defined Fields (UDFs) stored in your database. Perfect for forms that need library-specific custom fields like School, Department, Grade Level, etc.

#### Features
- **Dynamic UDF Loading**: Automatically loads options from PatronUdf database records
- **External Label Selection**: Specify which UDF to use via the `patronUdfLabel` parameter
- **Session Integration**: Remembers user's selection with label-specific session keys
- **Flux UI Integration**: Modern, accessible UI components
- **Custom Display Names**: Override database values with user-friendly labels
- **Event Broadcasting**: Notifies parent components of selection changes

#### Quick Start

To use the PatronUDF component for a "School" selection:

1. **In your Livewire component:**
```php
class YourComponent extends Component
{
    public $selectedSchool;
    
    public function mount()
    {
        $this->selectedSchool = session('PatronUDF_School', '');
    }
    
    #[On('patronUdfUpdated')]
    public function handleUdfUpdate($data)
    {
        if ($data['label'] === 'School') {
            $this->selectedSchool = $data['value'];
            // Handle school selection logic here
        }
    }
}
```

2. **In your Blade template:**
```blade
<livewire:patron-udf-select-flux 
    wire:model="selectedSchool"
    :patron-udf-label="'School'"
    :selected-patron-udf-changed="$selectedSchool"
/>
```

#### Configuration

##### 1. Database Setup

Ensure your `patron_udfs` table has records like:

```php
// Migration example
Schema::create('patron_udfs', function (Blueprint $table) {
    $table->id();
    $table->integer('PatronUdfID')->unique();
    $table->string('Label');           // e.g., 'School', 'Department', 'Grade'
    $table->boolean('Display')->default(true);
    $table->text('Values')->nullable(); // Comma-separated values
    $table->boolean('Required')->default(false);
    $table->string('DefaultValue')->nullable();
    $table->timestamps();
});
```

**Sample Data:**
```php
// Seeder example
PatronUdf::create([
    'PatronUdfID' => 1,
    'Label' => 'School',
    'Display' => true,
    'Values' => 'Elementary School,Middle School,High School,College,Adult Education',
    'Required' => true
]);

PatronUdf::create([
    'PatronUdfID' => 2,
    'Label' => 'Department',
    'Display' => true,
    'Values' => 'Math,Science,English,History,Art,Music',
    'Required' => false
]);
```

##### 2. Component Parameters

```blade
<livewire:patron-udf-select-flux 
    :patron-udf-label="'School'"                    {{-- Required: UDF Label to use --}}
    wire:model="selectedValue"                      {{-- Two-way data binding --}}
    :selected-patron-udf-changed="$currentValue"    {{-- Initial value --}}
    :placeholder="'Choose your school'"             {{-- Custom placeholder --}}
    :attrs="['class' => 'custom-class']"            {{-- Additional HTML attributes --}}
/>
```

##### 3. Usage Examples

**Basic School Selection:**
```blade
<livewire:patron-udf-select-flux patron-udf-label="School" />
```

**Department Selection with Custom Placeholder:**
```blade
<livewire:patron-udf-select-flux 
    patron-udf-label="Department"
    placeholder="Select your department"
    wire:model="userDepartment"
/>
```

**Grade Level with Session Integration:**
```php
// In your component
public function mount()
{
    $this->selectedGrade = session('PatronUDF_Grade', '');
}

public function updatedSelectedGrade($value)
{
    session(['PatronUDF_Grade' => $value]);
}
```

```blade
<livewire:patron-udf-select-flux 
    patron-udf-label="Grade"
    wire:model="selectedGrade"
    :selected-patron-udf-changed="$selectedGrade"
/>
```

##### 4. Event Handling

The component dispatches `patronUdfUpdated` events:

```php
#[On('patronUdfUpdated')]
public function handlePatronUdfUpdate($data)
{
    // $data contains:
    // - 'label': The UDF label (e.g., 'School')
    // - 'value': Selected value (e.g., 'High School')
    // - 'displayName': Display name (customizable)
    
    match($data['label']) {
        'School' => $this->updateSchoolPreferences($data['value']),
        'Department' => $this->updateDepartmentSettings($data['value']),
        default => null
    };
}
```

##### 5. Session Integration

- **Auto-session keys**: `PatronUDF_{Label}` (e.g., `PatronUDF_School`)
- **Persistent selection**: User choices persist across browser sessions
- **Label-specific**: Different UDFs maintain separate session values

##### 6. Customization

**Custom Display Names (Advanced):**
Extend the component to override display names:

```php
// Create a custom component extending PatronUDFSelectFlux
class CustomSchoolSelectFlux extends PatronUDFSelectFlux
{
    protected function getCustomDisplayName(string $value): string
    {
        return match($value) {
            'Elementary School' => '🏫 Elementary (K-5)',
            'Middle School' => '🏛️ Middle School (6-8)',
            'High School' => '🎓 High School (9-12)',
            'College' => '🏛️ College/University',
            default => $value
        };
    }
}
```

##### 7. Troubleshooting

**No options appearing:**
- Verify PatronUdf record exists with the specified Label
- Check that `Display` field is `true`
- Ensure `Values` field contains comma-separated options
- Confirm database connection is working

**Session not persisting:**
- Verify Laravel session configuration
- Check session driver settings
- Ensure session middleware is active

**Wrong UDF loading:**
- Double-check the `patronUdfLabel` parameter spelling
- Verify Label field in database matches exactly (case-sensitive)
- Check for duplicate Label entries in database

### PostalCodeSelectFlux

A comprehensive Livewire component for postal code selection with city, state, and county information. Ideal for address forms, service area selection, and location-based features.

#### Features
- **Rich Location Data**: Shows city, state, postal code, and county information
- **Multiple Display Formats**: Customizable display formats (full, city_state_zip, city_zip, etc.)
- **Geographic Filtering**: Filter by state, county, or other geographic criteria
- **Session Integration**: Remembers user's postal code selection
- **Flux UI Integration**: Modern, accessible select component
- **Event Broadcasting**: Dispatches detailed postal code information
- **Search Functionality**: Built-in search and filtering capabilities

#### Quick Start

To use the postal code component for address selection:

1. **In your Livewire component:**
```php
class AddressComponent extends Component
{
    public $selectedPostalCode;
    public $userCity;
    public $userState;
    
    public function mount()
    {
        $this->selectedPostalCode = session('PostalCodeID', null);
    }
    
    #[On('postalCodeUpdated')]
    public function handlePostalCodeUpdate($data)
    {
        $this->userCity = $data['city'];
        $this->userState = $data['state'];
        // Auto-populate address fields
        $this->updateAddressFromPostalCode($data);
    }
    
    private function updateAddressFromPostalCode($postalData)
    {
        // Handle postal code selection logic
        $this->dispatch('addressUpdated', $postalData);
    }
}
```

2. **In your Blade template:**
```blade
<livewire:postal-code-select-flux 
    wire:model="selectedPostalCode"
    :selected-postal-code-changed="$selectedPostalCode"
    display-format="city_state_zip"
/>
```

#### Configuration

##### 1. Database Setup

Ensure your `postal_codes` table has the structure:

```php
// Migration example
Schema::create('postal_codes', function (Blueprint $table) {
    $table->id();
    $table->integer('PostalCodeID')->unique();
    $table->string('PostalCode', 10);      // e.g., '80202', '80202-1234'
    $table->string('City', 100);
    $table->string('State', 2);            // State abbreviation
    $table->string('County', 100)->nullable();
    $table->integer('CountryID')->default(1);
    $table->timestamps();
    
    $table->index(['State', 'City']);
    $table->index('PostalCode');
});
```

**Sample Data:**
```php
// Seeder example
PostalCode::create([
    'PostalCodeID' => 1,
    'PostalCode' => '80202',
    'City' => 'Denver',
    'State' => 'CO',
    'County' => 'Denver County',
    'CountryID' => 1
]);

PostalCode::create([
    'PostalCodeID' => 2,
    'PostalCode' => '80203',
    'City' => 'Denver',
    'State' => 'CO',
    'County' => 'Denver County',
    'CountryID' => 1
]);
```

##### 2. Component Parameters

```blade
<livewire:postal-code-select-flux 
    wire:model="selectedValue"                           {{-- Two-way data binding --}}
    :selected-postal-code-changed="$currentValue"        {{-- Initial value --}}
    :placeholder="'Choose your location'"                {{-- Custom placeholder --}}
    :display-format="'city_state_zip'"                   {{-- Display format --}}
    :filters="['State' => 'CO']"                         {{-- Geographic filters --}}
    :attrs="['class' => 'location-select']"              {{-- Additional HTML attributes --}}
/>
```

##### 3. Display Formats

**Available formats:**
- `full`: "Denver, CO 80202 (Denver County)"
- `city_state_zip`: "Denver, CO 80202" (default)
- `city_zip`: "Denver 80202"
- `custom`: Use custom formatting method

```blade
{{-- Full format with county --}}
<livewire:postal-code-select-flux display-format="full" />

{{-- Compact format --}}
<livewire:postal-code-select-flux display-format="city_zip" />

{{-- Standard format --}}
<livewire:postal-code-select-flux display-format="city_state_zip" />
```

##### 4. Geographic Filtering

**Filter by State:**
```blade
<livewire:postal-code-select-flux 
    :filters="['State' => 'CO']"
    placeholder="Select Colorado location"
/>
```

**Filter by Multiple Criteria:**
```blade
<livewire:postal-code-select-flux 
    :filters="[
        'State' => 'CO',
        'County' => 'Denver County'
    ]"
/>
```

**Dynamic Filtering in Parent Component:**
```php
public $selectedState = 'CO';
public $availablePostalCodes = [];

public function updatedSelectedState($state)
{
    // Re-render postal code component with new filter
    $this->dispatch('updatePostalCodeFilter', ['State' => $state]);
}
```

##### 5. Usage Examples

**Basic Postal Code Selection:**
```blade
<livewire:postal-code-select-flux />
```

**Service Area Selection:**
```blade
<div class="service-area-form">
    <label>Select Service Area:</label>
    <livewire:postal-code-select-flux 
        wire:model="serviceArea"
        :filters="['State' => 'CO', 'County' => 'Denver County']"
        display-format="city_state_zip"
        placeholder="Choose service area"
    />
</div>
```

**Address Form Integration:**
```php
class AddressFormComponent extends Component
{
    public $selectedPostalCode;
    public $address = [
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'county' => ''
    ];
    
    #[On('postalCodeUpdated')]
    public function handlePostalCodeSelection($data)
    {
        $this->address = [
            'city' => $data['city'],
            'state' => $data['state'], 
            'postal_code' => $data['postalCode'],
            'county' => $data['county']
        ];
        
        // Auto-populate form fields
        $this->dispatch('addressFieldsUpdated', $this->address);
    }
}
```

```blade
<form>
    <div class="form-group">
        <label>Location:</label>
        <livewire:postal-code-select-flux 
            wire:model="selectedPostalCode"
            :selected-postal-code-changed="$selectedPostalCode"
        />
    </div>
    
    {{-- Auto-populated fields --}}
    <input type="text" value="{{ $address['city'] }}" readonly>
    <input type="text" value="{{ $address['state'] }}" readonly>
    <input type="text" value="{{ $address['postal_code'] }}" readonly>
</form>
```

##### 6. Event Handling

The component dispatches comprehensive `postalCodeUpdated` events:

```php
#[On('postalCodeUpdated')]
public function handlePostalCodeUpdate($data)
{
    // $data contains:
    // - 'id': Database ID
    // - 'postalCodeId': PostalCodeID field
    // - 'city': City name
    // - 'state': State abbreviation
    // - 'postalCode': Postal code
    // - 'county': County name
    // - 'countryId': Country ID
    // - 'displayText': Formatted display string
    
    $this->updateLocationPreferences($data);
    $this->loadNearbyServices($data['postalCode']);
    $this->calculateShippingCosts($data);
}
```

##### 7. Session Integration

- **Session key**: `PostalCodeID`
- **Persistent selection**: User's postal code choice persists across sessions
- **Auto-restoration**: Component automatically loads saved selection on mount

```php
// Manual session management
public function mount()
{
    $this->selectedPostalCode = session('PostalCodeID', null);
}

public function updatedSelectedPostalCode($value)
{
    session(['PostalCodeID' => $value]);
}
```

##### 8. Advanced Customization

**Custom Display Format:**
Extend the component for custom formatting:

```php
class CustomPostalCodeSelectFlux extends PostalCodeSelectFlux
{
    protected function customFormatDisplay(PostalCode $postalCode): string
    {
        return "{$postalCode->City} ({$postalCode->PostalCode}) - {$postalCode->County}";
    }
}
```

**Search Integration:**
```php
public function searchPostalCodes($searchTerm)
{
    $this->filterOptions($searchTerm);
    $this->render(); // Re-render with filtered options
}
```

##### 9. Performance Optimization

**Lazy Loading:**
```blade
{{-- Load postal codes only when needed --}}
<livewire:postal-code-select-flux lazy />
```

**Pagination for Large Datasets:**
```php
// In a custom extended component
protected function loadPostalCodes(): void
{
    $this->options = PostalCode::select(/* fields */)
        ->limit(500) // Limit initial load
        ->get();
}
```

##### 10. Troubleshooting

**No postal codes appearing:**
- Verify postal_codes table has data
- Check database connection
- Ensure proper column names match model
- Verify filters aren't too restrictive

**Performance issues:**
- Add database indexes on State, City, PostalCode
- Consider lazy loading for large datasets
- Implement search functionality for better UX

**Session not persisting:**
- Verify Laravel session configuration
- Check session driver and middleware
- Ensure session storage is writable

**Incorrect location data:**
- Verify postal code data accuracy in database
- Check PostalCode model fillable fields
- Ensure proper data seeding

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist. 

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/blashbrook/papiclient.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/blashbrook/papiclient.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/blashbrook/papiclient/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/blashbrook/papiclient
[link-downloads]: https://packagist.org/packages/blashbrook/papiclient
[link-travis]: https://travis-ci.org/blashbrook/papiclient
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/blashbrook
[link-contributors]: ../../contributors
