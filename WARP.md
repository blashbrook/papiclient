# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

**PAPIClient** is a Laravel package that provides a fluent API client and pre-built Livewire components for integrating with Polaris ILS (Integrated Library System) API services. The package enables library software to interact with Polaris systems for patron management, delivery options, and geographic data.

## Essential Development Commands

### Testing
```bash
# Run unit tests (safe, no API calls)
make test

# Run all test suites
make test-all

# Run specific test types
make test-unit           # Unit tests only
make test-integration    # Integration tests (requires PAPI credentials)
make test-feature        # Feature tests
make test-components     # Livewire component tests

# Generate coverage reports
make test-coverage       # HTML coverage report
make test-coverage-clover # XML coverage for CI

# Test individual components
make test-patron-udf     # PatronUDFSelectFlux tests
make test-postal-code    # PostalCodeSelectFlux tests
```

### Development Workflow
```bash
# Setup development environment
make dev-setup

# Code quality checks
make cs-check            # Check code style
make cs-fix              # Fix code style issues
make phpstan             # Static analysis
make ci                  # All CI checks

# Package validation
make validate            # Validate composer configuration

# Run single test file
vendor/bin/phpunit Tests/Unit/PAPIClientTest.php

# Run specific test method
vendor/bin/phpunit --filter testCanInstantiateClient
```

### Integration Test Setup
Integration tests require real PAPI credentials and are disabled by default:

```bash
# Set required environment variables
export PAPI_ACCESS_ID='your_access_id'
export PAPI_ACCESS_KEY='your_access_key'
export PAPI_BASE_URL='https://catalog.yourlibrary.org/PAPIService/REST'
export ENABLE_INTEGRATION_TESTS=true

# Then run integration tests
make test-integration
```

## Architecture & Code Organization

### Core Architecture
The package follows a **fluent interface pattern** for the main API client with **trait-based concerns** for separation of responsibilities:

- **`PAPIClient.php`** - Main fluent API client extending GuzzleHttp\Client
- **`Concerns/`** - Trait-based features (CreateHeaders, Formatters, GetConfig, ReadResponses)
- **`Livewire/`** - Pre-built UI components for common library operations
- **`Models/`** - Eloquent models for library-specific data
- **`Console/Commands/`** - Artisan commands for data management

### Fluent API Pattern
The main `PAPIClient` class uses method chaining for readable API calls:

```php
// Basic pattern
$response = $papiclient->method('GET')->uri('apikeyvalidate')->execRequest();

// Patron authentication pattern  
$response = $papiclient->protected()->patron('1234567890123')
                      ->uri('authenticator/patron')
                      ->params(['Password' => 'secret'])
                      ->execRequest();

// Authenticated API calls pattern
$holds = $papiclient->protected()->patron('1234567890123')
                   ->auth($accessToken)
                   ->uri('patron/holds')
                   ->execRequest();
```

### Component Architecture
Livewire components follow a **specialized UI component pattern** with:

1. **Flux UI Integration** - Modern, accessible components using Flux design system
2. **Session Persistence** - Automatic session management with label-specific keys  
3. **Event Broadcasting** - Components dispatch events to parent components
4. **External Data Loading** - Dynamic loading from database models
5. **Two-way Data Binding** - Full `wire:model` support

Key components:
- **`DeliveryOptionSelectFlux`** - Filtered delivery option selection
- **`PatronUDFSelectFlux`** - Dynamic User Defined Field dropdowns  
- **`PostalCodeSelectFlux`** - Geographic postal code selection with filtering

### Model Structure
Models represent library-specific data with **Polaris field naming conventions**:

```php
// Models use Polaris field names (e.g., DeliveryOptionID, not delivery_option_id)
DeliveryOption::class  // DeliveryOptionID, DeliveryOption
PatronUdf::class       // PatronUdfID, Label, Values, Display, Required
PostalCode::class      // PostalCodeID, PostalCode, City, State, County
PatronCode::class      // PatronCodeID, Description
```

### Service Layer
Services handle **business logic and external API integration**:
- **`PatronCodeService`** - Sync patron codes from PAPI
- **`PatronUdfService`** - Manage User Defined Fields
- **`PatronStatClassCodeService`** - Handle patron status codes

## Key Development Patterns

### Component Development
When creating new Livewire components:

1. **Follow the Flux pattern** - Extend existing Flux components for consistency
2. **Implement session integration** - Use label-specific session keys (`ComponentName_Label`)
3. **Add event broadcasting** - Dispatch events with comprehensive data
4. **Include automatic listeners** - Implement `updated{PropertyName}()` methods
5. **Write comprehensive tests** - Unit tests for logic, Feature tests for integration

### API Client Extensions
When extending the PAPIClient:

1. **Use trait-based concerns** - Separate functionality into focused traits
2. **Maintain fluent interface** - All methods should return `$this` for chaining
3. **Follow authentication patterns** - Use `protected()`, `patron()`, `auth()` methods
4. **Handle errors appropriately** - Use `@throws` annotations and proper exception handling

### Testing Strategy
The package uses **tiered testing** with different safety levels:

- **Unit Tests** - Safe, fast, no external dependencies (default for `make test`)
- **Feature Tests** - Laravel integration, in-memory database
- **Integration Tests** - Real API calls, require credentials, disabled by default
- **Component Tests** - Livewire component behavior and event handling

## Configuration & Environment

### Required Environment Variables
For API functionality:
```bash
PAPI_ACCESS_ID=          # Polaris API Access ID
PAPI_ACCESS_KEY=         # Polaris API Access Key  
PAPI_BASE_URL=           # Polaris API base URL
PAPI_LOGONBRANCHID=      # Branch ID for authentication
PAPI_LOGONUSERID=        # Staff user ID
PAPI_LOGONWORKSTATIONID= # Workstation ID
PAPI_DOMAIN=             # Active Directory domain
PAPI_STAFF=              # Staff username
PAPI_PASSWORD=           # Staff password
```

### Database Requirements
The package expects these tables for Livewire components:
- `delivery_options` - Delivery method options
- `patron_udfs` - User Defined Fields configuration
- `postal_codes` - Geographic location data
- `patron_codes` - Patron classification codes

## Important Development Notes

### Polaris Integration Specifics
- **Field Naming**: Use Polaris conventions (`DeliveryOptionID` not `delivery_option_id`)
- **Authentication Flow**: PAPI uses temporary access tokens from patron authentication
- **URL Structure**: Different endpoints for public vs protected operations
- **Parameter Formatting**: Request parameters need Polaris-specific formatting

### Component Session Management
Each Livewire component manages its own session state:
- `DeliveryOptionID` - Selected delivery option
- `PatronUDF_{Label}` - UDF selections by label (e.g., `PatronUDF_School`)  
- `PostalCodeID` - Selected postal code

### Error Handling Patterns
- **GuzzleException** - HTTP request failures
- **JsonException** - Response parsing issues
- **Component errors** - Handle missing database data gracefully

## Testing Best Practices

### Safe Development Testing
For day-to-day development, use unit tests which don't make external API calls:
```bash
make test  # Safe default - unit tests only
```

### Integration Testing
Only run integration tests when you need to verify API connectivity:
```bash
# Setup credentials first
make setup-integration
# Then run carefully
make test-real-api
```

### Component Testing
Test Livewire components comprehensively:
```bash
# Test all components
make test-components

# Test specific component behavior
vendor/bin/phpunit --filter "session|Session"     # Session integration
vendor/bin/phpunit --filter "event|Event"         # Event dispatching
vendor/bin/phpunit --filter "filter|Filter"       # Filtering functionality
```