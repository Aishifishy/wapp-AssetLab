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
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->unsignedBigInteger('approved_by')->nullable()->after('rejected_at');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_by');
            
            // Add foreign key constraints
            $table->foreign('approved_by')->references('id')->on('radmins')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('radmins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory_reservations', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['approved_at', 'rejected_at', 'approved_by', 'rejected_by']);
        });
    }
};
