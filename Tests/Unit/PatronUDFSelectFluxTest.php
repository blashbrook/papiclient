<?php

namespace Tests\Unit;

use Blashbrook\PAPIClient\Livewire\PatronUDFSelectFlux;
use Blashbrook\PAPIClient\Models\PatronUdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PatronUDFSelectFluxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test PatronUdf records
        PatronUdf::create([
            'PatronUdfID' => 1,
            'Label' => 'School',
            'Display' => true,
            'Values' => 'Elementary School,Middle School,High School,College,Adult Education',
            'Required' => true,
            'DefaultValue' => 'High School'
        ]);

        PatronUdf::create([
            'PatronUdfID' => 2,
            'Label' => 'Department',
            'Display' => true,
            'Values' => 'Math,Science,English,History,Art,Music',
            'Required' => false,
            'DefaultValue' => null
        ]);

        PatronUdf::create([
            'PatronUdfID' => 3,
            'Label' => 'Grade',
            'Display' => false, // This should be filtered out
            'Values' => '9th,10th,11th,12th',
            'Required' => false,
            'DefaultValue' => null
        ]);
    }

    #[Test]
    public function it_can_instantiate_the_component(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $component->assertStatus(200);
        $this->assertInstanceOf(PatronUDFSelectFlux::class, $component->instance());
    }

    #[Test]
    public function it_loads_udf_options_based_on_label(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $options = $component->get('options');
        $this->assertCount(5, $options);
        
        $optionValues = $options->pluck('value')->toArray();
        $this->assertContains('Elementary School', $optionValues);
        $this->assertContains('Middle School', $optionValues);
        $this->assertContains('High School', $optionValues);
        $this->assertContains('College', $optionValues);
        $this->assertContains('Adult Education', $optionValues);
    }

    #[Test]
    public function it_only_loads_udfs_with_display_true(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'Grade'
        ]);

        $options = $component->get('options');
        $this->assertCount(0, $options);
    }

    #[Test]
    public function it_handles_different_udf_labels(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'Department'
        ]);

        $options = $component->get('options');
        $this->assertCount(6, $options);
        
        $optionValues = $options->pluck('value')->toArray();
        $this->assertContains('Math', $optionValues);
        $this->assertContains('Science', $optionValues);
        $this->assertContains('English', $optionValues);
    }

    #[Test]
    public function it_sets_default_placeholder(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $this->assertEquals('Select School', $component->get('placeholder'));
    }

    #[Test]
    public function it_accepts_custom_placeholder(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School',
            'placeholder' => 'Choose your educational level'
        ]);

        $this->assertEquals('Choose your educational level', $component->get('placeholder'));
    }

    #[Test]
    public function it_loads_initial_value_from_session(): void
    {
        Session::put('PatronUDF_School', 'High School');

        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $this->assertEquals('High School', $component->get('selectedPatronUDFChanged'));
    }

    #[Test]
    public function it_accepts_initial_value_parameter(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School',
            'selectedPatronUDFChanged' => 'College'
        ]);

        $this->assertEquals('College', $component->get('selectedPatronUDFChanged'));
    }

    #[Test]
    public function it_prioritizes_parameter_over_session(): void
    {
        Session::put('PatronUDF_School', 'High School');

        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School',
            'selectedPatronUDFChanged' => 'College'
        ]);

        $this->assertEquals('College', $component->get('selectedPatronUDFChanged'));
    }

    #[Test]
    public function it_updates_session_when_selection_changes(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $component->set('selectedPatronUDFChanged', 'College');

        $this->assertEquals('College', Session::get('PatronUDF_School'));
    }

    #[Test]
    public function it_dispatches_patron_udf_updated_event_on_change(): void
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
    public function it_handles_empty_udf_values(): void
    {
        PatronUdf::create([
            'PatronUdfID' => 4,
            'Label' => 'EmptyUDF',
            'Display' => true,
            'Values' => '',
            'Required' => false
        ]);

        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'EmptyUDF'
        ]);

        $options = $component->get('options');
        $this->assertCount(0, $options);
    }

    #[Test]
    public function it_handles_null_udf_values(): void
    {
        PatronUdf::create([
            'PatronUdfID' => 5,
            'Label' => 'NullUDF',
            'Display' => true,
            'Values' => null,
            'Required' => false
        ]);

        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'NullUDF'
        ]);

        $options = $component->get('options');
        $this->assertCount(0, $options);
    }

    #[Test]
    public function it_trims_whitespace_from_option_values(): void
    {
        PatronUdf::create([
            'PatronUdfID' => 6,
            'Label' => 'WhitespaceUDF',
            'Display' => true,
            'Values' => ' Option One , Option Two , Option Three ',
            'Required' => false
        ]);

        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'WhitespaceUDF'
        ]);

        $options = $component->get('options');
        $optionValues = $options->pluck('value')->toArray();
        
        $this->assertContains('Option One', $optionValues);
        $this->assertContains('Option Two', $optionValues);
        $this->assertContains('Option Three', $optionValues);
        $this->assertNotContains(' Option One ', $optionValues);
    }

    #[Test]
    public function it_filters_empty_options_after_trim(): void
    {
        PatronUdf::create([
            'PatronUdfID' => 7,
            'Label' => 'FilterUDF',
            'Display' => true,
            'Values' => 'Valid Option,,   ,Another Valid Option, ',
            'Required' => false
        ]);

        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'FilterUDF'
        ]);

        $options = $component->get('options');
        $this->assertCount(2, $options);
        
        $optionValues = $options->pluck('value')->toArray();
        $this->assertContains('Valid Option', $optionValues);
        $this->assertContains('Another Valid Option', $optionValues);
    }

    #[Test]
    public function it_handles_nonexistent_udf_label(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'NonexistentLabel'
        ]);

        $options = $component->get('options');
        $this->assertCount(0, $options);
    }

    #[Test]
    public function it_returns_selected_option_property(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $component->set('selectedPatronUDFChanged', 'High School');

        $selectedOption = $component->instance()->getSelectedOptionProperty();
        $this->assertNotNull($selectedOption);
        $this->assertEquals('High School', $selectedOption->value);
        $this->assertEquals('High School', $selectedOption->label);
    }

    #[Test]
    public function it_returns_null_for_selected_option_when_nothing_selected(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $selectedOption = $component->instance()->getSelectedOptionProperty();
        $this->assertNull($selectedOption);
    }

    #[Test]
    public function it_maintains_unique_session_keys_per_label(): void
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

    #[Test]
    public function it_accepts_and_stores_custom_attributes(): void
    {
        $customAttrs = ['class' => 'custom-select', 'data-test' => 'school-select'];

        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School',
            'attrs' => $customAttrs
        ]);

        $this->assertEquals($customAttrs, $component->get('attrs'));
    }

    #[Test]
    public function it_can_render_the_component_view(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $component->assertViewIs('papiclient::livewire.patron-udf-select-flux');
    }

    #[Test]
    public function it_handles_case_sensitive_label_matching(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'school' // lowercase
        ]);

        $options = $component->get('options');
        $this->assertCount(0, $options); // Should not match 'School'
    }

    #[Test]
    public function it_creates_options_with_sequential_ids(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $options = $component->get('options');
        $ids = $options->pluck('id')->toArray();
        
        $this->assertEquals([1, 2, 3, 4, 5], $ids);
    }

    #[Test]
    public function it_dispatches_event_with_correct_display_name(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        $component->set('selectedPatronUDFChanged', 'Elementary School');

        $component->assertDispatched('patronUdfUpdated', function ($event, $data) {
            return $data['label'] === 'School' &&
                   $data['value'] === 'Elementary School' &&
                   $data['displayName'] === 'Elementary School';
        });
    }

    #[Test]
    public function it_does_not_dispatch_event_when_value_is_empty(): void
    {
        $component = Livewire::test(PatronUDFSelectFlux::class, [
            'patronUdfLabel' => 'School'
        ]);

        // Set to empty value
        $component->set('selectedPatronUDFChanged', '');

        $component->assertNotDispatched('patronUdfUpdated');
    }
}