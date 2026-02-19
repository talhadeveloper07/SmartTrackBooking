<?php
// database/seeders/EmployeeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Clear existing records
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Employee::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get all businesses
        $businesses = Business::all();

        if ($businesses->isEmpty()) {
            $this->command->error('No businesses found! Please run BusinessSeeder first.');
            return;
        }

        $employees = [
            // Salon & Spa employees (business_id = 1)
            [
                'business_index' => 0,
                'employees' => [
                    [
                        'name' => 'Sarah Johnson',
                        'email' => 'sarah.johnson@salon.com',
                        'phone' => '+1 234-567-8901',
                        'address' => '123 Main St, Apt 4B, New York, NY 10001',
                        'date_of_birth' => '1990-05-15',
                        'joining_date' => '2023-01-15',
                        'employee_id' => 'EMP001'
                    ],
                    [
                        'name' => 'Michael Chen',
                        'email' => 'michael.chen@salon.com',
                        'phone' => '+1 234-567-8902',
                        'address' => '456 Oak Ave, New York, NY 10002',
                        'date_of_birth' => '1988-08-22',
                        'joining_date' => '2023-03-10',
                        'employee_id' => 'EMP002'
                    ],
                    [
                        'name' => 'Jessica Martinez',
                        'email' => 'jessica.martinez@salon.com',
                        'phone' => '+1 234-567-8903',
                        'address' => '789 Pine St, New York, NY 10003',
                        'date_of_birth' => '1992-11-30',
                        'joining_date' => '2023-06-22',
                        'employee_id' => 'EMP003'
                    ],
                    [
                        'name' => 'David Kim',
                        'email' => 'david.kim@salon.com',
                        'phone' => '+1 234-567-8904',
                        'address' => '321 Elm St, New York, NY 10004',
                        'date_of_birth' => '1985-02-10',
                        'joining_date' => '2023-08-05',
                        'employee_id' => 'EMP004'
                    ]
                ]
            ],
            // Fitness Center employees (business_id = 2)
            [
                'business_index' => 1,
                'employees' => [
                    [
                        'name' => 'Robert Wilson',
                        'email' => 'robert.wilson@fitness.com',
                        'phone' => '+1 234-567-8905',
                        'address' => '654 Fitness Way, New York, NY 10005',
                        'date_of_birth' => '1987-07-18',
                        'joining_date' => '2023-02-01',
                        'employee_id' => 'EMP005'
                    ],
                    [
                        'name' => 'Emily Brown',
                        'email' => 'emily.brown@fitness.com',
                        'phone' => '+1 234-567-8906',
                        'address' => '987 Health Blvd, New York, NY 10006',
                        'date_of_birth' => '1991-09-25',
                        'joining_date' => '2023-04-18',
                        'employee_id' => 'EMP006'
                    ],
                    [
                        'name' => 'James Taylor',
                        'email' => 'james.taylor@fitness.com',
                        'phone' => '+1 234-567-8907',
                        'address' => '147 Wellness Ave, New York, NY 10007',
                        'date_of_birth' => '1989-12-03',
                        'joining_date' => '2023-07-12',
                        'employee_id' => 'EMP007'
                    ],
                    [
                        'name' => 'Lisa Anderson',
                        'email' => 'lisa.anderson@fitness.com',
                        'phone' => '+1 234-567-8908',
                        'address' => '258 Gym Street, New York, NY 10008',
                        'date_of_birth' => '1993-03-14',
                        'joining_date' => '2023-09-03',
                        'employee_id' => 'EMP008'
                    ]
                ]
            ]
        ];

        foreach ($employees as $data) {
            $business = $businesses[$data['business_index']];
            
            foreach ($data['employees'] as $empData) {
                // First create user account (if using separate users table)
                $user = User::create([
                    'name' => $empData['name'],
                    'email' => $empData['email'],
                    'password' => Hash::make('password'),
                    'user_type' => 'employee',
                    'status' => 'active'
                ]);

                // Then create employee record
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

                $this->command->info('Created employee: ' . $empData['name']);
            }
        }

        $this->command->info('====================================');
        $this->command->info('Employees seeded successfully!');
        $this->command->info('Total employees created: 8');
        $this->command->info('Employee passwords: password');
        $this->command->info('====================================');
    }
}