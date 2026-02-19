<?php
// database/seeders/OrganizationSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    public function run()
    {
        // Clear existing records
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Organization::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create organization with JSON encoded settings
        Organization::create([
            'name' => 'Smart Track Solutions',
            'legal_name' => 'Smart Track Solutions Pvt Ltd',
            'email' => 'info@smarttrack.com',
            'phone' => '+1 234 567 8900',
            'address' => '123 Business Avenue',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'postal_code' => '10001',
            'tax_number' => 'TAX123456',
            'registration_number' => 'REG789012',
            'website' => 'https://smarttrack.com',
            'logo' => 'company-logo.png',
            'settings' => json_encode([ // Manually encode to JSON
                'timezone' => 'America/New_York',
                'currency' => 'USD',
                'date_format' => 'm/d/Y',
                'business_hours' => [
                    'monday' => '9:00-18:00',
                    'tuesday' => '9:00-18:00',
                    'wednesday' => '9:00-18:00',
                    'thursday' => '9:00-18:00',
                    'friday' => '9:00-17:00',
                    'saturday' => 'closed',
                    'sunday' => 'closed'
                ]
            ])
        ]);

        $this->command->info('Organization seeded successfully!');
    }
}