<?php

namespace Tests\Unit;

use Blashbrook\PAPIClient\Livewire\PostalCodeSelectFlux;
use Blashbrook\PAPIClient\Models\PostalCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostalCodeSelectFluxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test PostalCode records
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

        PostalCode::create([
            'PostalCodeID' => 3,
            'PostalCode' => '90210',
            'City' => 'Beverly Hills',
            'State' => 'CA',
            'County' => 'Los Angeles County',
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

        PostalCode::create([
            'PostalCodeID' => 5,
            'PostalCode' => '80301',
            'City' => 'Boulder',
            'State' => 'CO',
            'County' => 'Boulder County',
            'CountryID' => 1
        ]);
    }

    #[Test]
    public function it_can_instantiate_the_component(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->assertStatus(200);
        $this->assertInstanceOf(PostalCodeSelectFlux::class, $component->instance());
    }

    #[Test]
    public function it_loads_all_postal_codes_by_default(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $options = $component->get('options');
        $this->assertCount(5, $options);
    }

    #[Test]
    public function it_orders_postal_codes_by_state_city_zip(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $options = $component->get('options');
        $firstOption = $options->first();
        
        // Should be ordered by State, then City, then PostalCode
        // CA (Beverly Hills), CO (Boulder, Denver), NY (New York)
        $this->assertEquals('CA', $firstOption->State);
        $this->assertEquals('Beverly Hills', $firstOption->City);
    }

    #[Test]
    public function it_sets_default_placeholder(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $this->assertEquals('Select your city and postal code', $component->get('placeholder'));
    }

    #[Test]
    public function it_accepts_custom_placeholder(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'placeholder' => 'Choose your location'
        ]);

        $this->assertEquals('Choose your location', $component->get('placeholder'));
    }

    #[Test]
    public function it_sets_default_display_format(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $this->assertEquals('city_state_zip', $component->get('displayFormat'));
    }

    #[Test]
    public function it_accepts_custom_display_format(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'displayFormat' => 'full'
        ]);

        $this->assertEquals('full', $component->get('displayFormat'));
    }

    #[Test]
    public function it_loads_initial_value_from_session(): void
    {
        Session::put('PostalCodeID', 2);

        $component = Livewire::test(PostalCodeSelectFlux::class);

        $this->assertEquals(2, $component->get('selectedPostalCodeChanged'));
    }

    #[Test]
    public function it_accepts_initial_value_parameter(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'selectedPostalCodeChanged' => 3
        ]);

        $this->assertEquals(3, $component->get('selectedPostalCodeChanged'));
    }

    #[Test]
    public function it_prioritizes_parameter_over_session(): void
    {
        Session::put('PostalCodeID', 2);

        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'selectedPostalCodeChanged' => 3
        ]);

        $this->assertEquals(3, $component->get('selectedPostalCodeChanged'));
    }

    #[Test]
    public function it_filters_by_state(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'filters' => ['State' => 'CO']
        ]);

        $options = $component->get('options');
        $this->assertCount(3, $options); // Denver (2) + Boulder (1)
        
        $states = $options->pluck('State')->unique()->toArray();
        $this->assertEquals(['CO'], $states);
    }

    #[Test]
    public function it_filters_by_multiple_criteria(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'filters' => [
                'State' => 'CO',
                'County' => 'Denver County'
            ]
        ]);

        $options = $component->get('options');
        $this->assertCount(2, $options); // Only Denver postal codes
        
        $counties = $options->pluck('County')->unique()->toArray();
        $this->assertEquals(['Denver County'], $counties);
    }

    #[Test]
    public function it_handles_empty_filters(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'filters' => ['State' => '']
        ]);

        $options = $component->get('options');
        $this->assertCount(5, $options); // Should load all records
    }

    #[Test]
    public function it_updates_session_when_selection_changes(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->set('selectedPostalCodeChanged', 3);

        $this->assertEquals(3, Session::get('PostalCodeID'));
    }

    #[Test]
    public function it_dispatches_postal_code_updated_event_on_change(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->set('selectedPostalCodeChanged', 1);

        $component->assertDispatched('postalCodeUpdated', function ($event, $data) {
            return $data['id'] === 1 &&
                   $data['postalCodeId'] === 1 &&
                   $data['city'] === 'Denver' &&
                   $data['state'] === 'CO' &&
                   $data['postalCode'] === '80202' &&
                   $data['county'] === 'Denver County' &&
                   $data['countryId'] === 1 &&
                   str_contains($data['displayText'], 'Denver');
        });
    }

    #[Test]
    public function it_does_not_dispatch_event_when_value_is_empty(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->set('selectedPostalCodeChanged', null);

        $component->assertNotDispatched('postalCodeUpdated');
    }

    #[Test]
    public function it_does_not_dispatch_event_when_value_is_zero(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->set('selectedPostalCodeChanged', 0);

        $component->assertNotDispatched('postalCodeUpdated');
    }

    #[Test]
    public function it_returns_selected_postal_code_property(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->set('selectedPostalCodeChanged', 1);

        $selectedPostalCode = $component->instance()->getSelectedPostalCodeProperty();
        $this->assertNotNull($selectedPostalCode);
        $this->assertEquals('Denver', $selectedPostalCode->City);
        $this->assertEquals('CO', $selectedPostalCode->State);
        $this->assertEquals('80202', $selectedPostalCode->PostalCode);
    }

    #[Test]
    public function it_returns_null_for_selected_postal_code_when_nothing_selected(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $selectedPostalCode = $component->instance()->getSelectedPostalCodeProperty();
        $this->assertNull($selectedPostalCode);
    }

    #[Test]
    public function it_returns_available_states_property(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $availableStates = $component->instance()->getAvailableStatesProperty();
        $states = $availableStates->toArray();
        
        $this->assertCount(3, $states);
        $this->assertContains('CA', $states);
        $this->assertContains('CO', $states);
        $this->assertContains('NY', $states);
        $this->assertEquals(['CA', 'CO', 'NY'], $states); // Should be sorted
    }

    #[Test]
    public function it_accepts_and_stores_custom_attributes(): void
    {
        $customAttrs = ['class' => 'location-select', 'data-test' => 'postal-code'];

        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'attrs' => $customAttrs
        ]);

        $this->assertEquals($customAttrs, $component->get('attrs'));
    }

    #[Test]
    public function it_can_render_the_component_view(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->assertViewIs('papiclient::livewire.postal-code-select-flux');
    }

    #[Test]
    public function it_filters_options_by_search_term(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        // Filter by city name
        $component->call('filterOptions', 'Denver');
        $filteredOptions = $component->get('filteredOptions');
        
        $this->assertCount(2, $filteredOptions); // Two Denver postal codes
        
        $cities = $filteredOptions->pluck('City')->unique()->toArray();
        $this->assertEquals(['Denver'], $cities);
    }

    #[Test]
    public function it_filters_options_by_state(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->call('filterOptions', 'CA');
        $filteredOptions = $component->get('filteredOptions');
        
        $this->assertCount(1, $filteredOptions);
        $this->assertEquals('CA', $filteredOptions->first()->State);
    }

    #[Test]
    public function it_filters_options_by_postal_code(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->call('filterOptions', '90210');
        $filteredOptions = $component->get('filteredOptions');
        
        $this->assertCount(1, $filteredOptions);
        $this->assertEquals('90210', $filteredOptions->first()->PostalCode);
    }

    #[Test]
    public function it_filters_options_by_county(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->call('filterOptions', 'Boulder');
        $filteredOptions = $component->get('filteredOptions');
        
        $this->assertCount(1, $filteredOptions);
        $this->assertEquals('Boulder County', $filteredOptions->first()->County);
    }

    #[Test]
    public function it_resets_filtered_options_with_empty_search(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        // First filter
        $component->call('filterOptions', 'Denver');
        $this->assertCount(2, $component->get('filteredOptions'));

        // Reset with empty search
        $component->call('filterOptions', '');
        $this->assertCount(5, $component->get('filteredOptions'));
    }

    #[Test]
    public function it_handles_case_insensitive_search(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->call('filterOptions', 'denver');
        $filteredOptions = $component->get('filteredOptions');
        
        $this->assertCount(2, $filteredOptions);
    }

    #[Test]
    public function it_handles_partial_match_search(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->call('filterOptions', 'Bev');
        $filteredOptions = $component->get('filteredOptions');
        
        $this->assertCount(1, $filteredOptions);
        $this->assertEquals('Beverly Hills', $filteredOptions->first()->City);
    }

    #[Test]
    public function it_returns_all_options_when_search_finds_nothing(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $component->call('filterOptions', 'NonexistentPlace');
        $filteredOptions = $component->get('filteredOptions');
        
        $this->assertCount(0, $filteredOptions);
    }

    #[Test]
    public function it_handles_multiple_filters_and_search(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'filters' => ['State' => 'CO']
        ]);

        // Should only search within CO postal codes
        $component->call('filterOptions', 'Denver');
        $filteredOptions = $component->get('filteredOptions');
        
        $this->assertCount(2, $filteredOptions);
        
        // All results should still be in CO
        $states = $filteredOptions->pluck('State')->unique()->toArray();
        $this->assertEquals(['CO'], $states);
    }

    #[Test]
    public function it_maintains_filtered_options_separate_from_original_options(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class);

        $originalCount = $component->get('options')->count();
        
        $component->call('filterOptions', 'Denver');
        
        // Original options should remain unchanged
        $this->assertEquals($originalCount, $component->get('options')->count());
        
        // But filtered options should be different
        $this->assertNotEquals($originalCount, $component->get('filteredOptions')->count());
    }

    #[Test]
    public function it_dispatches_event_with_display_text_in_correct_format(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'displayFormat' => 'full'
        ]);

        $component->set('selectedPostalCodeChanged', 1);

        $component->assertDispatched('postalCodeUpdated', function ($event, $data) {
            return str_contains($data['displayText'], 'Denver, CO 80202 (Denver County)');
        });
    }

    #[Test]
    public function it_handles_city_zip_display_format(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'displayFormat' => 'city_zip'
        ]);

        $component->set('selectedPostalCodeChanged', 1);

        $component->assertDispatched('postalCodeUpdated', function ($event, $data) {
            return $data['displayText'] === 'Denver 80202';
        });
    }

    #[Test]
    public function it_handles_city_state_zip_display_format(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'displayFormat' => 'city_state_zip'
        ]);

        $component->set('selectedPostalCodeChanged', 1);

        $component->assertDispatched('postalCodeUpdated', function ($event, $data) {
            return $data['displayText'] === 'Denver, CO 80202';
        });
    }

    #[Test]
    public function it_falls_back_to_default_format_for_invalid_display_format(): void
    {
        $component = Livewire::test(PostalCodeSelectFlux::class, [
            'displayFormat' => 'invalid_format'
        ]);

        $component->set('selectedPostalCodeChanged', 1);

        $component->assertDispatched('postalCodeUpdated', function ($event, $data) {
            return $data['displayText'] === 'Denver, CO 80202';
        });
    }
}