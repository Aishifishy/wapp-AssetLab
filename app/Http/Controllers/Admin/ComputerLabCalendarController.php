<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\ComputerLaboratory;
use App\Models\LaboratorySchedule;
use App\Services\ReservationConflictService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ComputerLabCalendarController extends Controller
{    /**
     * Display the calendar view.
     */
    public function index(Request $request)
    {
        $laboratories = ComputerLaboratory::orderBy('building')
            ->orderBy('room_number')
            ->get();

        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        if (!$currentTerm) {
            return redirect()->route('admin.academic.index')
                ->with('error', 'Please set a current academic term first.');
        }

        $selectedLaboratoryId = $request->get('laboratory_id');
        $selectedLaboratory = null;

        // Build schedules query
        $schedulesQuery = LaboratorySchedule::with(['laboratory', 'academicTerm'])
            ->where('academic_term_id', $currentTerm->id);

        // Filter by selected laboratory if provided
        if ($selectedLaboratoryId) {
            $selectedLaboratory = ComputerLaboratory::find($selectedLaboratoryId);
            if ($selectedLaboratory) {
                $schedulesQuery->where('laboratory_id', $selectedLaboratoryId);
            }
        }

        $schedules = $schedulesQuery->get()->groupBy('laboratory_id');

        return view('admin.comlab.calendar', compact('laboratories', 'currentTerm', 'schedules', 'selectedLaboratory'));
    }/**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        // Get all laboratories for the dropdown
        $laboratories = ComputerLaboratory::orderBy('building')
            ->orderBy('room_number')
            ->get();

        // Get current and future academic terms
        $academicTerms = AcademicTerm::whereHas('academicYear', function ($query) {
            $query->where('end_date', '>=', now());
        })->with('academicYear')->get();

        return view('admin.laboratory.schedules.create', compact('laboratories', 'academicTerms'));
    }    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'laboratory_id' => 'required|exists:computer_laboratories,id',
            'academic_term_id' => 'required|exists:academic_terms,id',
            'subject_code' => 'nullable|string|max:20',
            'subject_name' => 'required|string|max:100',
            'instructor_name' => 'required|string|max:100',
            'section' => 'required|string|max:20',
            'day_of_week' => 'required|integer|min:1|max:6',            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
            'notes' => 'nullable|string|max:500',        ]);

        // Check for schedule conflicts using centralized logic
        $conflictingSchedule = LaboratorySchedule::where('laboratory_id', $validated['laboratory_id'])
            ->where('academic_term_id', $validated['academic_term_id'])
            ->where('day_of_week', $validated['day_of_week']);
            
        $conflictingSchedule = ReservationConflictService::applyTimeOverlapConstraints(
            $conflictingSchedule, 
            $validated['start_time'], 
            $validated['end_time']
        )->first();

        if ($conflictingSchedule) {
            return back()->withInput()->withErrors([
                'start_time' => 'The selected time slot conflicts with an existing schedule.'
            ]);
        }

        $laboratory = ComputerLaboratory::findOrFail($validated['laboratory_id']);
        
        $schedule = $laboratory->schedules()->create([
            ...$validated,
            'type' => 'regular'
        ]);

        return redirect()->route('admin.comlab.calendar')
            ->with('success', 'Class schedule created successfully.');
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit(ComputerLaboratory $laboratory, LaboratorySchedule $schedule)
    {
        $currentTerm = AcademicTerm::where('is_current', true)->firstOrFail();
        return view('admin.comlab.schedule.edit', compact('laboratory', 'schedule', 'currentTerm'));
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, ComputerLaboratory $laboratory, LaboratorySchedule $schedule)
    {
        $validated = $request->validate([
            'subject_code' => 'nullable|string|max:20',
            'subject_name' => 'required|string|max:100',
            'instructor_name' => 'required|string|max:100',
            'section' => 'required|string|max:20',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:regular,special',
            'notes' => 'nullable|string',
        ]);        // Check for schedule conflicts using centralized logic
        $conflictingScheduleQuery = LaboratorySchedule::where('laboratory_id', $laboratory->id)
            ->where('academic_term_id', $schedule->academic_term_id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where('id', '!=', $schedule->id);
            
        $conflictingSchedule = ReservationConflictService::applyTimeOverlapConstraints(
            $conflictingScheduleQuery, 
            $validated['start_time'], 
            $validated['end_time']
        )->first();

        if ($conflictingSchedule) {
            throw ValidationException::withMessages([
                'time_conflict' => 'The selected time slot conflicts with an existing schedule.',
            ]);
        }

        $schedule->update($validated);

        return redirect()->route('admin.comlab.calendar')
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy(ComputerLaboratory $laboratory, LaboratorySchedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.comlab.calendar')
            ->with('success', 'Schedule deleted successfully.');
    }
} 