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
        Schema::create('laboratory_schedule_overrides', function (Blueprint $table) {
            $table->id();
            
            // References to existing tables
            $table->foreignId('laboratory_schedule_id')->nullable()->constrained('laboratory_schedules')->onDelete('cascade');
            $table->foreignId('laboratory_id')->constrained('computer_laboratories')->onDelete('cascade');
            $table->foreignId('academic_term_id')->constrained('academic_terms')->onDelete('cascade');
            
            // Override specific data
            $table->date('override_date'); // Specific date for this override
            $table->enum('override_type', ['cancel', 'reschedule', 'replace']);
            
            // New schedule details (for reschedule/replace)
            $table->time('new_start_time')->nullable();
            $table->time('new_end_time')->nullable();
            $table->string('new_subject_code', 20)->nullable();
            $table->string('new_subject_name', 100)->nullable();
            $table->string('new_instructor_name', 100)->nullable();
            $table->string('new_section', 20)->nullable();
            $table->text('new_notes')->nullable();
            
            // Override metadata
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->constrained('radmins')->onDelete('cascade');
            $table->datetime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['laboratory_id', 'override_date']);
            $table->index(['laboratory_schedule_id', 'override_date']);
            $table->index(['is_active', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_schedule_overrides');
    }
};
