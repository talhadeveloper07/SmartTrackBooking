<?php
// database/seeders/BusinessSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use Illuminate\Support\Facades\DB;

class BusinessSeeder extends Seeder
{
    public function run()
    {
        // Clear existing records
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Business::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create businesses
        $businesses = [
            [
                'name' => 'Salon & Spa',
                'slug' => 'salon-spa',
                'business_type' => 'beauty',
                'email' => 'salon@smarttrack.com',
                'phone' => '+1 234 567 8910',
                'address' => '456 Beauty Lane',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10002',
                'description' => 'Premium salon and spa services for all your beauty needs',
                'status' => 'active',
                'business_hours' => json_encode([
                    'monday' => '9:00-20:00',
                    'tuesday' => '9:00-20:00',
                    'wednesday' => '9:00-20:00',
                    'thursday' => '9:00-21:00',
                    'friday' => '9:00-21:00',
                    'saturday' => '10:00-18:00',
                    'sunday' => 'closed'
                ])
            ],
            [
                'name' => 'Fitness Center',
                'slug' => 'fitness-center',
                'business_type' => 'fitness',
                'email' => 'fitness@smarttrack.com',
                'phone' => '+1 234 567 8911',
                'address' => '789 Health Blvd',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10003',
                'description' => 'State-of-the-art fitness center with personal training',
                'status' => 'active',
                'business_hours' => json_encode([
                    'monday' => '6:00-22:00',
                    'tuesday' => '6:00-22:00',
                    'wednesday' => '6:00-22:00',
                    'thursday' => '6:00-22:00',
                    'friday' => '6:00-22:00',
                    'saturday' => '8:00-20:00',
                    'sunday' => '8:00-20:00'
                ])
            ]
        ];

        foreach ($businesses as $business) {
            Business::create($business);
        }

        $this->command->info('Businesses seeded successfully!');
    }
}