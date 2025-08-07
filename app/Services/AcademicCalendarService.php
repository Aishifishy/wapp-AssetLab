<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AcademicTerm;
use Carbon\Carbon;

class AcademicCalendarService
{
    /**
     * Automatically set the current academic year and term based on the given date
     * 
     * @param string|null $date
     * @return array
     */
    public function setCurrentByDate($date = null)
    {
        $date = $date ? Carbon::parse($date)->startOfDay() : now()->startOfDay();
        
        $result = [
            'academic_year' => null,
            'academic_term' => null,
            'message' => null,
            'success' => false
        ];
        
        // Find the academic year that contains this date
        $academicYear = AcademicYear::getCurrentByDate($date);
        
        if (!$academicYear) {
            $result['message'] = 'No academic year found for the date: ' . $date->format('M d, Y');
            return $result;
        }
        
        // Find the term within that academic year that contains this date
        $academicTerm = AcademicTerm::where('academic_year_id', $academicYear->id)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();
            
        if (!$academicTerm) {
            $result['message'] = 'No academic term found for the date: ' . $date->format('M d, Y') . ' within academic year: ' . $academicYear->name;
            return $result;
        }
        
        // Set the current academic year and term
        $academicTerm->markAsCurrent(); // This also marks the academic year as current
        
        $result['academic_year'] = $academicYear;
        $result['academic_term'] = $academicTerm;
        $result['message'] = 'Successfully set ' . $academicYear->name . ' - ' . $academicTerm->name . ' as current based on date: ' . $date->format('M d, Y');
        $result['success'] = true;
        
        return $result;
    }
    
    /**
     * Get academic year and term information for a specific date without setting as current
     * 
     * @param string|null $date
     * @return array
     */
    public function getAcademicInfoByDate($date = null)
    {
        $date = $date ? Carbon::parse($date)->startOfDay() : now()->startOfDay();
        
        $academicYear = AcademicYear::getCurrentByDate($date);
        $academicTerm = null;
        
        if ($academicYear) {
            $academicTerm = AcademicTerm::where('academic_year_id', $academicYear->id)
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->first();
        }
        
        return [
            'date' => $date,
            'academic_year' => $academicYear,
            'academic_term' => $academicTerm,
            'is_in_academic_period' => !is_null($academicYear) && !is_null($academicTerm)
        ];
    }
    
    /**
     * Automatically update current academic year and term based on today's date
     * This can be run daily via a scheduled command
     * 
     * @return array
     */
    public function updateCurrentStatus()
    {
        return $this->setCurrentByDate();
    }
    
    /**
     * Get all academic periods that need attention (no current year/term set)
     * 
     * @return array
     */
    public function getStatusReport()
    {
        $today = now()->startOfDay();
        $currentAcademicYear = AcademicYear::current()->first();
        $currentAcademicTerm = AcademicTerm::current()->first();
        
        $expectedAcademicYear = AcademicYear::getCurrentByDate($today);
        $expectedAcademicTerm = null;
        
        if ($expectedAcademicYear) {
            $expectedAcademicTerm = AcademicTerm::where('academic_year_id', $expectedAcademicYear->id)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first();
        }
        
        return [
            'today' => $today->format('Y-m-d'),
            'current_academic_year' => $currentAcademicYear,
            'current_academic_term' => $currentAcademicTerm,
            'expected_academic_year' => $expectedAcademicYear,
            'expected_academic_term' => $expectedAcademicTerm,
            'needs_update' => (
                ($currentAcademicYear?->id !== $expectedAcademicYear?->id) ||
                ($currentAcademicTerm?->id !== $expectedAcademicTerm?->id)
            )
        ];
    }
}
