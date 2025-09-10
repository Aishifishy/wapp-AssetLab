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
        // Add requires_image field to computer_laboratories table
        Schema::table('computer_laboratories', function (Blueprint $table) {
            $table->boolean('requires_image')->default(false)->after('status');
        });

        // Add form_image_path field to laboratory_reservations table
        Schema::table('laboratory_reservations', function (Blueprint $table) {
            $table->string('form_image_path')->nullable()->after('rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('computer_laboratories', function (Blueprint $table) {
            $table->dropColumn('requires_image');
        });

        Schema::table('laboratory_reservations', function (Blueprint $table) {
            $table->dropColumn('form_image_path');
        });
    }
};
