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
            // Add missing admin tracking fields if they don't exist
            if (!Schema::hasColumn('equipment_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('equipment_requests', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('equipment_requests', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('rejected_at');
                $table->foreign('approved_by')->references('id')->on('radmins')->onDelete('set null');
            }
            if (!Schema::hasColumn('equipment_requests', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_by');
                $table->foreign('rejected_by')->references('id')->on('radmins')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_requests', function (Blueprint $table) {
            if (Schema::hasColumn('equipment_requests', 'rejected_by')) {
                $table->dropForeign(['rejected_by']);
                $table->dropColumn('rejected_by');
            }
            if (Schema::hasColumn('equipment_requests', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('equipment_requests', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            if (Schema::hasColumn('equipment_requests', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
        });
    }
};
