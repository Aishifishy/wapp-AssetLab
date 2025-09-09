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
            // Add indexes for frequently queried columns
            $table->index('override_date', 'idx_override_date');
            $table->index('laboratory_id', 'idx_laboratory_id');
            $table->index('is_active', 'idx_is_active');
            $table->index('expires_at', 'idx_expires_at');
            $table->index('created_by', 'idx_created_by');
            $table->index('requested_by', 'idx_requested_by');
            
            // Composite indexes for common query patterns
            $table->index(['laboratory_id', 'override_date'], 'idx_lab_date');
            $table->index(['is_active', 'expires_at'], 'idx_active_expires');
            $table->index(['laboratory_id', 'is_active'], 'idx_lab_active');
            $table->index(['override_date', 'is_active'], 'idx_date_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory_schedule_overrides', function (Blueprint $table) {
            // Drop the indexes
            $table->dropIndex('idx_override_date');
            $table->dropIndex('idx_laboratory_id');
            $table->dropIndex('idx_is_active');
            $table->dropIndex('idx_expires_at');
            $table->dropIndex('idx_created_by');
            $table->dropIndex('idx_requested_by');
            $table->dropIndex('idx_lab_date');
            $table->dropIndex('idx_active_expires');
            $table->dropIndex('idx_lab_active');
            $table->dropIndex('idx_date_active');
        });
    }
};
