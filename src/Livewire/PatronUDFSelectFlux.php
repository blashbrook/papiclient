<?php

    namespace Blashbrook\PAPIClient\Livewire;

    use Blashbrook\PAPIClient\Models\PatronUdf;
    use Illuminate\Support\Collection;
    use Livewire\Attributes\Modelable;
    use Livewire\Component;

    class PatronUDFSelectFlux extends Component
    {

        #[Modelable]
        public $selectedOption = null;

        // Must be a Collection to use map/firstWhere without errors
        public Collection $options;

        // Define the UDF label to fetch
        public $udfLabel = '';
        public $placeholder = '';

        public function mount($selected, $udfLabel, $placeholder)
        {
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

        public function updatedSelectedOption($value): void
        {
            $this->handleUpdate($value);
        }

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