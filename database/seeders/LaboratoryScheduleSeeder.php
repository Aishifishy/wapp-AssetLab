<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\ComputerLaboratory;
use App\Models\LaboratorySchedule;
use Illuminate\Database\Seeder;

class LaboratoryScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        if (!$currentTerm) {
            return;
        }

        $laboratories = ComputerLaboratory::all();
        
        // Sample schedules for each laboratory
        $schedules = [
            [
                'subject_code' => 'CS101',
                'subject_name' => 'Introduction to Programming',
                'instructor_name' => 'John Smith',
                'section' => 'BSCS 1A',
                'day_of_week' => LaboratorySchedule::MONDAY,
                'start_time' => '08:00',
                'end_time' => '10:00',
                'type' => LaboratorySchedule::TYPE_REGULAR,
            ],
            [
                'subject_code' => 'CS102',
                'subject_name' => 'Data Structures',
                'instructor_name' => 'Jane Doe',
                'section' => 'BSCS 2A',
                'day_of_week' => LaboratorySchedule::WEDNESDAY,
                'start_time' => '13:00',
                'end_time' => '15:00',
                'type' => LaboratorySchedule::TYPE_REGULAR,
            ],
            [
                'subject_code' => 'IT201',
                'subject_name' => 'Web Development',
                'instructor_name' => 'Mike Johnson',
                'section' => 'BSIT 2B',
                'day_of_week' => LaboratorySchedule::FRIDAY,
                'start_time' => '15:00',
                'end_time' => '17:00',
                'type' => LaboratorySchedule::TYPE_REGULAR,
            ],
        ];

        foreach ($laboratories as $index => $laboratory) {
            foreach ($schedules as $schedule) {
                // Adjust times for each lab to avoid conflicts
                $startTime = date('H:i', strtotime($schedule['start_time']) + ($index * 30 * 60));
                $endTime = date('H:i', strtotime($schedule['end_time']) + ($index * 30 * 60));

                LaboratorySchedule::create([
                    'laboratory_id' => $laboratory->id,
                    'academic_term_id' => $currentTerm->id,
                    'subject_code' => $schedule['subject_code'],
                    'subject_name' => $schedule['subject_name'],
                    'instructor_name' => $schedule['instructor_name'],
                    'section' => $schedule['section'],
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'type' => $schedule['type'],
                    'notes' => 'Sample schedule for ' . $laboratory->name,
                ]);
            }
        }
    }
} 