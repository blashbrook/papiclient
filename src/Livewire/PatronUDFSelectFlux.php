<?php

    namespace Blashbrook\PAPIClient\Livewire;

    use Blashbrook\PAPIClient\Models\PatronUdf;
    use Illuminate\Support\Collection;
    use Livewire\Attributes\Modelable;
    use Livewire\Component;

    /**
     * @property string|null $selectedOption The currently selected UDF value, bound via wire:model.
     * @property string $udfLabel The UDF Label used to look up options in the Database.
     * @property string $placeholder The placeholder text for the select input.
     * @property Collection<array> $options Collection of UDF options, where each item is ['value' => string, 'label' => string].
     */
    class PatronUDFSelectFlux extends Component
    {

        #[Modelable]
        public $selectedOption = null;

        /**
         * UDF options formatted for the flux:select component.
         * @var Collection
         */
        public Collection $options;

        /**
         * The UDF label, passed as a prop from the parent.
         * @var string
         */
        public $udfLabel = '';

        /**
         * The placeholder text, passed as a prop from the parent.
         * @var string
         */
        public $placeholder = '';

        /**
         * Mounts the component and loads UDF options based on the label.
         *
         * @param  string|null  $selected  The initial value from the parent's wire:model.
         * @param  string  $udfLabel  The label of the UDF field (e.g., 'Non-Resident').
         * @param  string  $placeholder  The placeholder text.
         * @return void
         */
        public function mount($selected = null, $udfLabel = '', $placeholder = 'Select an option')
        {
            // Set properties passed as props
            $this->udfLabel = $udfLabel;
            $this->placeholder = $placeholder;

            // Set the initial selected value from the parent
            $this->selectedOption = $selected;

            // Load UDF options
            $patronUdf = PatronUdf::where('Label', $this->udfLabel)->first();

            $rawOptions = collect();
            if ($patronUdf && $patronUdf->Values) {
                $rawOptions = collect(array_filter(array_map('trim', explode(',', $patronUdf->Values))));
            }

            // Map the string values to a Collection of associative arrays for easier lookup
            $this->options = $rawOptions->map(function ($value) {
                return [
                    'value' => $value,
                    'label' => $value,
                ];
            });
        }

        /**
         * Livewire lifecycle hook called when $selectedOption changes.
         *
         * @param  string  $value  The newly selected UDF value.
         * @return void
         */
        public function updatedSelectedOption($value): void
        {
            $this->handleUpdate($value);
        }

        /**
         * Handles the selection update and dispatches the 'patronUdfUpdated' event.
         *
         * @param  string  $newSelection  The new selected value.
         * @return void
         *
         * @dispatch 'patronUdfUpdated' {
         * "label": string,     // The UDF Label
         * "value": string,     // The selected UDF value
         * "displayName": string // The selected UDF display name (same as value)
         * }
         */
        public function handleUpdate($newSelection): void
        {
            // Find the selected option details in the options collection
            $selectedOption = $this->options->firstWhere('value', $newSelection);

            if ($selectedOption) {
                // Dispatch the event with the full details
                $this->dispatch('patronUdfUpdated', [
                    'label' => $this->udfLabel,
                    'value' => $selectedOption['value'],
                    'displayName' => $selectedOption['label'],
                ]);

                // Persist to session
                session([$this->udfLabel => $newSelection]);
            }
        }

        public function render()
        {
            // Use the already formatted options
            $fluxOptions = $this->options->toArray();

            return view('papiclient::livewire.patron-udf-select-flux', [
                'fluxOptions' => $fluxOptions,
            ]);
        }
    }