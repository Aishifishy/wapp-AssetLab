<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AcademicTerm;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // Create current academic year (2023-2024)
        $academicYear = AcademicYear::create([
            'name' => '2023-2024',
            'start_date' => '2023-08-01',
            'end_date' => '2024-07-31',
            'is_current' => true,
        ]);

        // Create terms for the current academic year
        $terms = [
            [
                'name' => 'First Term',
                'term_number' => 1,
                'start_date' => '2023-08-01',
                'end_date' => '2023-11-30',
                'is_current' => Carbon::now()->between(
                    Carbon::parse('2023-08-01'),
                    Carbon::parse('2023-11-30')
                ),
            ],
            [
                'name' => 'Second Term',
                'term_number' => 2,
                'start_date' => '2023-12-01',
                'end_date' => '2024-03-31',
                'is_current' => Carbon::now()->between(
                    Carbon::parse('2023-12-01'),
                    Carbon::parse('2024-03-31')
                ),
            ],
            [
                'name' => 'Third Term',
                'term_number' => 3,
                'start_date' => '2024-04-01',
                'end_date' => '2024-07-31',
                'is_current' => Carbon::now()->between(
                    Carbon::parse('2024-04-01'),
                    Carbon::parse('2024-07-31')
                ),
            ],
        ];

        foreach ($terms as $term) {
            $academicYear->terms()->create($term);
        }

        // Create next academic year (2024-2025)
        $nextAcademicYear = AcademicYear::create([
            'name' => '2024-2025',
            'start_date' => '2024-08-01',
            'end_date' => '2025-07-31',
            'is_current' => false,
        ]);

        // Create terms for the next academic year
        $nextTerms = [
            [
                'name' => 'First Term',
                'term_number' => 1,
                'start_date' => '2024-08-01',
                'end_date' => '2024-11-30',
                'is_current' => false,
            ],
            [
                'name' => 'Second Term',
                'term_number' => 2,
                'start_date' => '2024-12-01',
                'end_date' => '2025-03-31',
                'is_current' => false,
            ],
            [
                'name' => 'Third Term',
                'term_number' => 3,
                'start_date' => '2025-04-01',
                'end_date' => '2025-07-31',
                'is_current' => false,
            ],
        ];

        foreach ($nextTerms as $term) {
            $nextAcademicYear->terms()->create($term);
        }
    }
} 