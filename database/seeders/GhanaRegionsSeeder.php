<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GhanaRegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            ['regional_code' => 'GAR', 'name' => 'Greater Accra Region'],
            ['regional_code' => 'AR', 'name' => 'Ashanti Region'],
            ['regional_code' => 'WR', 'name' => 'Western Region'],
            ['regional_code' => 'WNR', 'name' => 'Western North Region'],
            ['regional_code' => 'CR', 'name' => 'Central Region'],
            ['regional_code' => 'ER', 'name' => 'Eastern Region'],
            ['regional_code' => 'VR', 'name' => 'Volta Region'],
            ['regional_code' => 'OR', 'name' => 'Oti Region'],
            ['regional_code' => 'NR', 'name' => 'Northern Region'],
            ['regional_code' => 'SR', 'name' => 'Savannah Region'],
            ['regional_code' => 'UER', 'name' => 'Upper East Region'],
            ['regional_code' => 'UWR', 'name' => 'Upper West Region'],
            ['regional_code' => 'BR', 'name' => 'Bono Region'],
            ['regional_code' => 'BER', 'name' => 'Bono East Region'],
            ['regional_code' => 'AHR', 'name' => 'Ahafo Region'],
            ['regional_code' => 'NER', 'name' => 'North East Region'],
        ];

        DB::table('ghana_regions')->insert($regions);
    }
}
