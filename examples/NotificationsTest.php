<?php

namespace App\Http\Livewire;

use Blashbrook\PAPIClient\Models\PostalCode;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Examples component showing how to integrate PostalCodeSelectFlux
 * Copy this to your actual notifications-test component location
 */
class PostalCodeSelectFluxTest extends Component
{
    // Postal Code Selection Properties
    public $selectedPostalCode = null;
    public $userCity = '';
    public $userState = '';
    public $userPostalCode = '';
    public $userCounty = '';
    
    // Delivery Option Properties (if you're also using DeliveryOptionSelectFlux)
    public $deliveryOptionIDChanged = null;
    
    // Other notification properties
    public $patronBarcode = '';
    public $notificationMessage = '';
    public $isFormValid = false;

    public function mount()
    {
        // Initialize postal code from session
        $this->selectedPostalCode = session('PostalCodeID', null);
        
        // Initialize delivery option from session
        $this->deliveryOptionIDChanged = session('DeliveryOptionID', 8);
        
        // If we have a postal code selection, load the details
        if ($this->selectedPostalCode) {
            $this->loadPostalCodeDetails();
        }
    }

    /**
     * Listen for postal code updates from PostalCodeSelectFlux component
     */
    #[On('postalCodeUpdated')]
    public function handlePostalCodeUpdate($data)
    {
        // Update local properties with comprehensive postal code data
        $this->userCity = $data['city'];
        $this->userState = $data['state'];
        $this->userPostalCode = $data['postalCode'];
        $this->userCounty = $data['county'] ?? '';
        
        // Log the update for debugging
        logger('Postal code updated', $data);
        
        // Validate form after postal code selection
        $this->validateForm();
        
        // You could also trigger other actions here:
        // - Update service area calculations
        // - Load location-specific notification preferences
        // - Update delivery options based on location
        $this->updateServiceAreaSettings($data);
    }

    /**
     * Listen for delivery option updates (if using DeliveryOptionSelectFlux)
     */
    #[On('deliveryOptionUpdated')]
    public function handleDeliveryOptionUpdate($data)
    {
        logger('Delivery option updated', $data);
        
        // Update notification settings based on delivery method
        $this->updateNotificationSettings($data);
        
        // Validate form
        $this->validateForm();
    }

    /**
     * Load postal code details from database if we have a selection
     */
    private function loadPostalCodeDetails()
    {
        if ($this->selectedPostalCode) {
            $postalCodeData = PostalCode::find($this->selectedPostalCode);
            
            if ($postalCodeData) {
                $this->userCity = $postalCodeData->City;
                $this->userState = $postalCodeData->State;
                $this->userPostalCode = $postalCodeData->PostalCode;
                $this->userCounty = $postalCodeData->County ?? '';
            }
        }
    }

    /**
     * Update service area settings based on postal code
     */
    private function updateServiceAreaSettings($postalData)
    {
        // Examples: Enable/disable certain notification types based on location
        if ($postalData['state'] === 'CO') {
            // Colorado-specific settings
            $this->enableColoradoNotifications();
        }
        
        // Update delivery zones
        $this->updateDeliveryZone($postalData);
    }

    /**
     * Update notification settings based on delivery option
     */
    private function updateNotificationSettings($deliveryData)
    {
        // Examples: Adjust notification message based on delivery method
        switch ($deliveryData['deliveryOptionId']) {
            case 1: // Mailing Address
                $this->notificationMessage = "Notification will be sent to your mailing address in {$this->userCity}, {$this->userState}";
                break;
            case 2: // Email Address
                $this->notificationMessage = "Notification will be sent to your email address";
                break;
            case 3: // Phone
                $this->notificationMessage = "Notification will be sent via phone call";
                break;
            case 8: // TXT Messaging
                $this->notificationMessage = "Notification will be sent via text message";
                break;
        }
    }

    /**
     * Validate the form based on current selections
     */
    private function validateForm()
    {
        $this->isFormValid = !empty($this->patronBarcode) && 
                           !empty($this->selectedPostalCode) && 
                           !empty($this->deliveryOptionIDChanged);
    }

    /**
     * Send notification (examples action)
     */
    public function sendNotification()
    {
        $this->validate([
            'patronBarcode' => 'required',
            'selectedPostalCode' => 'required',
            'deliveryOptionIDChanged' => 'required',
        ]);

        // Process notification with postal code and delivery option data
        $notificationData = [
            'patron_barcode' => $this->patronBarcode,
            'delivery_option_id' => $this->deliveryOptionIDChanged,
            'postal_code_id' => $this->selectedPostalCode,
            'city' => $this->userCity,
            'state' => $this->userState,
            'postal_code' => $this->userPostalCode,
            'county' => $this->userCounty,
            'message' => $this->notificationMessage,
        ];

        // Send notification logic here
        logger('Sending notification', $notificationData);
        
        session()->flash('success', 'Notification sent successfully to ' . $this->userCity . ', ' . $this->userState);
    }

    /**
     * Reset form
     */
    public function resetForm()
    {
        $this->reset([
            'selectedPostalCode',
            'userCity', 
            'userState',
            'userPostalCode',
            'userCounty',
            'patronBarcode',
            'notificationMessage',
            'deliveryOptionIDChanged'
        ]);
        
        // Clear sessions
        session()->forget(['PostalCodeID', 'DeliveryOptionID']);
    }

    /**
     * Examples Colorado-specific functionality
     */
    private function enableColoradoNotifications()
    {
        // Colorado-specific notification settings
        logger('Enabled Colorado-specific notifications');
    }

    /**
     * Update delivery zone based on postal code
     */
    private function updateDeliveryZone($postalData)
    {
        // Examples delivery zone logic
        if (in_array($postalData['state'], ['CO', 'WY', 'NE'])) {
            // Mountain region
            $deliveryZone = 'mountain';
        } else {
            $deliveryZone = 'standard';
        }
        
        logger('Updated delivery zone', ['zone' => $deliveryZone, 'postal' => $postalData]);
    }

    public function render()
    {
        return view('livewire.notifications-test');
    }
}