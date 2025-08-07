<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\AcademicTerm;
use Illuminate\Console\Command;

class CleanupAcademicYears extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academic:cleanup {--force : Actually perform the deletion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate academic years and fix data issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Scanning for duplicate academic years...");
        
        // Get all academic years grouped by name
        $academicYears = AcademicYear::all()->groupBy('name');
        
        $duplicates = [];
        $toDelete = [];
        
        foreach ($academicYears as $name => $years) {
            if ($years->count() > 1) {
                $this->warn("Found {$years->count()} academic years named '{$name}':");
                
                foreach ($years as $year) {
                    $termCount = $year->terms()->count();
                    $this->line("  ID {$year->id}: {$year->start_date} to {$year->end_date} ({$termCount} terms) " . ($year->is_current ? '[CURRENT]' : ''));
                }
                
                // Keep the one with the most terms, or the first one if tied
                $keeper = $years->sortByDesc(function($year) {
                    return $year->terms()->count();
                })->first();
                
                $toDelete = array_merge($toDelete, $years->reject(function($year) use ($keeper) {
                    return $year->id === $keeper->id;
                })->all());
                
                $this->info("  → Will keep ID {$keeper->id} and delete " . (count($years) - 1) . " duplicate(s)");
                $duplicates[] = $name;
            }
        }
        
        if (empty($duplicates)) {
            $this->info("✅ No duplicate academic years found!");
            return;
        }
        
        $this->line("");
        $this->info("Summary:");
        $this->line("Found duplicates for: " . implode(', ', $duplicates));
        $this->line("Will delete " . count($toDelete) . " duplicate academic years");
        
        if (!$this->option('force')) {
            $this->line("");
            $this->warn("This is a dry run. Use --force to actually perform the deletion.");
            $this->line("Command: php artisan academic:cleanup --force");
            return;
        }
        
        $this->line("");
        if ($this->confirm("Are you sure you want to delete " . count($toDelete) . " duplicate academic years?")) {
            foreach ($toDelete as $year) {
                $termCount = $year->terms()->count();
                $this->line("Deleting academic year '{$year->name}' (ID: {$year->id}) with {$termCount} terms...");
                
                // Delete associated terms first
                $year->terms()->delete();
                
                // Delete the academic year
                $year->delete();
            }
            
            $this->info("✅ Cleanup completed! Deleted " . count($toDelete) . " duplicate academic years.");
        } else {
            $this->info("Cleanup cancelled.");
        }
    }
}
