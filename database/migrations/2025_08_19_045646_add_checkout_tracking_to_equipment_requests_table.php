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
        Schema::table('equipment_requests', function (Blueprint $table) {
            $table->timestamp('checked_out_at')->nullable()->after('returned_at');
            $table->unsignedBigInteger('checked_out_by')->nullable()->after('checked_out_at');
            
            // Add foreign key for who checked out the equipment
            $table->foreign('checked_out_by')->references('id')->on('radmins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_requests', function (Blueprint $table) {
            $table->dropForeign(['checked_out_by']);
            $table->dropColumn(['checked_out_at', 'checked_out_by']);
        });
    }
};
