<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Business;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Clear existing records
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Employee::truncate();
        Customer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Organization Admin
        $orgAdmin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@smarttrack.com',
            'password' => Hash::make('password'),
            'user_type' => 'org_admin',
            'status' => 'active',
            'email_verified_at' => now()
        ]);

        $this->command->info('Organization Admin created: admin@smarttrack.com / password');

        // 2. Get all businesses
        $businesses = Business::all();

        if ($businesses->isEmpty()) {
            $this->command->warn('No businesses found. Please run BusinessSeeder first.');
            return;
        }

        // 3. Create Employees (without business admins)
        $employeeData = [
            // Salon & Spa employees
            [
                'business_index' => 0,
                'employees' => [
                    [
                        'name' => 'Sarah Johnson',
                        'email' => 'sarah@salon.com',
                        'phone' => '+1 234-567-8901',
                        'address' => '123 Main St, New York, NY 10001',
                        'date_of_birth' => '1990-05-15',
                        'joining_date' => '2023-01-15',
                        'employee_id' => 'EMP001'
                    ],
                    [
                        'name' => 'Michael Chen',
                        'email' => 'michael@salon.com',
                        'phone' => '+1 234-567-8902',
                        'address' => '456 Oak Ave, New York, NY 10002',
                        'date_of_birth' => '1988-08-22',
                        'joining_date' => '2023-03-10',
                        'employee_id' => 'EMP002'
                    ]
                ]
            ],
            // Fitness Center employees
            [
                'business_index' => 1,
                'employees' => [
                    [
                        'name' => 'Robert Wilson',
                        'email' => 'robert@fitness.com',
                        'phone' => '+1 234-567-8903',
                        'address' => '654 Fitness Way, New York, NY 10005',
                        'date_of_birth' => '1987-07-18',
                        'joining_date' => '2023-02-01',
                        'employee_id' => 'EMP003'
                    ],
                    [
                        'name' => 'Emily Brown',
                        'email' => 'emily@fitness.com',
                        'phone' => '+1 234-567-8904',
                        'address' => '987 Health Blvd, New York, NY 10006',
                        'date_of_birth' => '1991-09-25',
                        'joining_date' => '2023-04-18',
                        'employee_id' => 'EMP004'
                    ]
                ]
            ]
        ];

        foreach ($employeeData as $data) {
            if (!isset($businesses[$data['business_index']])) {
                continue;
            }
            
            $business = $businesses[$data['business_index']];
            
            foreach ($data['employees'] as $empData) {
                // Create user
                $user = User::create([
                    'name' => $empData['name'],
                    'email' => $empData['email'],
                    'password' => Hash::make('password'),
                    'user_type' => 'employee',
                    'status' => 'active',
                    'email_verified_at' => now()
                ]);

                // Create employee record
                Employee::create([
                    'business_id' => $business->id,
                    'user_id' => $user->id,
                    'employee_id' => $empData['employee_id'],
                    'name' => $empData['name'],
                    'email' => $empData['email'],
                    'phone' => $empData['phone'],
                    'address' => $empData['address'],
                    'date_of_birth' => $empData['date_of_birth'],
                    'joining_date' => $empData['joining_date'],
                    'status' => 'active'
                ]);

                $this->command->info('Employee created: ' . $empData['name']);
            }
        }

        // 4. Create Customers
        $customerData = [
            // Salon customers
            [
                'business_index' => 0,
                'customers' => [
                    [
                        'name' => 'Alice Johnson',
                        'email' => 'alice@email.com',
                        'phone' => '+1 234-567-8910',
                        'address' => '789 Pine St, New York, NY 10003',
                        'customer_id' => 'CUST001'
                    ],
                    [
                        'name' => 'Maria Garcia',
                        'email' => 'maria@email.com',
                        'phone' => '+1 234-567-8911',
                        'address' => '321 Elm St, New York, NY 10004',
                        'customer_id' => 'CUST002'
                    ]
                ]
            ],
            // Fitness customers
            [
                'business_index' => 1,
                'customers' => [
                    [
                        'name' => 'Bob Wilson',
                        'email' => 'bob@email.com',
                        'phone' => '+1 234-567-8912',
                        'address' => '147 Wellness Ave, New York, NY 10007',
                        'customer_id' => 'CUST003'
                    ],
                    [
                        'name' => 'Carol Martinez',
                        'email' => 'carol@email.com',
                        'phone' => '+1 234-567-8913',
                        'address' => '258 Gym Street, New York, NY 10008',
                        'customer_id' => 'CUST004'
                    ]
                ]
            ]
        ];

        foreach ($customerData as $data) {
            if (!isset($businesses[$data['business_index']])) {
                continue;
            }
            
            $business = $businesses[$data['business_index']];
            
            foreach ($data['customers'] as $custData) {
                // Create user
                $user = User::create([
                    'name' => $custData['name'],
                    'email' => $custData['email'],
                    'password' => Hash::make('password'),
                    'user_type' => 'customer',
                    'status' => 'active',
                    'email_verified_at' => now()
                ]);

                // Create customer record
                Customer::create([
                    'business_id' => $business->id,
                    'user_id' => $user->id,
                    'customer_id' => $custData['customer_id'],
                    'name' => $custData['name'],
                    'email' => $custData['email'],
                    'phone' => $custData['phone'],
                    'address' => $custData['address'],
                    'status' => 'active'
                ]);

                $this->command->info('Customer created: ' . $custData['name']);
            }
        }

        $this->command->info('====================================');
        $this->command->info('All users seeded successfully!');
        $this->command->info('Total users created: ' . User::count());
        $this->command->info('Organization Admin: admin@smarttrack.com / password');
        $this->command->info('Employee password: password');
        $this->command->info('Customer password: password');
        $this->command->info('====================================');
    }
}