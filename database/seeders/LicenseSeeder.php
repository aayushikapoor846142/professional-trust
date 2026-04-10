<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category1 = \App\Models\CdsLicenseCategory::create([
            'name' => 'Category 1',
            'added_by' => '1',
        ]);
        $category2 = \App\Models\CdsLicenseCategory::create([
            'name' => 'Category 2',
            'added_by' => '1',
        ]);

        $regulary_country1 = \App\Models\CdsRegulatoryCountry::create([
            'name' => 'Canada',
            'added_by' => '1',
        ]);
        $regulary_country2 = \App\Models\CdsRegulatoryCountry::create([
            'name' => 'Australia',
            'added_by' => '1',
        ]);

        \App\Models\CdsRegulatoryBody::create([
            'name' => 'RCIC',
            'added_by' => '1',
            'regulatory_country_id'=>$regulary_country1->id,
            'license_prefix' => "R",
            'license_category_id' => $category1->id
        ]);

        \App\Models\CdsRegulatoryBody::create([
            'name' => 'MARA',
            'added_by' => '1',
            'regulatory_country_id'=>$regulary_country2->id,
            'license_prefix' => "MARA",
            'license_category_id' => $category2->id
        ]);
    }
}
