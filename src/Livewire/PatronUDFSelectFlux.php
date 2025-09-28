<?php

namespace Blashbrook\PAPIClient\Livewire;

use Blashbrook\PAPIClient\Models\PatronUdf;
use Illuminate\Support\Collection;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class PatronUDFSelectFlux extends Component
{
    #[Modelable]
    public $selectedPatronUDFChanged;

    public $patronUdfLabel;
    public $placeholder = 'Select an option';
    public Collection $options;
    public array $attrs = [];
    
    /**
     * Available UDF options that can be filtered/customized
     * Override this array to customize which options appear and their display names
     */
    private array $availableUdfOptions = [];

    public function mount($patronUdfLabel = 'School', $selectedPatronUDFChanged = null, $placeholder = null, $attrs = [])
    {
        $this->patronUdfLabel = $patronUdfLabel;
        $this->selectedPatronUDFChanged = $selectedPatronUDFChanged ?? session('PatronUDF_' . $patronUdfLabel, '');
        $this->placeholder = $placeholder ?? "Select {$patronUdfLabel}";
        $this->attrs = $attrs;
        
        $this->loadUdfOptions();
    }
    
    /**
     * Load UDF options from database based on label
     */
    private function loadUdfOptions(): void
    {
        $this->options = collect();
        
        $patronUdf = PatronUdf::where('Label', $this->patronUdfLabel)
            ->where('Display', true)
            ->first();

        if ($patronUdf && $patronUdf->Values) {
            $rawOptions = array_filter(array_map('trim', explode(',', $patronUdf->Values)));
            
            // Convert to collection of objects for consistency
            $this->options = collect($rawOptions)->map(function ($option, $index) {
                return (object) [
                    'value' => $option,
                    'label' => $this->getCustomDisplayName($option),
                    'id' => $index + 1
                ];
            });
            
            // Filter options if availableUdfOptions is configured
            if (!empty($this->availableUdfOptions)) {
                $this->options = $this->options->filter(function ($option) {
                    return array_key_exists($option->value, $this->availableUdfOptions);
                })->map(function ($option) {
                    $option->label = $this->availableUdfOptions[$option->value] ?? $option->value;
                    return $option;
                });
            }
        }
    }
    
    /**
     * Get custom display name for an option
     * Override this method to customize display names
     */
    protected function getCustomDisplayName(string $value): string
    {
        return $this->availableUdfOptions[$value] ?? $value;
    }
    
    /**
     * Handle updates to the selected option
     */
    public function updatedSelectedPatronUDFChanged($value)
    {
        // Store in session for persistence
        session(['PatronUDF_' . $this->patronUdfLabel => $value]);
        
        // Dispatch event for parent components
        $selectedOption = $this->options->firstWhere('value', $value);
        $this->dispatch('patronUdfUpdated', [
            'label' => $this->patronUdfLabel,
            'value' => $value,
            'displayName' => $selectedOption?->label ?? $value
        ]);
    }
    
    /**
     * Get the current selected option details
     */
    public function getSelectedOptionProperty()
    {
        if (!$this->selectedPatronUDFChanged) {
            return null;
        }
        
        return $this->options->firstWhere('value', $this->selectedPatronUDFChanged);
    }

    public function render()
    {
        return view('papiclient::livewire.patron-udf-select-flux');
    }
}
