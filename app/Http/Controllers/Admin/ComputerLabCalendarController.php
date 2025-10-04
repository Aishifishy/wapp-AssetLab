<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\ComputerLaboratory;
use App\Models\LaboratorySchedule;
use App\Services\ReservationConflictService;
use Carbon\Carbon;
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

        // Get the requested week or default to current week
        $weekOffset = (int) $request->query('week', 0); // 0 = current week, -1 = previous, 1 = next, etc.
        
        // Limit week navigation to reasonable bounds (e.g., 6 months in the past and 1 year in the future)
        $weekOffset = max(-26, min(52, $weekOffset));
        
        $currentWeekStart = \Carbon\Carbon::now()->startOfWeek();
        $selectedWeekStart = $currentWeekStart->copy()->addWeeks($weekOffset);
        $selectedWeekEnd = $selectedWeekStart->copy()->endOfWeek();

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

        // Get reservations for the selected week (for all laboratories or selected one)
        $reservationsQuery = \App\Models\LaboratoryReservation::with(['user', 'laboratory'])
            ->where('status', \App\Models\LaboratoryReservation::STATUS_APPROVED)
            ->whereBetween('reservation_date', [$selectedWeekStart->toDateString(), $selectedWeekEnd->toDateString()]);

        if ($selectedLaboratory) {
            $reservationsQuery->where('laboratory_id', $selectedLaboratory->id);
        }

        $reservations = $reservationsQuery->get()->groupBy('laboratory_id');

        // Prepare week navigation data
        $weekData = [
            'current_offset' => $weekOffset,
            'selected_week_start' => $selectedWeekStart,
            'selected_week_end' => $selectedWeekEnd,
            'is_current_week' => $weekOffset === 0,
            'week_dates' => []
        ];
        
        // Generate array of dates for the selected week
        for ($i = 0; $i < 7; $i++) {
            $weekData['week_dates'][] = $selectedWeekStart->copy()->addDays($i);
        }

        // Handle AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.comlab.calendar', compact('laboratories', 'currentTerm', 'schedules', 'reservations', 'selectedLaboratory', 'weekData'))->render();
        }

        return view('admin.comlab.calendar', compact('laboratories', 'currentTerm', 'schedules', 'reservations', 'selectedLaboratory', 'weekData'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create(ComputerLaboratory $laboratory = null)
    {
        // If laboratory is provided (from route parameter), show the laboratory-specific form
        if ($laboratory) {
            $currentTerm = AcademicTerm::where('is_current', true)->first();
            
            if (!$currentTerm) {
                return redirect()->route('admin.academic.index')
                    ->with('error', 'Please set a current academic term first.');
            }

            return view('admin.comlab.schedule.create', compact('laboratory', 'currentTerm'));
        }

        // Otherwise, show the generic form with laboratory selection
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
    public function store(Request $request, ComputerLaboratory $laboratory = null)
    {
        $validated = $request->validate([
            'laboratory_id' => $laboratory ? 'nullable' : 'required|exists:computer_laboratories,id',
            'academic_term_id' => 'required|exists:academic_terms,id',
            'subject_code' => 'nullable|string|max:20',
            'subject_name' => 'required|string|max:100',
            'instructor_name' => 'required|string|max:100',
            'section' => 'required|string|max:20',
            'day_of_week' => 'required|integer|min:0|max:6',            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
            'notes' => 'nullable|string|max:500',        ]);

        // Use laboratory from route parameter if available, otherwise from form data
        $targetLaboratory = $laboratory ?: ComputerLaboratory::findOrFail($validated['laboratory_id']);
        $laboratoryId = $targetLaboratory->id;

        // For laboratory-specific routes, use current academic term if not provided
        if ($laboratory && !isset($validated['academic_term_id'])) {
            $currentTerm = AcademicTerm::where('is_current', true)->first();
            if (!$currentTerm) {
                return back()->withInput()->withErrors([
                    'academic_term_id' => 'No current academic term is set.'
                ]);
            }
            $validated['academic_term_id'] = $currentTerm->id;
        }

        // Check for schedule conflicts using centralized logic
        $conflictingSchedule = LaboratorySchedule::where('laboratory_id', $laboratoryId)
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
        
        $schedule = $targetLaboratory->schedules()->create([
            'academic_term_id' => $validated['academic_term_id'],
            'subject_code' => $validated['subject_code'],
            'subject_name' => $validated['subject_name'],
            'instructor_name' => $validated['instructor_name'],
            'section' => $validated['section'],
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'notes' => $validated['notes'],
            'type' => $validated['type'] ?? 'regular'
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