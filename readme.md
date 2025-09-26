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

### Troubleshooting Tests

**Tests not found:**
```bash
composer dump-autoload
```

**Integration tests skipped:**
Ensure `ENABLE_INTEGRATION_TESTS=true` is set

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
