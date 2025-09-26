<?php

// @formatter:off
/**
 * A helper file for IDEs to provide better autocomplete for Eloquent models.
 * This file should not be included in production code.
 * 
 * @package Blashbrook\PAPIClient
 * @author Brian Lashbrook <blashbrook@gmail.com>
 */

namespace Blashbrook\PAPIClient\Models {

    /**
     * DeliveryOption Model
     * 
     * @property int $id
     * @property int $DeliveryOptionID
     * @property string $DeliveryOption
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read string $display_name Custom display name for the option
     * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption query()
     * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereDeliveryOption($value)
     * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereDeliveryOptionID($value)
     * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereUpdatedAt($value)
     * @mixin \Eloquent
     */
    class DeliveryOption extends \Eloquent {}
    
    /**
     * PatronCode Model
     * 
     * @property int $id
     * @property int $PatronCodeID
     * @property string $Description
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|PatronCode newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|PatronCode newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|PatronCode query()
     * @mixin \Eloquent
     */
    class PatronCode extends \Eloquent {}
    
    /**
     * PatronUdf Model
     * 
     * @property int $id
     * @property int $PatronUdfID
     * @property string $Label
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|PatronUdf newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|PatronUdf newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|PatronUdf query()
     * @mixin \Eloquent
     */
    class PatronUdf extends \Eloquent {}
}

namespace Blashbrook\PAPIClient\Livewire {
    
    /**
     * DeliveryOptionSelectFlux Component
     * 
     * @property int|null $deliveryOptionIDChanged
     * @property \Illuminate\Database\Eloquent\Collection $deliveryOptions
     */
    class DeliveryOptionSelectFlux extends \Livewire\Component {}
}

namespace {
    /**
     * PAPIClient Facade Helper
     */
    class PAPIClient extends \Blashbrook\PAPIClient\Facades\PAPIClient {}
}