<?php

namespace Blashbrook\PAPIClient\Livewire;

use Blashbrook\PAPIClient\Models\PostalCode;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Modelable;
use Livewire\Component;

/**
 * Livewire component for selecting a Postal Code using a Flux select field.
 *
 * @property string|null $selectedOption The ID of the currently selected Postal Code, bound via wire:model.
 * @property Collection<int, PostalCode> $options Collection of all available PostalCode models.
 */
class PostalCodeSelectFlux extends Component
{

    /** @var string|null The selected ID, synchronized with the parent. */
    #[Modelable]
    public $selectedOption = null;

    /** @var Collection Collection of PostalCode models. */
    public Collection $options;

    /**
     * Mount the component and load all postal codes.
     *
     * @param string|null $selected The initial selected ID passed from the parent.
     * @return void
     */
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

    /**
     * Livewire lifecycle hook called when $selectedOption changes via wire:model.live.
     * @param string $value The newly selected ID.
     * @return void
     */
    public function updatedSelectedOption($value)
    {
        $this->handleUpdate($value);
    }

    /**
     * Handles the update logic and dispatches the 'postalCodeUpdated' event with full data.
     *
     * @param string $newSelection The ID of the newly selected postal code.
     * @return void
     *
     * @dispatch 'postalCodeUpdated' { "id": string, "City": string, "State": string, "PostalCode": string, "County": string, "CountryID": string }
     */
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

    /**
     * Prepares options for the Flux select view and renders.
     *
     * @return View|Factory
     */
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
