<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Equipment;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration converts existing RFID-tagged equipment to use barcodes
     * and sets the identification_type field appropriately.
     */
    public function up(): void
    {
        // Get all equipment with RFID tags
        $equipmentWithRfid = Equipment::whereNotNull('rfid_tag')->get();
        
        foreach ($equipmentWithRfid as $equipment) {
            // Generate barcode for equipment with RFID
            $barcode = Equipment::generateBarcode();
            
            $equipment->update([
                'barcode' => $barcode,
                'identification_type' => Equipment::ID_TYPE_BARCODE
            ]);
            
            echo "Migrated equipment '{$equipment->name}' from RFID '{$equipment->rfid_tag}' to barcode '{$barcode}'\n";
        }
        
        // Set default identification type for equipment without any identification
        Equipment::whereNull('rfid_tag')
            ->whereNull('barcode')
            ->whereNull('qr_code')
            ->update([
                'identification_type' => Equipment::ID_TYPE_BARCODE
            ]);
        
        echo "Migration completed. " . $equipmentWithRfid->count() . " equipment items migrated to barcode system.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the migration by clearing barcode and qr_code fields
        // and resetting identification_type
        Equipment::whereNotNull('barcode')->update([
            'barcode' => null,
            'qr_code' => null,
            'identification_type' => Equipment::ID_TYPE_RFID
        ]);
    }
};
