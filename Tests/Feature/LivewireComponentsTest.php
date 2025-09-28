<?php

namespace Tests\Feature;

use Blashbrook\PAPIClient\Livewire\DeliveryOptionSelectFlux;
use Blashbrook\PAPIClient\Livewire\PatronUDFSelectFlux;
use Blashbrook\PAPIClient\Livewire\PostalCodeSelectFlux;
use Blashbrook\PAPIClient\Models\DeliveryOption;
use Blashbrook\PAPIClient\Models\PatronUdf;
use Blashbrook\PAPIClient\Models\PostalCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LivewireComponentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data for all components
        $this->createDeliveryOptions();
        $this->createPatronUdfs();
        $this->createPostalCodes();
    }

    private function createDeliveryOptions(): void
    {
        DeliveryOption::create([
            'DeliveryOptionID' => 1,
            'DeliveryOption' => 'Mailing Address'
        ]);

        DeliveryOption::create([
            'DeliveryOptionID' => 2,
            'DeliveryOption' => 'Email Address'
        ]);

        DeliveryOption::create([
            'DeliveryOptionID' => 3,
            'DeliveryOption' => 'Phone 1'
        ]);

        DeliveryOption::create([
            'DeliveryOptionID' => 8,
            'DeliveryOption' => 'TXT Messaging'
        ]);
    }

    private function createPatronUdfs(): void
    {
        PatronUdf::create([
            'PatronUdfID' => 1,
            'Label' => 'School',
            'Display' => true,
            'Values' => 'Elementary School,Middle School,High School,College',
            'Required' => true
        ]);

        PatronUdf::create([
            'PatronUdfID' => 2,
            'Label' => 'Department',
            'Display' => true,
            'Values' => 'Math,Science,English,History',
            'Required' => false
        ]);
    }

    private function createPostalCodes(): void
    {
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
            'PostalCode' => '90210',
            'City' => 'Beverly Hills',
            'State' => 'CA',
            'County' => 'Los Angeles County',
            'CountryID' => 1
        ]);
    }

    // DeliveryOptionSelectFlux Feature Tests

    #[Test]
    public function delivery_option_component_can_be_rendered(): void
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);
        
        $component->assertStatus(200)
                 ->assertViewIs('papiclient::livewire.delivery-option-select-flux');
    }

    #[Test]
    public function delivery_option_component_loads_filtered_options(): void
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);
        
        $options = $component->get('filteredOptions');
        
        // Should only show the options defined in availableDeliveryOptions
        $this->assertGreaterThan(0, $options->count());
        
        // Verify it's not showing all database records
        $this->assertLessThanOrEqual(4, $options->count());
    }

    #[Test]
    public function delivery_option_component_persists_selection_in_session(): void
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);
        
        $component->set('deliveryOptionIDChanged', 2);
        
        $this->assertEquals(2, Session::get('DeliveryOptionID'));
    }

    #[Test]
    public function delivery_option_component_dispatches_updated_event(): void
    {
        $component = Livewire::test(DeliveryOptionSelectFlux::class);
        
        $component->set('deliveryOptionIDChanged', 2);
        
        $component->assertDispatched('deliveryOptionUpdated', function ($event, $data) {
            return isset($data['deliveryOptionId']) &&
                   isset($data['deliveryOption']) &&
                   isset($data['displayName']) &&
                   $data['deliveryOptionId'] == 2;
        });
    }

    // PatronUDFSelectFlux Feature Tests

    #[Test]
    public function patron_udf_component_can_be_rendered(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        
        $component->assertStatus(200)
                 ->assertViewIs('papiclient::livewire.patron-udf-select-flux');
    }

    #[Test]
    public function patron_udf_component_loads_options_for_specified_label(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        
        $options = $component->get('options');
        $this->assertCount(4, $options);
        
        $optionValues = $options->pluck('value')->toArray();
        $this->assertContains('Elementary School', $optionValues);
        $this->assertContains('High School', $optionValues);
    }

    #[Test]
    public function patron_udf_component_handles_different_labels(): void
    {
        $schoolComponent = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        
        $deptComponent = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'Department'
        ]);
        
        $schoolOptions = $schoolComponent->get('options');
        $deptOptions = $deptComponent->get('options');
        
        $this->assertCount(4, $schoolOptions);
        $this->assertCount(4, $deptOptions);
        
        $this->assertNotEquals(
            $schoolOptions->pluck('value')->toArray(),
            $deptOptions->pluck('value')->toArray()
        );
    }

    #[Test]
    public function patron_udf_component_persists_selection_with_label_specific_session(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        
        $component->set('selectedPatronUDFChanged', 'High School');
        
        $this->assertEquals('High School', Session::get('PatronUDF_School'));
    }

    #[Test]
    public function patron_udf_component_dispatches_updated_event_with_label_info(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        
        $component->set('selectedPatronUDFChanged', 'College');
        
        $component->assertDispatched('patronUdfUpdated', [
            'label' => 'School',
            'value' => 'College',
            'displayName' => 'College'
        ]);
    }

    #[Test]
    public function patron_udf_component_handles_multiple_instances_independently(): void
    {
        $schoolComponent = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        
        $deptComponent = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'Department'
        ]);
        
        $schoolComponent->set('selectedPatronUDFChanged', 'College');
        $deptComponent->set('selectedPatronUDFChanged', 'Math');
        
        $this->assertEquals('College', Session::get('PatronUDF_School'));
        $this->assertEquals('Math', Session::get('PatronUDF_Department'));
    }

    // PostalCodeSelectFlux Feature Tests

    #[Test]
    public function postal_code_component_can_be_rendered(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);
        
        $component->assertStatus(200)
                 ->assertViewIs('papiclient::livewire.postal-code-select-flux');
    }

    #[Test]
    public function postal_code_component_loads_all_postal_codes(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);
        
        $options = $component->get('options');
        $this->assertCount(2, $options);
        
        $cities = $options->pluck('City')->toArray();
        $this->assertContains('Denver', $cities);
        $this->assertContains('Beverly Hills', $cities);
    }

    #[Test]
    public function postal_code_component_can_filter_by_state(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'filters' => ['State' => 'CO']
        ]);
        
        $options = $component->get('options');
        $this->assertCount(1, $options);
        
        $option = $options->first();
        $this->assertEquals('CO', $option->State);
        $this->assertEquals('Denver', $option->City);
    }

    #[Test]
    public function postal_code_component_supports_different_display_formats(): void
    {
        $cityZipComponent = Livewire::test(PostalCodeSelectFlux::class, [
            'displayFormat' => 'city_zip'
        ]);
        
        $fullComponent = Livewire::test(PostalCodeSelectFlux::class, [
            'displayFormat' => 'full'
        ]);
        
        $this->assertEquals('city_zip', $cityZipComponent->get('displayFormat'));
        $this->assertEquals('full', $fullComponent->get('displayFormat'));
    }

    #[Test]
    public function postal_code_component_persists_selection_in_session(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);
        
        $component->set('selectedPostalCodeChanged', 1);
        
        $this->assertEquals(1, Session::get('PostalCodeID'));
    }

    #[Test]
    public function postal_code_component_dispatches_comprehensive_update_event(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);
        
        $component->set('selectedPostalCodeChanged', 1);
        
        $component->assertDispatched('postalCodeUpdated', function ($event, $data) {
            return isset($data['id']) &&
                   isset($data['city']) &&
                   isset($data['state']) &&
                   isset($data['postalCode']) &&
                   isset($data['displayText']) &&
                   $data['city'] === 'Denver' &&
                   $data['state'] === 'CO';
        });
    }

    #[Test]
    public function postal_code_component_can_search_and_filter(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);
        
        $component->call('filterOptions', 'Denver');
        $filteredOptions = $component->get('filteredOptions');
        
        $this->assertCount(1, $filteredOptions);
        $this->assertEquals('Denver', $filteredOptions->first()->City);
    }

    // Cross-Component Integration Tests

    #[Test]
    public function multiple_components_can_coexist_without_interference(): void
    {
        $deliveryComponent = Livewire::test(DeliveryOptionSelectFlux::class);
        $patronUdfComponent = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        $postalCodeComponent = Livewire::test(PostalCodeSelectFlux::class);
        
        // Set values in each component
        $deliveryComponent->set('deliveryOptionIDChanged', 2);
        $patronUdfComponent->set('selectedPatronUDFChanged', 'College');
        $postalCodeComponent->set('selectedPostalCodeChanged', 1);
        
        // Verify each component maintains its own session state
        $this->assertEquals(2, Session::get('DeliveryOptionID'));
        $this->assertEquals('College', Session::get('PatronUDF_School'));
        $this->assertEquals(1, Session::get('PostalCodeID'));
        
        // Verify components still work independently
        $this->assertCount(4, $patronUdfComponent->get('options'));
        $this->assertCount(2, $postalCodeComponent->get('options'));
    }

    #[Test]
    public function components_handle_missing_database_data_gracefully(): void
    {
        // Test with non-existent UDF label
        $patronUdfComponent = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'NonExistentLabel'
        ]);
        
        $this->assertCount(0, $patronUdfComponent->get('options'));
        $patronUdfComponent->assertStatus(200);
    }

    #[Test]
    public function components_maintain_session_state_across_page_loads(): void
    {
        // First component instance sets a value
        $component1 = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        $component1->set('selectedPatronUDFChanged', 'High School');
        
        // Second component instance should load the session value
        $component2 = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        
        $this->assertEquals('High School', $component2->get('selectedPatronUDFChanged'));
    }

    #[Test]
    public function components_can_reset_and_clear_selections(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        
        // Set a value
        $component->set('selectedPatronUDFChanged', 'College');
        $this->assertEquals('College', Session::get('PatronUDF_School'));
        
        // Clear the value
        $component->set('selectedPatronUDFChanged', '');
        $this->assertEquals('', Session::get('PatronUDF_School'));
    }

    #[Test]
    public function postal_code_component_orders_results_consistently(): void
    {
        // Add more postal codes to test ordering
        PostalCode::create([
            'PostalCodeID' => 3,
            'PostalCode' => '80203',
            'City' => 'Denver',
            'State' => 'CO',
            'County' => 'Denver County',
            'CountryID' => 1
        ]);
        
        PostalCode::create([
            'PostalCodeID' => 4,
            'PostalCode' => '10001',
            'City' => 'New York',
            'State' => 'NY',
            'County' => 'New York County',
            'CountryID' => 1
        ]);
        
        $component = Livewire::test(PostalCodeSelectFlux::class);
        
        $options = $component->get('options');
        $states = $options->pluck('State')->toArray();
        
        // Should be ordered by State, then City, then PostalCode
        $this->assertEquals(['CA', 'CO', 'CO', 'NY'], $states);
    }

    #[Test]
    public function all_components_render_with_flux_ui_elements(): void
    {
        $deliveryComponent = Livewire::test(DeliveryOptionSelectFlux::class);
        $patronUdfComponent = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);
        $postalCodeComponent = Livewire::test(PostalCodeSelectFlux::class);
        
        // All components should render successfully
        $deliveryComponent->assertStatus(200);
        $patronUdfComponent->assertStatus(200);
        $postalCodeComponent->assertStatus(200);
        
        // All should use their respective Flux templates
        $deliveryComponent->assertViewIs('papiclient::livewire.delivery-option-select-flux');
        $patronUdfComponent->assertViewIs('papiclient::livewire.patron-udf-select-flux');
        $postalCodeComponent->assertViewIs('papiclient::livewire.postal-code-select-flux');
    }
}