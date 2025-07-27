<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ruser;

class ClearRfidTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear all RFID tags from existing users (make them placeholders)
        Ruser::whereNotNull('rfid_tag')->update(['rfid_tag' => null]);
        
        // Also clear RFID tags from equipment if any exist
        \App\Models\Equipment::whereNotNull('rfid_tag')->update(['rfid_tag' => null]);
        
        echo "All RFID tags have been cleared and set as placeholders.\n";
        echo "Users can now be assigned RFID tags through the admin interface.\n";
        echo "Equipment can also be assigned RFID tags through the equipment management.\n";
    }
}
