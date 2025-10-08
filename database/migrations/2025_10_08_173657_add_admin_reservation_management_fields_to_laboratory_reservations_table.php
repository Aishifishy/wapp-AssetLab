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
        Schema::table('laboratory_reservations', function (Blueprint $table) {
            // Admin cancellation fields
            $table->text('cancellation_reason')->nullable()->after('rejection_reason');
            $table->timestamp('cancelled_at')->nullable()->after('rejected_at');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('rejected_by');
            
            // Admin modification tracking
            $table->timestamp('modified_at')->nullable()->after('cancelled_by');
            $table->unsignedBigInteger('modified_by')->nullable()->after('modified_at');
            $table->text('admin_notes')->nullable()->after('modified_by');
            
            // Foreign key constraints
            $table->foreign('cancelled_by')->references('id')->on('radmins')->onDelete('set null');
            $table->foreign('modified_by')->references('id')->on('radmins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory_reservations', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropForeign(['modified_by']);
            
            $table->dropColumn([
                'cancellation_reason',
                'cancelled_at',
                'cancelled_by',
                'modified_at',
                'modified_by',
                'admin_notes'
            ]);
        });
    }
};
