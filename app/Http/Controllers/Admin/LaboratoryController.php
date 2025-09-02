<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use App\Http\Controllers\Traits\CrudOperations;
use App\Models\ComputerLaboratory;
use App\Models\LaboratoryReservation;
use App\Models\LaboratoryScheduleOverride;
use App\Models\LaboratorySchedule;
use App\Models\AcademicTerm;
use App\Services\ScheduleOverrideService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    use ControllerHelpers, CrudOperations;

    protected $overrideService;

    public function __construct(ScheduleOverrideService $overrideService)
    {
        $this->overrideService = $overrideService;
    }

    protected function getRoutePrefix(): string
    {
        return 'admin.laboratory';
    }

    protected function getViewPrefix(): string
    {
        return 'admin.laboratory';
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => 'required|string|unique:computer_laboratories,name',
            'room_number' => 'required|string',
            'building' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'number_of_computers' => 'required|integer|min:1',
            'status' => 'required|in:available,in_use,under_maintenance,reserved',
        ];
    }

    protected function getUpdateValidationRules($model): array
    {
        return [
            'name' => 'required|string|unique:computer_laboratories,name,' . $model->id,
            'room_number' => 'required|string',
            'building' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'number_of_computers' => 'required|integer|min:1',
            'status' => 'required|in:available,in_use,under_maintenance,reserved',
        ];
    }

    public function index()
    {
        $laboratories = ComputerLaboratory::orderBy('building')
            ->orderBy('room_number')
            ->get();

        return view($this->getViewPrefix() . '.index', compact('laboratories'));
    }

    public function create()
    {
        return view($this->getViewPrefix() . '.create');
    }

    public function store(Request $request)
    {
        return $this->handleStore($request, ComputerLaboratory::class);
    }

    public function edit(ComputerLaboratory $laboratory)
    {
        return view($this->getViewPrefix() . '.edit', compact('laboratory'));
    }

    public function update(Request $request, ComputerLaboratory $laboratory)
    {
        return $this->handleUpdate($request, $laboratory);
    }

    public function destroy(ComputerLaboratory $laboratory)
    {
        return $this->handleDestroy($laboratory);
    }

    public function updateStatus(Request $request, ComputerLaboratory $laboratory)
    {
        $validated = $this->validateRequest($request, [
            'status' => 'required|in:available,in_use,under_maintenance,reserved',
        ]);

        $laboratory->update(['status' => $validated['status']]);

        return redirect()->route('admin.laboratory.index')
            ->with('success', 'Laboratory status updated successfully.');
    }

    /**
     * Show laboratory reservation requests for admin approval
     */
    public function reservations()
    {
        $pendingRequests = LaboratoryReservation::with(['user', 'laboratory', 'approvedBy', 'rejectedBy'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        $recentRequests = LaboratoryReservation::with(['user', 'laboratory', 'approvedBy', 'rejectedBy'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // Get recent schedule overrides
        $recentOverrides = LaboratoryScheduleOverride::with(['laboratory', 'originalSchedule', 'createdBy'])
            ->where('override_date', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $pendingCount = $pendingRequests->count();
        $approvedTodayCount = LaboratoryReservation::approved()
            ->whereDate('updated_at', today())
            ->count();
        $rejectedTodayCount = LaboratoryReservation::rejected()
            ->whereDate('updated_at', today())
            ->count();

        // Count active overrides
        $activeOverridesCount = LaboratoryScheduleOverride::active()
            ->where('override_date', '>=', today())
            ->count();

        return view('admin.laboratory.reservations', compact(
            'pendingRequests', 
            'recentRequests', 
            'recentOverrides',
            'pendingCount', 
            'approvedTodayCount', 
            'rejectedTodayCount',
            'activeOverridesCount'
        ));
    }

    /**
     * Approve a laboratory reservation request
     */
    public function approveRequest(Request $request, LaboratoryReservation $reservation)
    {
        if ($reservation->status !== LaboratoryReservation::STATUS_PENDING) {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $reservation->update([
            'status' => LaboratoryReservation::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth('admin')->id()
        ]);

        return redirect()->back()->with('success', 'Reservation request approved successfully.');
    }

    /**
     * Reject a laboratory reservation request
     */
    public function rejectRequest(Request $request, LaboratoryReservation $reservation)
    {
        $validated = $this->validateRequest($request, [
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($reservation->status !== LaboratoryReservation::STATUS_PENDING) {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $reservation->update([
            'status' => LaboratoryReservation::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now(),
            'rejected_by' => auth('admin')->id()
        ]);

        return redirect()->back()->with('success', 'Reservation request rejected.');
    }

    /**
     * Show schedule overrides management
     */
    public function scheduleOverrides(Request $request)
    {
        $laboratories = ComputerLaboratory::orderBy('name')->get();
        
        $query = LaboratoryScheduleOverride::with(['laboratory', 'originalSchedule', 'createdBy', 'academicTerm'])
            ->orderBy('created_at', 'desc');

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
                $query->where('is_active', true)
                      ->where(function ($q) {
                          $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                      });
            } elseif ($request->status === 'inactive') {
                $query->where(function ($q) {
                    $q->where('is_active', false)
                      ->orWhere('expires_at', '<=', now());
                });
            }
        }

        $overrides = $query->paginate(20);

        return view('admin.laboratory.schedule-overrides', compact('overrides', 'laboratories'));
    }

    /**
     * Show form to create schedule override
     */
    public function createScheduleOverride(Request $request)
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

        return view('admin.laboratory.create-override', compact(
            'laboratories', 
            'currentTerm', 
            'selectedDate', 
            'selectedLaboratory', 
            'schedule'
        ));
    }

    /**
     * Store schedule override
     */
    public function storeScheduleOverride(Request $request)
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

            return redirect()->route('admin.laboratory.schedule-overrides')
                           ->with('success', 'Schedule override created successfully.');
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'override_date' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Deactivate schedule override
     */
    public function deactivateScheduleOverride(LaboratoryScheduleOverride $override)
    {
        $this->overrideService->deactivateOverride($override);

        return redirect()->back()
                       ->with('success', 'Schedule override deactivated successfully.');
    }

    /**
     * AJAX endpoint to get schedules for a specific laboratory and date
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