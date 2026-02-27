<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $departments = [
            [
                'name' => 'Administration',
                'code' => 'ADMIN',
                'cost_center' => 'CC-ADMIN-001',
                'passcode' => '0000',
                'annual_budget' => 0,
                'allocated_budget' => 0,
                'spent_budget' => 0,
                'remaining_budget' => 0,
                'budget_year' => date('Y'),
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'cost_center' => 'CC-IT-001',
                'passcode' => '1234',
                'annual_budget' => 500000,
                'allocated_budget' => 500000,
                'spent_budget' => 0,
                'remaining_budget' => 500000,
                'budget_year' => date('Y'),
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'cost_center' => 'CC-HR-001',
                'passcode' => '2345',
                'annual_budget' => 300000,
                'allocated_budget' => 300000,
                'spent_budget' => 0,
                'remaining_budget' => 300000,
                'budget_year' => date('Y'),
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'cost_center' => 'CC-FIN-001',
                'passcode' => '3456',
                'annual_budget' => 400000,
                'allocated_budget' => 400000,
                'spent_budget' => 0,
                'remaining_budget' => 400000,
                'budget_year' => date('Y'),
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'cost_center' => 'CC-MKT-001',
                'passcode' => '4567',
                'annual_budget' => 600000,
                'allocated_budget' => 600000,
                'spent_budget' => 0,
                'remaining_budget' => 600000,
                'budget_year' => date('Y'),
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'cost_center' => 'CC-OPS-001',
                'passcode' => '5678',
                'annual_budget' => 450000,
                'allocated_budget' => 450000,
                'spent_budget' => 0,
                'remaining_budget' => 450000,
                'budget_year' => date('Y'),
            ],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                $dept
            );

            $this->command->info("Department created: {$dept['name']} ({$dept['code']})");
        }

        $this->command->newLine();
        $this->command->info('All departments seeded successfully!');
    }
}