<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create a temporary table with the new structure
        Schema::create('equipment_requests_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('rusers')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->text('purpose');
            $table->dateTime('requested_from');
            $table->dateTime('requested_until');
            $table->string('status')->default('pending');
            $table->dateTime('returned_at')->nullable();
            $table->string('return_condition')->nullable();
            $table->text('return_notes')->nullable();
            $table->timestamps();
        });

        // Copy data from the old table to the new one
        DB::statement('INSERT INTO equipment_requests_new (id, user_id, equipment_id, purpose, requested_from, requested_until, status, returned_at, created_at, updated_at) 
            SELECT id, user_id, equipment_id, purpose, requested_from, requested_until, status, returned_at, created_at, updated_at 
            FROM equipment_requests');

        // Drop the old table
        Schema::drop('equipment_requests');

        // Rename the new table to the original name
        Schema::rename('equipment_requests_new', 'equipment_requests');
    }

    public function down()
    {
        // Create a temporary table with the old structure
        Schema::create('equipment_requests_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('rusers')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->text('purpose');
            $table->dateTime('requested_from');
            $table->dateTime('requested_until');
            $table->string('status')->default('pending');
            $table->dateTime('returned_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('radmins')->nullOnDelete();
            $table->timestamps();
        });

        // Copy data back
        DB::statement('INSERT INTO equipment_requests_old (id, user_id, equipment_id, purpose, requested_from, requested_until, status, returned_at, created_at, updated_at) 
            SELECT id, user_id, equipment_id, purpose, requested_from, requested_until, status, returned_at, created_at, updated_at 
            FROM equipment_requests');

        // Drop the current table
        Schema::drop('equipment_requests');

        // Rename the old structure table to the original name
        Schema::rename('equipment_requests_old', 'equipment_requests');
    }
}; 