<?php

namespace Blashbrook\PAPIClient\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DeliveryOption Model.
 *
 * Represents delivery options for patron notifications in the Polaris ILS system.
 * These options define how patrons can receive notifications (email, mail, phone, text, etc.).
 *
 * @author Brian Lashbrook <blashbrook@gmail.com>
 *
 * @property int $id Laravel auto-increment primary key
 * @property int $DeliveryOptionID Polaris delivery option ID
 * @property string $DeliveryOption Display name of the delivery option
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereDeliveryOption(string $value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereDeliveryOptionID(int $value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereId(int $value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereCreatedAt(string $value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryOption whereUpdatedAt(string $value)
 *
 * @example Find by Polaris ID:
 *   $option = DeliveryOption::where('DeliveryOptionID', 8)->first();
 * @example Get all available options:
 *   $options = DeliveryOption::all();
 * @example Create new option:
 *   DeliveryOption::create([
 *       'DeliveryOptionID' => 5,
 *       'DeliveryOption' => 'Push Notification'
 *   ]);
 *
 * @since 1.0.0
 */
class DeliveryOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'DeliveryOptionID',
        'DeliveryOption',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'DeliveryOptionID' => 'integer',
        'DeliveryOption' => 'string',
    ];
}
