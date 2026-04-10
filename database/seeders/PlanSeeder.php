<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic', 
                'slug' => 'basic', 
                'stripe_plan' => 'prod_RKu9lAUc2wukXj', 
                'price' => 99, 
                'description' => 'Basic'

            ]

        ];
        foreach ($plans as $plan) {

            Plan::create($plan);

        }
    }
}
