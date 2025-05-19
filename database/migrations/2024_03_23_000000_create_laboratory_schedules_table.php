<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratory_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_id')->constrained('computer_laboratories')->onDelete('cascade');
            $table->foreignId('academic_term_id')->constrained()->onDelete('cascade');
            $table->string('subject_code')->nullable();
            $table->string('subject_name');
            $table->string('instructor_name');
            $table->string('section');
            $table->integer('day_of_week'); // 0 = Sunday, 1 = Monday, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', ['regular', 'special'])->default('regular');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add unique constraint to prevent schedule conflicts
            $table->unique(['laboratory_id', 'academic_term_id', 'day_of_week', 'start_time', 'end_time'], 'schedule_conflict_check');
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratory_schedules');
    }
}; 