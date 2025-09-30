<?php

namespace Blashbrook\PAPIClient\Livewire;

use Blashbrook\PAPIClient\Models\DeliveryOption;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

/**
 * @class DeliveryOptionSelectFlux
 */
class DeliveryOptionSelectFlux extends Component
{
    /**
     * The ID of the currently selected delivery option.
     * This property allows two-way binding with the parent component
     * (e.g., PatronNotificationsTest) via wire:model.
     *
     * @var int|null
     */
    public $deliveryOptionIDChanged;

    /**
     * A collection of filtered DeliveryOption models from the database.
     *
     * @var Collection<int, DeliveryOption>
     */
    public $deliveryOptions;

    /**
     * Array of allowed delivery options with custom display names.
     * Only options matching these keys will be shown in the select.
     *
     * @var array
     */
    private $availableDeliveryOptions = [
        'Mailing Address' => 'Mail',
        'Email Address' => 'Email',
        'Phone 1' => 'Phone',
        'TXT Messaging' => 'Text Messaging',
    ];

    /**
     * Initializes the component by fetching all delivery options.
     *
     * @return void
     */
    public function mount($deliveryOptionIDChanged = null): void
    {
        // Fetch all delivery options from database
        $allDeliveryOptions = DeliveryOption::all();

        // Filter to only include options that are in our allowed list
        $this->deliveryOptions = $allDeliveryOptions->filter(function ($option) {
            return array_key_exists($option->DeliveryOption, $this->availableDeliveryOptions);
        });

        // Initialize with provided value, session value, or default to first allowed option
        $this->deliveryOptionIDChanged = $deliveryOptionIDChanged 
            ?? session('DeliveryOptionID', $this->deliveryOptions->first()->DeliveryOptionID ?? null);
    }
    
    /**
     * Handle updates to the delivery option selection.
     * Updates session and dispatches event to notify parent components.
     *
     * @param mixed $value
     * @return void
     */
    public function updatedDeliveryOptionIDChanged($value): void
    {
        // Store in session for persistence
        session(['DeliveryOptionID' => $value]);
        
        // Find the selected delivery option for event data
        $selectedOption = $this->deliveryOptions->firstWhere('DeliveryOptionID', $value);
        
        if ($selectedOption) {
            // Dispatch event with comprehensive delivery option data
            $this->dispatch('deliveryOptionUpdated', [
                'deliveryOptionId' => $selectedOption->DeliveryOptionID,
                'deliveryOption' => $selectedOption->DeliveryOption,
                'displayName' => $this->availableDeliveryOptions[$selectedOption->DeliveryOption] 
                    ?? $selectedOption->DeliveryOption
            ]);
        }
    }

    /**
     * Renders the component's view, passing pre-processed options.
     *
     * @return View|Factory
     */
    public function render()
    {

        $fluxOptions = $this->deliveryOptions->map(function ($option) {
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
