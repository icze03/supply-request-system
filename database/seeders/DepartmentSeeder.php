<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'passcode' => '123456',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'passcode' => '234567',
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'passcode' => '345678',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'passcode' => '456789',
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'passcode' => '567890',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}