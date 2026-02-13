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
            ['name' => 'Bond Paper A4', 'category' => 'Office', 'unit' => 'ream', 'description' => '80gsm white bond paper'],
            ['name' => 'Bond Paper Letter', 'category' => 'Office', 'unit' => 'ream', 'description' => '80gsm white bond paper letter size'],
            ['name' => 'Ballpen (Black)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Standard black ink ballpoint pen'],
            ['name' => 'Ballpen (Blue)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Standard blue ink ballpoint pen'],
            ['name' => 'Ballpen (Red)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Standard red ink ballpoint pen'],
            ['name' => 'Permanent Marker (Black)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Black permanent marker'],
            ['name' => 'Permanent Marker (Blue)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Blue permanent marker'],
            ['name' => 'Permanent Marker (Red)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Red permanent marker'],
            ['name' => 'Highlighter (Yellow)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Yellow highlighter pen'],
            ['name' => 'Highlighter (Green)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Green highlighter pen'],
            ['name' => 'Highlighter (Pink)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Pink highlighter pen'],
            ['name' => 'Stapler', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Standard desk stapler'],
            ['name' => 'Staple Wire', 'category' => 'Office', 'unit' => 'box', 'description' => 'Standard staple wire #10'],
            ['name' => 'Staple Remover', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Metal staple remover'],
            ['name' => 'Paper Clip', 'category' => 'Office', 'unit' => 'box', 'description' => 'Metal paper clips'],
            ['name' => 'Binder Clip (Small)', 'category' => 'Office', 'unit' => 'box', 'description' => 'Small binder clips'],
            ['name' => 'Binder Clip (Medium)', 'category' => 'Office', 'unit' => 'box', 'description' => 'Medium binder clips'],
            ['name' => 'Binder Clip (Large)', 'category' => 'Office', 'unit' => 'box', 'description' => 'Large binder clips'],
            ['name' => 'Folder (Long)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Long plastic folder'],
            ['name' => 'Folder (Short)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Short plastic folder'],
            ['name' => 'Envelope (Long)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Long brown envelope'],
            ['name' => 'Envelope (Short)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Short brown envelope'],
            ['name' => 'Notebook (Spiral)', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'A4 spiral notebook'],
            ['name' => 'Sticky Notes 3x3', 'category' => 'Office', 'unit' => 'pad', 'description' => '3x3 inch sticky notes'],
            ['name' => 'Sticky Notes 2x3', 'category' => 'Office', 'unit' => 'pad', 'description' => '2x3 inch sticky notes'],
            ['name' => 'Correction Tape', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'White correction tape'],
            ['name' => 'Correction Fluid', 'category' => 'Office', 'unit' => 'bottle', 'description' => 'White correction fluid'],
            ['name' => 'Scissors', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Office scissors'],
            ['name' => 'Cutter', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Utility cutter'],
            ['name' => 'Tape Dispenser', 'category' => 'Office', 'unit' => 'pcs', 'description' => 'Desktop tape dispenser'],
            ['name' => 'Scotch Tape', 'category' => 'Office', 'unit' => 'roll', 'description' => 'Transparent scotch tape'],
            ['name' => 'Masking Tape', 'category' => 'Office', 'unit' => 'roll', 'description' => 'Masking tape'],
            ['name' => 'Double-sided Tape', 'category' => 'Office', 'unit' => 'roll', 'description' => 'Double-sided adhesive tape'],
            ['name' => 'Rubber Band', 'category' => 'Office', 'unit' => 'pack', 'description' => 'Assorted rubber bands'],
            ['name' => 'Puncher (2-hole)', 'category' => 'Office', 'unit' => 'pcs', 'description' => '2-hole paper puncher'],
            
            // IT Supplies
            ['name' => 'USB Flash Drive 16GB', 'category' => 'IT', 'unit' => 'pcs', 'description' => '16GB USB 3.0 flash drive'],
            ['name' => 'USB Flash Drive 32GB', 'category' => 'IT', 'unit' => 'pcs', 'description' => '32GB USB 3.0 flash drive'],
            ['name' => 'USB Flash Drive 64GB', 'category' => 'IT', 'unit' => 'pcs', 'description' => '64GB USB 3.0 flash drive'],
            ['name' => 'HDMI Cable 1.5m', 'category' => 'IT', 'unit' => 'pcs', 'description' => '1.5m HDMI cable'],
            ['name' => 'HDMI Cable 3m', 'category' => 'IT', 'unit' => 'pcs', 'description' => '3m HDMI cable'],
            ['name' => 'USB Cable Type-C', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'USB Type-C cable 1m'],
            ['name' => 'USB Cable Micro', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'Micro USB cable 1m'],
            ['name' => 'Wireless Mouse', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'USB wireless mouse'],
            ['name' => 'Wired Mouse', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'USB wired mouse'],
            ['name' => 'Keyboard (Wired)', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'USB wired keyboard'],
            ['name' => 'Keyboard (Wireless)', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'USB wireless keyboard'],
            ['name' => 'Webcam HD', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'HD USB webcam 1080p'],
            ['name' => 'Headset with Microphone', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'USB headset with mic'],
            ['name' => 'External Hard Drive 1TB', 'category' => 'IT', 'unit' => 'pcs', 'description' => '1TB external HDD'],
            ['name' => 'External Hard Drive 2TB', 'category' => 'IT', 'unit' => 'pcs', 'description' => '2TB external HDD'],
            ['name' => 'Network Cable Cat6 (3m)', 'category' => 'IT', 'unit' => 'pcs', 'description' => '3m Cat6 ethernet cable'],
            ['name' => 'Network Cable Cat6 (5m)', 'category' => 'IT', 'unit' => 'pcs', 'description' => '5m Cat6 ethernet cable'],
            ['name' => 'Power Strip 4-outlet', 'category' => 'IT', 'unit' => 'pcs', 'description' => '4-outlet power strip'],
            ['name' => 'Power Strip 6-outlet', 'category' => 'IT', 'unit' => 'pcs', 'description' => '6-outlet power strip'],
            ['name' => 'Extension Cord 3m', 'category' => 'IT', 'unit' => 'pcs', 'description' => '3m extension cord'],
            ['name' => 'VGA Cable', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'VGA cable 1.5m'],
            ['name' => 'DisplayPort Cable', 'category' => 'IT', 'unit' => 'pcs', 'description' => 'DisplayPort cable 1.5m'],
            
            // Cleaning Supplies
            ['name' => 'Hand Sanitizer 500ml', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => '500ml hand sanitizer'],
            ['name' => 'Hand Sanitizer 1L', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => '1L hand sanitizer'],
            ['name' => 'Alcohol 70% 500ml', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => '500ml isopropyl alcohol 70%'],
            ['name' => 'Alcohol 70% 1L', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => '1L isopropyl alcohol 70%'],
            ['name' => 'Tissue Paper (Facial)', 'category' => 'Cleaning', 'unit' => 'box', 'description' => 'Facial tissue box'],
            ['name' => 'Tissue Paper (Bathroom)', 'category' => 'Cleaning', 'unit' => 'roll', 'description' => 'Bathroom tissue roll'],
            ['name' => 'Paper Towel', 'category' => 'Cleaning', 'unit' => 'roll', 'description' => 'Kitchen paper towel'],
            ['name' => 'Trash Bags (Small)', 'category' => 'Cleaning', 'unit' => 'pack', 'description' => 'Small trash bags 20pcs'],
            ['name' => 'Trash Bags (Medium)', 'category' => 'Cleaning', 'unit' => 'pack', 'description' => 'Medium trash bags 20pcs'],
            ['name' => 'Trash Bags (Large)', 'category' => 'Cleaning', 'unit' => 'pack', 'description' => 'Large trash bags 20pcs'],
            ['name' => 'Dishwashing Liquid', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => 'Dishwashing liquid soap'],
            ['name' => 'Hand Soap', 'category' => 'Cleaning', 'unit' => 'bottle', 'description' => 'Liquid hand soap'],
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