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
            $table->dropColumn(['form_image_path', 'form_image_thumbnail_path']);
        });
        
        Schema::table('computer_laboratories', function (Blueprint $table) {
            $table->dropColumn('requires_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory_reservations', function (Blueprint $table) {
            $table->string('form_image_path')->nullable();
            $table->string('form_image_thumbnail_path')->nullable();
        });
        
        Schema::table('computer_laboratories', function (Blueprint $table) {
            $table->boolean('requires_image')->default(false);
        });
    }
};
