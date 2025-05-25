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
        Schema::create('laboratory_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('rusers')->onDelete('cascade');
            $table->foreignId('laboratory_id')->constrained('computer_laboratories')->onDelete('cascade');
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('purpose');
            $table->text('rejection_reason')->nullable();
            $table->unsignedInteger('num_students')->default(1);
            $table->string('course_code')->nullable();
            $table->string('subject')->nullable();
            $table->string('section')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_pattern', ['daily', 'weekly', 'monthly'])->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_reservations');
    }
};
