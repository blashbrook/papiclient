<?php

namespace Blashbrook\PAPIForms\Database\Seeders;

use Blashbrook\PAPIForms\App\Models\DeliveryOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DeliveryOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
