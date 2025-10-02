<?php

namespace Blashbrook\PAPIClient\Database\Seeders;

use Blashbrook\PAPIClient\Models\DeliveryOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DeliveryOptionSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(): void
    {
        DeliveryOption::truncate();

        $json = File::get(__DIR__.'/delivery_options.json');
        $delivery_options = json_decode($json);

        foreach ($delivery_options as $value) {
            DeliveryOption::query()->updateOrCreate([
                'DeliveryOptionID' => $value->DeliveryOptionID,
                'DeliveryOption' => $value->DeliveryOption,
            ]);
        }
    }
}
