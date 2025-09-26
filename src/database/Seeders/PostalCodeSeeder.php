<?php

namespace Blashbrook\PAPIForms\Database\Seeders;

use Blashbrook\PAPIForms\App\Models\PostalCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PostalCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PostalCode::truncate();

        $json = File::get(__DIR__.'/postal_codes.json');
        $postal_codes = json_decode($json);

        foreach ($postal_codes as $value) {
            PostalCode::query()->updateOrCreate([
                'PostalCode' => $value->PostalCode,
                'PostalCodeID' => $value->PostalCodeID,
                'City' => $value->City,
                'State' => $value->State,
                'CountryID' => $value->CountryID,
                'County' => $value->County,
            ]);
        }
    }
}
