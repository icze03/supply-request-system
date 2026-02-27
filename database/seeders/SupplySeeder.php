<?php

namespace Database\Seeders;

use App\Models\Supply;
use Illuminate\Database\Seeder;

class SupplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplies = [
            // Office Supplies
            ['name' => 'Scotch Tape', 'category' => 'Office', 'unit' => 'roll', 'description' => 'Transparent scotch tape'],
            ['name' => 'Masking Tape', 'category' => 'Office', 'unit' => 'roll', 'description' => 'Masking tape'],
            ['name' => 'Double-sided Tape', 'category' => 'Office', 'unit' => 'roll', 'description' => 'Double-sided adhesive tape'],
            ['name' => 'Rubber Band', 'category' => 'Office', 'unit' => 'pack', 'description' => 'Assorted rubber bands'],
            ['name' => 'Puncher (2-hole)', 'category' => 'Office', 'unit' => 'pcs', 'description' => '2-hole paper puncher'],
            
            // IT Supplies
           
            ['name' => 'Power Strip 6-outlet', 'category' => 'IT', 'unit' => 'pcs', 'description' => '6-outlet power strip'],
            ['name' => 'Extension Cord 3m', 'category' => 'IT', 'unit' => 'pcs', 'description' => '3m extension cord'],
            ['name' => 'VGA Cable', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'VGA cable 1.5m'],
            ['name' => 'DisplayPort Cable', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'DisplayPort cable 1.5m'],
            
            // Cleaning Supplies
            ['name' => 'Floor Cleaner', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => 'All-purpose floor cleaner'],
            ['name' => 'Glass Cleaner', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => 'Glass and window cleaner'],
            ['name' => 'Disinfectant Spray', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => 'Multipurpose disinfectant'],
            ['name' => 'Air Freshener', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => 'Room air freshener spray'],
            ['name' => 'Mop', 'category' => 'Cleaning', 'unit' => 'pcs', 'description' => 'Floor mop'],
            ['name' => 'Broom', 'category' => 'Cleaning', 'unit' => 'pcs', 'description' => 'Soft broom'],
            ['name' => 'Dustpan', 'category' => 'Cleaning', 'unit' => 'pcs', 'description' => 'Plastic dustpan'],
            ['name' => 'Cleaning Cloth', 'category' => 'Cleaning', 'unit' => 'pcs', 'description' => 'Microfiber cleaning cloth'],
            ['name' => 'Sponge', 'category' => 'Cleaning', 'unit' => 'pcs', 'description' => 'Kitchen sponge'],
        ];

        foreach ($supplies as $supply) {
            Supply::create($supply);
        }
    }
}