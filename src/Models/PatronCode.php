<?php

namespace Blashbrook\PAPIClient\Models;

use Illuminate\Database\Eloquent\Model;

class PatronCode extends Model
{
    protected $fillable = [
        'PatronCodeID',
        'Description',
    ];
}
