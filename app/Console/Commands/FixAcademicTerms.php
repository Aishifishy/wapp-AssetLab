<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixAcademicTerms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academic:fix-terms {year?} {--force : Actually perform the fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix academic term dates to properly cover the academic year period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yearName = $this->argument('year');
        
        if ($yearName) {
            $academicYear = AcademicYear::where('name', $yearName)->first();
            if (!$academicYear) {
                $this->error("Academic year '{$yearName}' not found.");
                return;
            }
            $academicYears = collect([$academicYear]);
        } else {
            $academicYears = AcademicYear::with('terms')->get();
        }
        
        $this->info("🔍 Checking academic year terms...");
        
        foreach ($academicYears as $year) {
            $this->line("");
            $this->info("Academic Year: {$year->name} ({$year->start_date->format('M d, Y')} - {$year->end_date->format('M d, Y')})");
            
            $terms = $year->terms()->orderBy('term_number')->get();
            
            if ($terms->count() === 0) {
                $this->warn("  No terms found. Creating 3 terms...");
                $this->createTermsForYear($year);
                continue;
            }
            
            $this->line("  Found {$terms->count()} terms:");
            
            $needsFix = false;
            $totalCoverage = 0;
            
            foreach ($terms as $term) {
                $isValid = $term->start_date >= $year->start_date && $term->end_date <= $year->end_date;
                $status = $isValid ? "✅" : "❌";
                
                $this->line("    {$status} {$term->name}: {$term->start_date->format('M d, Y')} - {$term->end_date->format('M d, Y')}");
                
                if (!$isValid) {
                    $needsFix = true;
                }
            }
            
            // Check if terms cover the full academic year
            $firstTerm = $terms->first();
            $lastTerm = $terms->last();
            
            if ($firstTerm && $lastTerm) {
                $coverageStart = $firstTerm->start_date->eq($year->start_date);
                $coverageEnd = $lastTerm->end_date->eq($year->end_date);
                
                if (!$coverageStart || !$coverageEnd) {
                    $this->warn("  ⚠️  Terms don't fully cover the academic year period");
                    $needsFix = true;
                }
            }
            
            if ($needsFix) {
                $this->warn("  → Terms need fixing");
                
                if ($this->option('force')) {
                    $this->fixTermsForYear($year);
                }
            } else {
                $this->info("  ✅ Terms are properly configured");
            }
        }
        
        if (!$this->option('force')) {
            $this->line("");
            $this->warn("This is a dry run. Use --force to actually fix the terms.");
            $this->line("Command: php artisan academic:fix-terms --force");
        }
    }
    
    private function createTermsForYear(AcademicYear $year)
    {
        if (!$this->option('force')) {
            $this->line("    Would create 3 terms for {$year->name}");
            return;
        }
        
        $startDate = Carbon::parse($year->start_date);
        $endDate = Carbon::parse($year->end_date);
        
        // Get the total days between start and end (end - start)
        $totalDays = $startDate->diffInDays($endDate);
        $termLength = floor($totalDays / 3);
        
        $this->line("    Academic year duration: {$totalDays} days, term length: {$termLength} days");
        
        $termNames = ['First Term', 'Second Term', 'Third Term'];
        $currentStart = $startDate->copy();
        
        for ($i = 0; $i < 3; $i++) {
            $termEnd = $currentStart->copy()->addDays($termLength - 1);
            
            // Ensure the last term ends exactly on the academic year end date
            if ($i === 2) {
                $termEnd = $endDate->copy();
            }
            
            $year->terms()->create([
                'name' => $termNames[$i],
                'term_number' => $i + 1,
                'start_date' => $currentStart->format('Y-m-d'),
                'end_date' => $termEnd->format('Y-m-d'),
                'is_current' => false,
            ]);
            
            $this->line("    Created: {$termNames[$i]} ({$currentStart->format('M d, Y')} - {$termEnd->format('M d, Y')})");
            
            $currentStart = $termEnd->copy()->addDay();
        }
    }
    
    private function fixTermsForYear(AcademicYear $year)
    {
        $this->line("    Fixing terms for {$year->name}...");
        
        // Delete existing terms
        $year->terms()->delete();
        
        // Create new properly spaced terms
        $this->createTermsForYear($year);
    }
}
