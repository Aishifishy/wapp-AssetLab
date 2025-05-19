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
        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Add foreign key to equipment table
        Schema::table('equipment', function (Blueprint $table) {
            // First remove the old category column
            $table->dropColumn('category');
            
            // Add the new foreign key
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('equipment_categories')
                  ->onDelete('set null');
                  
            // Remove the location column as it's no longer needed
            $table->dropColumn('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->string('category');
            $table->string('location')->nullable();
        });

        Schema::dropIfExists('equipment_categories');
    }
};
