<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaboratoryScheduleOverride;
use App\Models\LaboratorySchedule;
use App\Models\ComputerLaboratory;
use App\Models\AcademicTerm;
use App\Services\ScheduleOverrideService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ScheduleOverrideController extends Controller
{
    protected $overrideService;

    public function __construct(ScheduleOverrideService $overrideService)
    {
        $this->overrideService = $overrideService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LaboratoryScheduleOverride::with(['laboratory', 'originalSchedule', 'createdBy']);

        // Filter by laboratory
        if ($request->filled('laboratory_id')) {
            $query->where('laboratory_id', $request->laboratory_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('override_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('override_date', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $overrides = $query->orderBy('override_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);

        $laboratories = ComputerLaboratory::orderBy('building')
                                         ->orderBy('room_number')
                                         ->get();

        return view('admin.schedule.overrides.index', compact('overrides', 'laboratories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $laboratories = ComputerLaboratory::orderBy('building')
                                         ->orderBy('room_number')
                                         ->get();

        $currentTerm = AcademicTerm::where('is_current', true)->first();

        // If coming from calendar with specific date and lab
        $selectedDate = $request->get('date');
        $selectedLaboratory = $request->get('laboratory_id');
        $selectedSchedule = $request->get('schedule_id');

        $schedule = null;
        if ($selectedSchedule) {
            $schedule = LaboratorySchedule::find($selectedSchedule);
        }

        return view('admin.schedule.overrides.create', compact(
            'laboratories', 
            'currentTerm', 
            'selectedDate', 
            'selectedLaboratory', 
            'schedule'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'laboratory_id' => 'required|exists:computer_laboratories,id',
            'laboratory_schedule_id' => 'nullable|exists:laboratory_schedules,id',
            'override_date' => 'required|date|after_or_equal:today',
            'override_type' => 'required|in:cancel,reschedule,replace',
            'new_start_time' => 'required_unless:override_type,cancel|date_format:H:i',
            'new_end_time' => 'required_unless:override_type,cancel|date_format:H:i|after:new_start_time',
            'new_subject_code' => 'nullable|string|max:20',
            'new_subject_name' => 'required_if:override_type,replace|nullable|string|max:100',
            'new_instructor_name' => 'required_if:override_type,replace|nullable|string|max:100',
            'new_section' => 'required_if:override_type,replace|nullable|string|max:20',
            'new_notes' => 'nullable|string',
            'reason' => 'required|string|max:500',
            'expires_at' => 'nullable|date|after:override_date',
        ]);

        // Get current term
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        if (!$currentTerm) {
            throw ValidationException::withMessages([
                'override_date' => 'No current academic term is set.',
            ]);
        }

        $validated['academic_term_id'] = $currentTerm->id;
        $validated['created_by'] = auth('admin')->id();

        try {
            $override = $this->overrideService->createOverride($validated);

            return redirect()->route('admin.schedule.overrides.index')
                           ->with('success', 'Schedule override created successfully.');
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'override_date' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LaboratoryScheduleOverride $override)
    {
        $override->load(['laboratory', 'originalSchedule', 'createdBy', 'academicTerm']);
        
        return view('admin.schedule.overrides.show', compact('override'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LaboratoryScheduleOverride $override)
    {
        $laboratories = ComputerLaboratory::orderBy('building')
                                         ->orderBy('room_number')
                                         ->get();

        return view('admin.schedule.overrides.edit', compact('override', 'laboratories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LaboratoryScheduleOverride $override)
    {
        $validated = $request->validate([
            'override_type' => 'required|in:cancel,reschedule,replace',
            'new_start_time' => 'required_unless:override_type,cancel|date_format:H:i',
            'new_end_time' => 'required_unless:override_type,cancel|date_format:H:i|after:new_start_time',
            'new_subject_code' => 'nullable|string|max:20',
            'new_subject_name' => 'required_if:override_type,replace|nullable|string|max:100',
            'new_instructor_name' => 'required_if:override_type,replace|nullable|string|max:100',
            'new_section' => 'required_if:override_type,replace|nullable|string|max:20',
            'new_notes' => 'nullable|string',
            'reason' => 'required|string|max:500',
            'expires_at' => 'nullable|date|after:override_date',
            'is_active' => 'boolean',
        ]);

        $this->overrideService->updateOverride($override, $validated);

        return redirect()->route('admin.schedule.overrides.index')
                       ->with('success', 'Schedule override updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaboratoryScheduleOverride $override)
    {
        $this->overrideService->deactivateOverride($override);

        return redirect()->route('admin.schedule.overrides.index')
                       ->with('success', 'Schedule override deactivated successfully.');
    }

    /**
     * AJAX endpoint to get schedules for a specific laboratory and date.
     */
    public function getSchedulesForDate(Request $request)
    {
        $request->validate([
            'laboratory_id' => 'required|exists:computer_laboratories,id',
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek;

        $currentTerm = AcademicTerm::where('is_current', true)->first();
        if (!$currentTerm) {
            return response()->json(['schedules' => []]);
        }

        $schedules = LaboratorySchedule::where('laboratory_id', $request->laboratory_id)
            ->where('academic_term_id', $currentTerm->id)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        return response()->json([
            'schedules' => $schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'subject_name' => $schedule->subject_name,
                    'instructor_name' => $schedule->instructor_name,
                    'section' => $schedule->section,
                    'time_range' => $schedule->time_range,
                    'start_time' => $schedule->start_time->format('H:i'),
                    'end_time' => $schedule->end_time->format('H:i'),
                ];
            })
        ]);
    }
}
