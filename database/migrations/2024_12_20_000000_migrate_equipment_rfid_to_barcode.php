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
        Schema::table('equipment', function (Blueprint $table) {
            // Add new barcode/QR code fields
            $table->string('barcode')->unique()->nullable()->after('rfid_tag');
            $table->string('qr_code')->unique()->nullable()->after('barcode');
            $table->enum('identification_type', ['rfid', 'barcode', 'qr_code'])->default('barcode')->after('qr_code');
            
            // Keep rfid_tag for backward compatibility during transition
            // Will be removed in a future migration after full migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'qr_code', 'identification_type']);
        });
    }
};
