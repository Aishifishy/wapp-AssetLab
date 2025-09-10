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
            $table->renameColumn('form_image', 'form_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory_reservations', function (Blueprint $table) {
            $table->renameColumn('form_image_path', 'form_image');
        });
    }
};
