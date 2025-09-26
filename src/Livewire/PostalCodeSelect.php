<?php

namespace Blashbrook\PAPIClient\Livewire;

use Blashbrook\PAPIClient\Models\PostalCode;
use Illuminate\Support\Collection;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class PostalCodeSelect extends Component
{
    #[Modelable]
    public $selectedOption = null;

    public Collection $options;
    //public array $availableDeliveryOptions = [];
    public array $attrs = [];

    public function mount($attrs = [])
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
    }

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

    public function render()
    {
        return view('papiclient::livewire.postal-code-select');
    }
}
