<?php

namespace Blashbrook\PAPIClient\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    protected $fillable = [
        'PostalCodeID',
        'PostalCode',
        'City',
        'State',
        'CountryID',
        'County',
    ];
}
