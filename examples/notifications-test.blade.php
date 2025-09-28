<div class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-6">Patron Notification Test</h2>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="sendNotification">
        
        {{-- Patron Information --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Patron Information</h3>
            
            <div class="mb-4">
                <label for="patronBarcode" class="block text-sm font-medium text-gray-700 mb-2">
                    Patron Barcode
                </label>
                <input 
                    type="text" 
                    id="patronBarcode"
                    wire:model.live="patronBarcode"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter patron barcode"
                >
                @error('patronBarcode') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>
        </div>

        {{-- Location Selection --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Location Information</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Location (City, State, ZIP)
                </label>
                
                {{-- PostalCodeSelectFlux Component Integration --}}
                <livewire:postal-code-select-flux 
                    wire:model="selectedPostalCode"
                    :selected-postal-code-changed="$selectedPostalCode"
                    display-format="city_state_zip"
                    placeholder="Choose your city and postal code"
                    :attrs="['class' => 'w-full']"
                />
                
                @error('selectedPostalCode') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            {{-- Display Selected Location Details --}}
            @if($userCity && $userState)
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-semibold text-blue-800">Selected Location:</h4>
                    <div class="text-blue-700">
                        <p><strong>City:</strong> {{ $userCity }}</p>
                        <p><strong>State:</strong> {{ $userState }}</p>
                        <p><strong>ZIP Code:</strong> {{ $userPostalCode }}</p>
                        @if($userCounty)
                            <p><strong>County:</strong> {{ $userCounty }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Delivery Method Selection --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Notification Method</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    How should we send the notification?
                </label>
                
                {{-- DeliveryOptionSelectFlux Component Integration --}}
                <livewire:delivery-option-select-flux 
                    wire:model="deliveryOptionIDChanged"
                    :delivery-option-i-d-changed="$deliveryOptionIDChanged"
                />
                
                @error('deliveryOptionIDChanged') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            {{-- Display Notification Preview --}}
            @if($notificationMessage)
                <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                    <h4 class="font-semibold text-yellow-800">Notification Preview:</h4>
                    <p class="text-yellow-700">{{ $notificationMessage }}</p>
                </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="flex space-x-4">
            <button 
                type="submit" 
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                @if(!$isFormValid) disabled @endif
            >
                Send Notification
            </button>
            
            <button 
                type="button" 
                wire:click="resetForm"
                class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
                Reset Form
            </button>
        </div>
    </form>

    {{-- Debug Information (remove in production) --}}
    @if(app()->environment('local'))
        <div class="mt-8 p-4 bg-gray-100 rounded-lg">
            <h4 class="font-semibold mb-2">Debug Information:</h4>
            <div class="text-sm space-y-1">
                <p><strong>Selected Postal Code ID:</strong> {{ $selectedPostalCode ?? 'None' }}</p>
                <p><strong>Delivery Option ID:</strong> {{ $deliveryOptionIDChanged ?? 'None' }}</p>
                <p><strong>Form Valid:</strong> {{ $isFormValid ? 'Yes' : 'No' }}</p>
                <p><strong>Session Postal Code:</strong> {{ session('PostalCodeID', 'None') }}</p>
                <p><strong>Session Delivery Option:</strong> {{ session('DeliveryOptionID', 'None') }}</p>
            </div>
        </div>
    @endif

</div>

{{-- Optional: Add some custom CSS for better styling --}}
<style>
    /* Custom styles for Flux components if needed */
    .flux-select {
        @apply w-full;
    }
</style>