<?php

namespace Blashbrook\PAPIClient\Models;

use Illuminate\Database\Eloquent\Model;

class PendingUpdate extends Model
{
    protected $fillable = [
        'access_secret',
        'barcode',
        'field',
        'new_value',
        'token',
    ];
}
