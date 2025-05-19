<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('computer_laboratories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('room_number');
            $table->string('building');
            $table->integer('capacity');
            $table->integer('number_of_computers');
            $table->text('equipment_inventory')->nullable(); // JSON array of equipment
            $table->enum('status', ['available', 'in_use', 'under_maintenance', 'reserved'])->default('available');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('computer_laboratories');
    }
}; 