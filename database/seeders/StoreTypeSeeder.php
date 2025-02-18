<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreType;

class StoreTypeSeeder extends Seeder
{
    public function run(): void
    {
        $storeTypes = ['takeaway', 'shop', 'restaurant'];

        foreach ($storeTypes as $type) {
            StoreType::updateOrCreate(['name' => $type]);
        }

        // Optionally generate additional random store types
        //StoreType::factory()->count(10)->create();
    }
}
