<?php

namespace Blashbrook\PAPIClient\Livewire;

use Blashbrook\PAPIClient\Models\DeliveryOption;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Modelable;
use Livewire\Component;

/**
 * Livewire component for selecting a Delivery Option using a Flux select field.
 *
 * @property string|null $selectedOption The ID of the currently selected Delivery Option, bound via wire:model.
 * @property Collection<int, DeliveryOption> $options Collection of all available DeliveryOption models.
 */
class DeliveryOptionSelectFlux extends Component
{
    /** @var string|null The selected ID, synchronized with the parent. */
    #[Modelable]
    public $selectedOption = null;

    /** @var Collection Collection of DeliveryOption models. */
    public Collection $options;

    /** @var array<string, string> Map of Database DeliveryOption name to the desired display name. */
    private $availableDeliveryOptions = [
        'Mailing Address' => 'Mail',
        'Email Address' => 'Email',
        'Phone 1' => 'Phone',
        'TXT Messaging' => 'Text Messaging',
    ];

    /**
     * Mount the component and load all available delivery options.
     *
     * @param  string|null  $selected  The initial selected ID passed from the parent.
     * @return void
     */
    public function mount($selected = null): void
    {
        // Fetch all delivery options from Database
        $allDeliveryOptions = DeliveryOption::all();

        // Filter to only include options that are in our allowed list
        $this->options = $allDeliveryOptions->filter(function ($option) {
            return array_key_exists($option->DeliveryOption, $this->availableDeliveryOptions);
        });

        // Initialize with provided value
        $this->selectedOption = $selected;
    }

    /**
     * Livewire lifecycle hook called when $selectedOption changes via wire:model.live.
     *
     * @param  string  $value  The newly selected ID.
     * @return void
     */
    public function updatedSelectedOption($value): void
    {
        $this->handleUpdate($value);
    }

    /**
     * Handles the update logic and dispatches the 'deliveryOptionUpdated' event with full data.
     *
     * @param  string  $newSelection  The ID of the newly selected delivery option.
     * @return void
     *
     * @dispatch 'deliveryOptionUpdated' { "deliveryOptionId": string, "deliveryOption": string, "displayName": string }
     */
    public function handleUpdate($newSelection)
    {
        // Find the selected delivery option for event data
        $selectedOption = $this->options->firstWhere('DeliveryOptionID', $newSelection);

        if ($selectedOption) {
            // Dispatch event with comprehensive delivery option data
            $this->dispatch('deliveryOptionUpdated', [
                'deliveryOptionId' => $selectedOption->DeliveryOptionID,
                'deliveryOption' => $selectedOption->DeliveryOption,
                'displayName' => $this->availableDeliveryOptions[$selectedOption->DeliveryOption]
                    ?? $selectedOption->DeliveryOption,
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
                'value' => (string) $option->DeliveryOptionID,
                // Use custom display name from our mapping, fallback to original if not found
                'label' => $this->availableDeliveryOptions[$option->DeliveryOption] ?? $option->DeliveryOption,
            ];
        })->toArray();

        // Pass the clean array to the view.
        return view('papiclient::livewire.delivery-option-select-flux', [
            'fluxOptions' => $fluxOptions,
        ]);
    }
}
