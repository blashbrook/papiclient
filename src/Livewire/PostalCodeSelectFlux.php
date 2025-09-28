<?php

namespace Blashbrook\PAPIClient\Livewire;

use Blashbrook\PAPIClient\Models\PostalCode;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Modelable;
use Livewire\Component;

/**
 * @class PostalCodeSelectFlux
 */
class PostalCodeSelectFlux extends Component
{
    /**
     * The ID of the currently selected postal code.
     * This property allows two-way binding with the parent component
     * (e.g., PatronNotificationsTest) via wire:model.
     *
     * @var int|null
     */
    public $postalCodeIDChanged;

    /**
     * A collection of filtered PostalCode models from the database.
     *
     * @var Collection<int, PostalCode>
     */
    public $postalCodes;

    /**
     * Initializes the component by fetching all postal codes.
     *
     * @return void
     */
    public function mount($postalCodeIDChanged = null): void
    {
        // Fetch all postal codes from database
        $this->postalCodes = PostalCode::select(
            'id',
            'City',
            'State',
            'PostalCode',
            'County',
            'CountryID'
        )
            ->orderBy('PostalCode')
            ->get();

        // Initialize with provided value, session value, or default to first allowed option
        $this->postalCodeIDChanged = $postalCodeIDChanged
            ?? session('PostalCodeID', $this->postalCodes->first()->id ?? null);
    }
    
    /**
     * Handle updates to the postal code selection.
     * Updates session and dispatches event to notify parent components.
     *
     * @param mixed $value
     * @return void
     */
    public function updatedPostalCodeIDChanged($value): void
    {
        // Store in session for persistence
        session(['PostalCodeID' => $value]);
        
        // Find the selected postal code for event data
        $selectedOption = $this->postalCodes->firstWhere('id', $value);
        
        if ($selectedOption) {
            // Dispatch event with comprehensive postal code data
            $this->dispatch('postalCodeUpdated', [
                'postalCodeId' => $selectedOption->id,
                'postalCode' => $selectedOption->PostalCode,
                'displayName' => $selectedOption->PostalCode
            ]);
        }
    }

    /**
     * Renders the component's view, passing pre-processed options.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {

        $fluxOptions = $this->postalCodes->map(function ($option) {
            return [
                // Cast the ID to a string, which is necessary for HTML select values.
                'value' => (string) $option->id,
                // Use custom display name from our mapping, fallback to original if not found
                'label' => $option->City . ', ' .
                           $option->State . '  ' .
                           $option->PostalCode,
            ];
        })->toArray();

        // Pass the clean array to the view.
        return view('papiclient::livewire.postal-code-select-flux', [
            'fluxOptions' => $fluxOptions,
        ]);
    }

    ////// From Postal Code Select
    public function handleUpdate($selectedPostalCodeID)
    {
        $postalCode = $this->options->firstWhere('id', $selectedPostalCodeID);
        $this->dispatch('postalCodeUpdated', [
            'id' => $postalCode->id,
            'City' => $postalCode->City,
            'State' => $postalCode->State,
            'PostalCode' => $postalCode->PostalCode,
            'County' => $postalCode->County,
            'CountryID' => $postalCode->CountryID,
        ]);
    }

    #[Modelable]
    public $selectedOption = null;

   // public \Illuminate\Support\Collection $options;
    //public array $availableDeliveryOptions = [];
 //   public array $attrs = [];

/*    public function mount($attrs = [])
    {
        $this->attrs = $attrs;
        $this->options = PostalCode::select(
            'id',
            'City',
            'State',
            'PostalCode',
            'County',
            'CountryID'
        )
            ->orderBy('PostalCode')
            ->get();
    }*/
}
