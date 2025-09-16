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
        Schema::table('laboratory_schedule_overrides', function (Blueprint $table) {
            // Add foreign key to laboratory reservations (nullable since we can still override regular schedules)
            $table->foreignId('laboratory_reservation_id')->nullable()->constrained('laboratory_reservations')->onDelete('cascade');
            
            // Add index for performance
            $table->index(['laboratory_reservation_id', 'override_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory_schedule_overrides', function (Blueprint $table) {
            $table->dropForeign(['laboratory_reservation_id']);
            $table->dropIndex(['laboratory_reservation_id', 'override_date']);
            $table->dropColumn('laboratory_reservation_id');
        });
    }
};
