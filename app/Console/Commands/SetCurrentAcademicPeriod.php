<?php

namespace App\Console\Commands;

use App\Services\AcademicCalendarService;
use Illuminate\Console\Command;

class SetCurrentAcademicPeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academic:set-current {date?} {--report : Show status report only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically set the current academic year and term based on the specified date (or today\'s date)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $academicService = new AcademicCalendarService();
        
        // If report flag is set, show status report only
        if ($this->option('report')) {
            return $this->showStatusReport($academicService);
        }
        
        $date = $this->argument('date');
        
        if ($date) {
            $this->info("Setting current academic period based on date: {$date}");
        } else {
            $this->info("Setting current academic period based on today's date");
        }
        
        $result = $academicService->setCurrentByDate($date);
        
        if ($result['success']) {
            $this->info("✅ " . $result['message']);
            
            $this->table(
                ['Academic Year', 'Academic Term', 'Term Period'],
                [[
                    $result['academic_year']->name,
                    $result['academic_term']->name,
                    $result['academic_term']->start_date->format('M d, Y') . ' - ' . $result['academic_term']->end_date->format('M d, Y')
                ]]
            );
        } else {
            $this->error("❌ " . $result['message']);
            
            $this->line("");
            $this->line("Available Academic Years:");
            
            $academicYears = \App\Models\AcademicYear::with('terms')->orderBy('start_date')->get();
            
            if ($academicYears->count() > 0) {
                $rows = [];
                foreach ($academicYears as $year) {
                    $rows[] = [
                        $year->name,
                        $year->start_date->format('M d, Y') . ' - ' . $year->end_date->format('M d, Y'),
                        $year->terms->count() . ' terms'
                    ];
                }
                
                $this->table(['Academic Year', 'Period', 'Terms'], $rows);
            } else {
                $this->warn("No academic years found in the database.");
                $this->line("Run: php artisan db:seed --class=AcademicYearSeeder");
            }
        }
    }
    
    /**
     * Show academic calendar status report
     */
    private function showStatusReport(AcademicCalendarService $service)
    {
        $report = $service->getStatusReport();
        
        $this->info("📋 Academic Calendar Status Report");
        $this->line("Date: " . $report['today']);
        $this->line("");
        
        // Current Status
        $this->info("Current Status:");
        if ($report['current_academic_year']) {
            $this->line("Academic Year: " . $report['current_academic_year']->name);
        } else {
            $this->error("No current academic year set");
        }
        
        if ($report['current_academic_term']) {
            $this->line("Academic Term: " . $report['current_academic_term']->name);
        } else {
            $this->error("No current academic term set");
        }
        
        $this->line("");
        
        // Expected Status
        $this->info("Expected Status (based on today's date):");
        if ($report['expected_academic_year']) {
            $this->line("Academic Year: " . $report['expected_academic_year']->name);
        } else {
            $this->warn("No academic year found for today's date");
        }
        
        if ($report['expected_academic_term']) {
            $this->line("Academic Term: " . $report['expected_academic_term']->name);
            $this->line("Period: " . $report['expected_academic_term']->start_date->format('M d, Y') . ' - ' . $report['expected_academic_term']->end_date->format('M d, Y'));
        } else {
            $this->warn("No academic term found for today's date");
        }
        
        $this->line("");
        
        // Recommendation
        if ($report['needs_update']) {
            $this->warn("⚠️  Academic calendar needs update!");
            $this->line("Run: php artisan academic:set-current");
        } else {
            $this->info("✅ Academic calendar is up to date!");
        }
    }
}
