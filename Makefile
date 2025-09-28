# PAPI Client Package Makefile
#
# This Makefile provides convenient commands for running tests,
# code quality checks, and common development tasks.

.PHONY: help test test-unit test-integration test-feature test-all test-coverage \
        install update clean cs-fix cs-check phpstan psalm

# Default target
help: ## Show this help message
	@echo 'Usage:'
	@echo '  make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-18s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Installation and Dependencies
install: ## Install dependencies
	composer install

update: ## Update dependencies
	composer update

# Testing Commands
test: ## Run all tests (unit only by default)
	vendor/bin/phpunit --testsuite=Unit

test-unit: ## Run unit tests only
	vendor/bin/phpunit --testsuite=Unit --colors=always

test-integration: ## Run integration tests (requires PAPI credentials)
	ENABLE_INTEGRATION_TESTS=true vendor/bin/phpunit --testsuite=Integration --colors=always

test-feature: ## Run feature tests
	vendor/bin/phpunit --testsuite=Feature --colors=always

test-all: ## Run all test suites
	vendor/bin/phpunit --colors=always

test-coverage: ## Run tests with coverage report (requires xdebug)
	vendor/bin/phpunit --coverage-html coverage-report --colors=always
	@echo "Coverage report generated in coverage-report/"

test-coverage-clover: ## Generate clover coverage report
	vendor/bin/phpunit --coverage-clover coverage.xml --colors=always

# Performance Testing
test-performance: ## Run performance tests
	vendor/bin/phpunit --group performance --colors=always

# Component Testing
test-components: ## Run all Livewire component tests
	vendor/bin/phpunit --filter "Component|Livewire" --colors=always

test-patron-udf: ## Run PatronUDFSelectFlux tests
	vendor/bin/phpunit Tests/Unit/PatronUDFSelectFluxTest.php --colors=always

test-postal-code: ## Run PostalCodeSelectFlux tests
	vendor/bin/phpunit Tests/Unit/PostalCodeSelectFluxTest.php --colors=always

test-livewire-feature: ## Run Livewire component feature tests
	vendor/bin/phpunit Tests/Feature/LivewireComponentsTest.php --colors=always

# Test with real PAPI API (use cautiously)
test-real-api: ## Run integration tests with real API (set env vars first)
	@echo "WARNING: This will make real API calls. Ensure you have proper credentials set."
	@echo "Required environment variables: PAPI_ACCESS_ID, PAPI_ACCESS_KEY, PAPI_BASE_URL"
	@echo "Press Ctrl+C to cancel, or Enter to continue..."
	@read
	ENABLE_INTEGRATION_TESTS=true vendor/bin/phpunit --testsuite=Integration

# Code Quality
cs-check: ## Check code style
	vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Fix code style issues
	vendor/bin/php-cs-fixer fix

phpstan: ## Run PHPStan static analysis
	vendor/bin/phpstan analyse

psalm: ## Run Psalm static analysis
	vendor/bin/psalm

# Cleanup
clean: ## Clean up generated files
	rm -rf coverage-report/
	rm -f coverage.xml
	rm -rf .phpunit.cache/
	rm -rf vendor/

# Documentation
docs: ## Generate documentation
	@echo "Documentation can be found in README.md"
	@echo "API documentation: https://your-papi-docs-url.com"

# Development helpers
watch-tests: ## Watch for file changes and run tests
	@echo "Watching for changes... (requires inotify-tools)"
	while inotifywait -r -e modify,create,delete src/ Tests/; do \
		make test-unit; \
	done

validate: ## Validate package configuration
	composer validate
	@echo "Package configuration is valid"

# Environment setup for integration tests
setup-integration: ## Setup environment for integration tests
	@echo "Setting up integration test environment..."
	@echo "Please set the following environment variables for integration tests:"
	@echo "  export PAPI_ACCESS_ID='your_access_id'"
	@echo "  export PAPI_ACCESS_KEY='your_access_key'"
	@echo "  export PAPI_BASE_URL='your_base_url'"
	@echo "  export PAPI_PUBLIC_URI='your_public_uri'"
	@echo "  export PAPI_PROTECTED_URI='your_protected_uri'"
	@echo "  export ENABLE_INTEGRATION_TESTS=true"

# Shortcuts for common tasks
quick: test-unit ## Quick test run (unit tests only)

ci: test-all cs-check ## Run all CI checks

dev-setup: install ## Setup development environment
	@echo "Development environment setup complete!"
	@echo "Run 'make test' to run the test suite."