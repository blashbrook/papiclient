<?php

namespace Blashbrook\PAPIClient\Tests\Unit;

use Blashbrook\PAPIClient\Livewire\DeliveryOptionSelectFlux;
use Blashbrook\PAPIClient\Models\DeliveryOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Orchestra\Testbench\TestCase;

class DeliveryOptionSelectFluxTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Blashbrook\PAPIClient\Providers\PAPIClientServiceProvider::class,
            \Livewire\LivewireServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Set up test database
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();

        // Create the delivery_options table for testing
        Schema::create('delivery_options', function (Blueprint $table) {
            $table->id();
            $table->integer('DeliveryOptionID')->unique();
            $table->string('DeliveryOption');
            $table->timestamps();
        });

        // Seed test data
        DeliveryOption::create([
            'DeliveryOptionID' => 1,
            'DeliveryOption' => 'Mailing Address',
        ]);

        DeliveryOption::create([
            'DeliveryOptionID' => 2,
            'DeliveryOption' => 'Email Address',
        ]);

        DeliveryOption::create([
            'DeliveryOptionID' => 8,
            'DeliveryOption' => 'TXT Messaging',
        ]);
    }

    /** @test */
    public function it_can_mount_component_and_initialize_delivery_options()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Assert that delivery options were loaded
        $this->assertInstanceOf(Collection::class, $component->get('deliveryOptions'));
        $this->assertCount(3, $component->get('deliveryOptions'));

        // Assert that default deliveryOptionIDChanged is set to first option
        $this->assertEquals('1', $component->get('deliveryOptionIDChanged'));
    }

    /** @test */
    public function it_can_mount_with_predefined_delivery_option_id()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class, [
            'deliveryOptionIDChanged' => 8,
        ]);

        // Assert that the predefined value is preserved
        $this->assertEquals(8, $component->get('deliveryOptionIDChanged'));
        $this->assertCount(3, $component->get('deliveryOptions'));
    }

    /** @test */
    public function it_renders_without_errors()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        $component->assertOk();
        $component->assertViewIs('papiclient::livewire.delivery-option-select-flux');
    }

    /** @test */
    public function it_processes_delivery_options_into_flux_compatible_format()
    {
        $component = new DeliveryOptionSelectFlux();
        $component->mount();

        $view = $component->render();
        $fluxOptions = $view->getData()['fluxOptions'];

        // Assert the format is correct for Flux select component
        $this->assertIsArray($fluxOptions);
        $this->assertCount(3, $fluxOptions);

        // Test the structure of the first option
        $firstOption = $fluxOptions[0];
        $this->assertArrayHasKey('value', $firstOption);
        $this->assertArrayHasKey('label', $firstOption);
        $this->assertIsString($firstOption['value']); // Critical: value must be string
        $this->assertEquals('1', $firstOption['value']);
        $this->assertEquals('Mailing Address', $firstOption['label']);

        // Test another option to ensure all are processed correctly
        $thirdOption = $fluxOptions[2];
        $this->assertEquals('8', $thirdOption['value']);
        $this->assertEquals('TXT Messaging', $thirdOption['label']);
    }

    /** @test */
    public function it_fixes_trim_error_by_providing_clean_array_to_view()
    {
        // This test specifically addresses the original trim() error
        $component = new DeliveryOptionSelectFlux();
        $component->mount();

        $view = $component->render();
        $fluxOptions = $view->getData()['fluxOptions'];

        // Assert that all values are strings (not arrays or objects)
        foreach ($fluxOptions as $option) {
            $this->assertIsString($option['value'], 'Option value must be string to prevent trim() error');
            $this->assertIsString($option['label'], 'Option label must be string to prevent trim() error');
        }

        // Assert no complex objects or collections are passed to the view
        $this->assertIsArray($fluxOptions, 'fluxOptions should be a plain array, not a Collection');

        // The view should not contain complex collection mapping
        $viewContent = $view->render();
        $this->assertStringContainsString(':options="$fluxOptions"', $viewContent,
            'View should use pre-processed $fluxOptions instead of inline collection mapping');
    }

    /** @test */
    public function it_can_update_delivery_option_id()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Update the delivery option
        $component->set('deliveryOptionIDChanged', 2);

        // Assert the value was updated
        $this->assertEquals(2, $component->get('deliveryOptionIDChanged'));
        $component->assertOk();
    }

    /** @test */
    public function it_handles_reactive_property_updates()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Simulate parent component updating the reactive property
        $component->set('deliveryOptionIDChanged', 8);

        $this->assertEquals(8, $component->get('deliveryOptionIDChanged'));
        $component->assertOk();
    }

    /** @test */
    public function it_handles_empty_delivery_options_gracefully()
    {
        // Clear all delivery options
        DeliveryOption::truncate();

        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Should not throw an error
        $component->assertOk();
        $this->assertCount(0, $component->get('deliveryOptions'));
        $this->assertNull($component->get('deliveryOptionIDChanged'));
    }

    /** @test */
    public function it_passes_correct_data_structure_to_blade_view()
    {
        $component = new DeliveryOptionSelectFlux();
        $component->mount();

        $view = $component->render();
        $viewData = $view->getData();

        // Assert the view receives exactly what it needs
        $this->assertArrayHasKey('fluxOptions', $viewData);
        $this->assertIsArray($viewData['fluxOptions']);

        // Assert the structure matches what the Flux select component expects
        foreach ($viewData['fluxOptions'] as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertEquals(2, count($option), 'Each option should have exactly 2 keys: value and label');
        }
    }

    /** @test */
    public function it_maintains_data_integrity_between_model_and_view()
    {
        $component = new DeliveryOptionSelectFlux();
        $component->mount();

        // Get original data from component
        $originalOptions = $component->deliveryOptions;

        // Get processed data from render method
        $view = $component->render();
        $fluxOptions = $view->getData()['fluxOptions'];

        // Assert data integrity is maintained
        $this->assertCount($originalOptions->count(), $fluxOptions);

        foreach ($originalOptions as $index => $originalOption) {
            $processedOption = $fluxOptions[$index];
            $this->assertEquals((string) $originalOption->DeliveryOptionID, $processedOption['value']);
            $this->assertEquals($originalOption->DeliveryOption, $processedOption['label']);
        }
    }

    /** @test */
    public function it_updates_session_when_delivery_option_changes()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Change delivery option
        $component->set('deliveryOptionIDChanged', 2);

        // Assert session was updated
        $this->assertEquals(2, session('DeliveryOptionID'));
    }

    /** @test */
    public function it_dispatches_delivery_option_updated_event()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Change delivery option
        $component->set('deliveryOptionIDChanged', 2);

        // Assert event was dispatched with correct data
        $component->assertDispatched('deliveryOptionUpdated', function ($event, $data) {
            return $data['deliveryOptionId'] === 2 &&
                   $data['deliveryOption'] === 'Email Address' &&
                   $data['displayName'] === 'Email';
        });
    }

    /** @test */
    public function it_uses_custom_display_names_in_events()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Test each available option's custom display name
        $expectedMappings = [
            1 => ['option' => 'Mailing Address', 'display' => 'Mail'],
            2 => ['option' => 'Email Address', 'display' => 'Email'],
            8 => ['option' => 'TXT Messaging', 'display' => 'Text Messaging']
        ];

        foreach ($expectedMappings as $id => $expected) {
            $component->set('deliveryOptionIDChanged', $id);

            $component->assertDispatched('deliveryOptionUpdated', function ($event, $data) use ($id, $expected) {
                return $data['deliveryOptionId'] === $id &&
                       $data['deliveryOption'] === $expected['option'] &&
                       $data['displayName'] === $expected['display'];
            });
        }
    }

    /** @test */
    public function it_loads_initial_value_from_session()
    {
        // Set session value
        session(['DeliveryOptionID' => 8]);

        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Assert component loaded session value
        $this->assertEquals(8, $component->get('deliveryOptionIDChanged'));
    }

    /** @test */
    public function it_prioritizes_parameter_over_session_value()
    {
        // Set session value
        session(['DeliveryOptionID' => 8]);

        // Mount with explicit parameter
        $component = Livewire::test(DeliveryOptionSelectFlux::class, [
            'deliveryOptionIDChanged' => 2
        ]);

        // Assert parameter takes precedence
        $this->assertEquals(2, $component->get('deliveryOptionIDChanged'));
    }

    /** @test */
    public function it_handles_nonexistent_delivery_option_gracefully()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Try to set non-existent delivery option
        $component->set('deliveryOptionIDChanged', 999);

        // Session should be updated even for non-existent option
        $this->assertEquals(999, session('DeliveryOptionID'));
        
        // But no event should be dispatched for non-existent option
        $component->assertNotDispatched('deliveryOptionUpdated');
    }

    /** @test */
    public function it_supports_wire_model_binding()
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);

        // Simulate wire:model binding from parent component
        $component->set('deliveryOptionIDChanged', 2);

        // Verify the binding works
        $this->assertEquals(2, $component->get('deliveryOptionIDChanged'));
        $this->assertEquals(2, session('DeliveryOptionID'));
        $component->assertDispatched('deliveryOptionUpdated');
    }
}
