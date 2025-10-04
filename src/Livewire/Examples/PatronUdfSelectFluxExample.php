<?php

    namespace Blashbrook\PAPIClient\Livewire\Examples;

    use Livewire\Attributes\{Layout, On};
    use Livewire\Component;

    /**
     * Component to test and demonstrate the PatronUDFSelectFlux component.
     * Handles configuration and receives data from the child component.
     *
     * @property string $udfLabel The UDF name in Polaris.
     * @property string $placeholder Placeholder text for the select input.
     * @property string $udfRegistrationField The Polaris internal field name (User1-User5).
     * @property string|null $selectedUdfOption The currently selected UDF value, bound via wire:model.
     * @property array $udfData Holds the full UDF data received from the child component or session.
     */
    class PatronUdfSelectFluxExample extends Component
    {

        /**
         * Edit $udfLabel, $placeholder, and $udfRegistrationField to configure the Patron UDF select field.
         *
         * NOTE: The $udfLabel and $udfRegistrationField values are crucial and must match settings found in:
         * Polaris -> SA -> Parameters -> Patron registration options -> Data Field Defaults -> UDFs
         *
         * The $udfRegistrationField value is positional:
         * - First UDF field is 'User1', the second is 'User2', etc.
         *
         * Session Persistence:
         * The component uses the $udfRegistrationField value as the session key
         * to store the selected UDF value for persistence.
         */
        /**
         * @var string $udfLabel The display name of the UDF Field in Polaris (e.g., 'Non-Resident').
         */
        public $udfLabel = 'Non-Resident';

        /**
         * @var string $placeholder Placeholder text for the option select input (e.g., 'Select one').
         */
        public $placeholder = 'Select one';

        /**
         * @var string $udfRegistrationField Field name used in the PatronRegistration table (User1-User5).
         */
        public $udfRegistrationField = 'User3';

        /*
         * Standard Code for UDF select field.  Do not edit
         */

        /** @var string|null The selected UDF value. */
        public $selectedUdfOption = null;

        /** @var array The full UDF data: ['label', 'value', 'displayName']. */
        public $udfData = [];

        /**
         * Initializes selected UDF option from the session.
         * @return void
         */
        public function mount()
        {
            $this->selectedUdfOption = session($this->udfLabel, '');

            if ($this->selectedUdfOption) {
                $this->udfData = [
                    'label' => $this->udfLabel,
                    'value' => $this->selectedUdfOption,
                    'displayName' => $this->selectedUdfOption,
                ];
            }
        }

        /**
         * Handles the 'patronUdfUpdated' event dispatched by the child component.
         *
         * @param  array  $data  The event payload: ['label' => string, 'value' => string, 'displayName' => string].
         * @return void
         */
        #[On('patronUdfUpdated')]
        public function handlePatronUdfUpdated(array $data)
        {
            $this->selectedUdfOption = $data['value'];
            $this->udfData = $data;
        }

        #[Layout('components.layouts.app')]
        public function render()
        {
            return view('papiclient::livewire.examples.patron-udf-select-flux-example');
        }
    }
