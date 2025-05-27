<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Osiset\ShopifyApp\Storage\Models\Plan;

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Plan::truncate();

        $plansData = [
            [
                "type" => "RECURRING",
                "name" => "All in One Yearly",
                "price" => 95.88,
                "interval" => "ANNUAL",
                "terms" => "Save 20% yearly. No extra charges are applied.",
                "test" => false,
                "trial_days" => 3,
                "on_install" => true,
            ],
            [
                "type" => "RECURRING",
                "name" => "All in One Monthly",
                "price" => 9.99,
                "interval" => "EVERY_30_DAYS",
                "terms" => "Easy monthly plan. No extra charges are applied.",
                "test" => false,
                "trial_days" => 3,
                "on_install" => false,
            ]
        ];

        foreach ($plansData as $planData) {
            $plan = new Plan();
            $plan->fill($planData);

            $plan->save();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
