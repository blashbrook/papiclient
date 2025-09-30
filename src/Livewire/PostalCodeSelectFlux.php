<?php

namespace Blashbrook\PAPIClient\Livewire;

use Blashbrook\PAPIClient\Models\PostalCode;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class PostalCodeSelectFlux extends Component
{

    #[Modelable]
    public $selectedOption = null;

    public Collection $options;

    public function mount($selected): void
    {
        $this->options = PostalCode::select(
            'id',
            'City',
            'State',
            'PostalCode',
            'County',
            'CountryID'
        )->orderBy('PostalCode')->get();
        $this->selectedOption = $selected;
    }

    public function updatedSelectedOption($value)
    {
        $this->handleUpdate($value);
    }
    public function handleUpdate($newSelection): void
    {
        $selectedOption = $this->options->firstWhere('id', (string) $newSelection);
        
        if ($selectedOption) {
            $this->dispatch('postalCodeUpdated', [
                'id' => $selectedOption->id,
                'City' => $selectedOption->City,
                'State' => $selectedOption->State,
                'PostalCode' => $selectedOption->PostalCode,
                'County' => $selectedOption->County,
                'CountryID' => $selectedOption->CountryID
            ]);
        }
    }

    public function render()
    {

        $fluxOptions = $this->options->map(function ($option) {
            return [
                // Cast the ID to a string, which is necessary for HTML select values.
                'value' => (string) $option->id,
                // Use custom display name from our mapping, fallback to original if not found
                'label' => $option->City . ', ' .
                           $option->State . '  ' .
                           $option->PostalCode,
            ];
        })->toArray();

        return view('papiclient::livewire.postal-code-select-flux', [
            'fluxOptions' => $fluxOptions,
        ]);
    }

}
