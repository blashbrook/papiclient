<?php

// @formatter:off
/**
 * A helper file for IDEs to provide better autocomplete for Eloquent models.
 * This file should not be included in production code.
 *
 * @author Brian Lashbrook <blashbrook@gmail.com>
 */

namespace Blashbrook\PAPIClient\Models {use Carbon\Carbon;use Eloquent;use Illuminate\Database\Eloquent\Builder;
    /**
     * DeliveryOption Model.
     *
     * @property int $id
     * @property int $DeliveryOptionID
     * @property string $DeliveryOption
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read string $display_name Custom display name for the option
     *
     * @method static Builder|DeliveryOption newModelQuery()
     * @method static Builder|DeliveryOption newQuery()
     * @method static Builder|DeliveryOption query()
     * @method static Builder|DeliveryOption whereCreatedAt($value)
     * @method static Builder|DeliveryOption whereDeliveryOption($value)
     * @method static Builder|DeliveryOption whereDeliveryOptionID($value)
     * @method static Builder|DeliveryOption whereId($value)
     * @method static Builder|DeliveryOption whereUpdatedAt($value)
     *
     * @mixin Eloquent
     */
    class DeliveryOption extends Eloquent
    {
    }

    /**
     * PatronCode Model.
     *
     * @property int $id
     * @property int $PatronCodeID
     * @property string $Description
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     *
     * @method static Builder|PatronCode newModelQuery()
     * @method static Builder|PatronCode newQuery()
     * @method static Builder|PatronCode query()
     *
     * @mixin Eloquent
     */
    class PatronCode extends Eloquent
    {
    }

    /**
     * PatronUdf Model.
     *
     * @property int $id
     * @property int $PatronUdfID
     * @property string $Label
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     *
     * @method static Builder|PatronUdf newModelQuery()
     * @method static Builder|PatronUdf newQuery()
     * @method static Builder|PatronUdf query()
     *
     * @mixin Eloquent
     */
    class PatronUdf extends Eloquent
    {
    }
}

namespace Blashbrook\PAPIClient\Livewire {use Illuminate\Database\Eloquent\Collection;use Livewire\Component;
    /**
     * DeliveryOptionSelectFlux Component.
     *
     * @property int|null $deliveryOptionIDChanged
     * @property Collection $deliveryOptions
     */
    class DeliveryOptionSelectFlux extends Component
    {
    }
}

namespace {
    /**
     * PAPIClient Facade Helper.
     */
    class PAPIClient extends \Blashbrook\PAPIClient\Facades\PAPIClient
    {
    }
}
