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
     * This migration ensures all equipment has barcodes generated.
     */
    public function up(): void
    {
        // Get all equipment without barcodes
        $equipmentWithoutBarcodes = Equipment::whereNull('barcode')->get();
        
        foreach ($equipmentWithoutBarcodes as $equipment) {
            // Generate unique barcode
            $barcode = Equipment::generateBarcode();
            
            $equipment->update([
                'barcode' => $barcode
            ]);
            
            echo "Generated barcode '{$barcode}' for equipment '{$equipment->name}'\n";
        }
        
        echo "Migration completed. " . $equipmentWithoutBarcodes->count() . " equipment items now have barcodes.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all generated barcodes
        Equipment::whereNotNull('barcode')->update([
            'barcode' => null
        ]);
    }
};
