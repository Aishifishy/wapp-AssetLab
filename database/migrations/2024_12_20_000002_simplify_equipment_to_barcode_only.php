<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip this migration since QR code and identification_type columns were never created
        // The equipment table only has barcode and rfid_tag columns
        // This migration is just a placeholder for the cleanup process
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->string('qr_code')->unique()->nullable()->after('barcode');
            $table->enum('identification_type', ['rfid', 'barcode', 'qr_code'])->default('barcode')->after('qr_code');
        });
    }
};
