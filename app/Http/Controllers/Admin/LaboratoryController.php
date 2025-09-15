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
use App\Models\Ruser;
use App\Services\ScheduleOverrideService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\ScheduleOverrideNotification;

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

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50, 100]) ? $perPage : 10;

        $laboratories = ComputerLaboratory::orderBy('building')
            ->orderBy('room_number')
            ->paginate($perPage);

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
    public function reservations(Request $request)
    {
        // Get per_page parameter with validation
        $perPage = $request->get('per_page', 10);
        $allowedPerPage = [5, 10, 15, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Combine pending and recent requests into a single paginated collection
        $reservations = LaboratoryReservation::with(['user', 'laboratory', 'approvedBy', 'rejectedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Get recent schedule overrides
        $recentOverrides = LaboratoryScheduleOverride::with(['laboratory', 'originalSchedule', 'createdBy', 'requestedBy'])
            ->where('override_date', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $pendingCount = LaboratoryReservation::pending()->count();
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
            'reservations',
            'recentOverrides',
            'pendingCount',
            'approvedTodayCount',
            'rejectedTodayCount',
            'activeOverridesCount',
            'perPage'
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
    /**
     * Display schedule overrides with search functionality
     */
    public function scheduleOverrides(Request $request)
    {
        // Get per_page parameter with validation
        $perPage = $request->get('per_page', 10);
        $allowedPerPage = [5, 10, 15, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Get sorting parameters (keeping server-side sorting for initial load)
        $sortBy = $request->get('sort', 'override_date');
        $sortDirection = $request->get('direction', 'desc');

        // Validate sort parameters
        $allowedSorts = ['id', 'override_date', 'override_type', 'created_at', 'laboratory_name'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'override_date';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Build query with proper eager loading
        $query = LaboratoryScheduleOverride::with([
                'laboratory:id,name,building,room_number',
                'originalSchedule:id,subject_name,instructor_name,start_time,end_time,section',
                'createdBy:id,name',
                'requestedBy:id,name,email',
                'academicTerm:id,name'
            ])
            ->select([
                'id', 'laboratory_id', 'laboratory_schedule_id', 'academic_term_id',
                'override_date', 'override_type', 'new_start_time', 'new_end_time',
                'new_subject_name', 'new_instructor_name', 'reason', 'created_by',
                'requested_by', 'expires_at', 'is_active', 'created_at'
            ]);

        // Apply sorting
        if ($sortBy === 'laboratory_name') {
            $query->join('computer_laboratories', 'laboratory_schedule_overrides.laboratory_id', '=', 'computer_laboratories.id')
                  ->orderBy('computer_laboratories.name', $sortDirection)
                  ->select('laboratory_schedule_overrides.*'); // Ensure we only select override columns
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Add secondary sort for consistency
        if ($sortBy !== 'created_at') {
            $query->orderBy('created_at', 'desc');
        }

        // Paginate (keeping pagination for performance)
        $overrides = $query->paginate($perPage)->withQueryString(); // Preserve sort parameters in pagination

        return view('admin.laboratory.schedule-overrides', compact(
            'overrides',
            'sortBy',
            'sortDirection',
            'perPage'
        ));
    }



    /**
     * Show form to create schedule override
     */
    public function createScheduleOverride(Request $request)
    {
        $laboratories = ComputerLaboratory::orderBy('building')
                                         ->orderBy('room_number')
                                         ->get();

        $users = Ruser::orderBy('name')->get();

        $currentTerm = AcademicTerm::where('is_current', true)->first();

        // If coming from calendar with specific date and lab
        $selectedDate = $request->get('date');
        $selectedLaboratory = $request->get('laboratory_id');
        $selectedSchedule = $request->get('schedule_id');
        $requestedBy = $request->get('requested_by');

        $schedule = null;
        if ($selectedSchedule) {
            $schedule = LaboratorySchedule::find($selectedSchedule);
        }

        return view('admin.laboratory.create-override', compact(
            'laboratories', 
            'users',
            'currentTerm', 
            'selectedDate', 
            'selectedLaboratory', 
            'schedule',
            'requestedBy'
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
            'requested_by' => 'nullable|exists:rusers,id',
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

            // Find affected reservations and notify users
            $affectedReservations = LaboratoryReservation::where('laboratory_id', $validated['laboratory_id'])
                ->whereDate('start_datetime', $validated['override_date'])
                ->where('status', 'approved')
                ->with('user')
                ->get();

            // Send notifications to affected users
            foreach ($affectedReservations as $reservation) {
                Mail::to($reservation->user->email)->send(new ScheduleOverrideNotification($override, [$reservation], $reservation->user));
            }

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

    /**
     * Get reservation details for modal view
     */
    public function getReservationDetails(LaboratoryReservation $reservation)
    {
        $reservation->load(['user', 'laboratory', 'approvedBy', 'rejectedBy']);
        
        return response()->json([
            'id' => $reservation->id,
            'user' => [
                'name' => $reservation->user->name,
                'email' => $reservation->user->email,
            ],
            'laboratory' => [
                'name' => $reservation->laboratory->name,
                'building' => $reservation->laboratory->building,
                'room_number' => $reservation->laboratory->room_number,
                'capacity' => $reservation->laboratory->capacity,
            ],
            'reservation_date' => $reservation->reservation_date ? $reservation->reservation_date->format('M d, Y') : 'N/A',
            'start_time' => $reservation->start_time ? \Carbon\Carbon::parse($reservation->start_time)->format('H:i') : 'N/A',
            'end_time' => $reservation->end_time ? \Carbon\Carbon::parse($reservation->end_time)->format('H:i') : 'N/A',
            'purpose' => $reservation->purpose,
            'num_students' => $reservation->num_students,
            'course_code' => $reservation->course_code,
            'subject' => $reservation->subject,
            'section' => $reservation->section,
            'status' => $reservation->status,
            'is_recurring' => $reservation->is_recurring,
            'recurrence_pattern' => $reservation->recurrence_pattern,
            'recurrence_end_date' => $reservation->recurrence_end_date ? $reservation->recurrence_end_date->format('M d, Y') : null,
            'rejection_reason' => $reservation->rejection_reason,
            'created_at' => $reservation->created_at->format('M d, Y H:i'),
            'approved_at' => $reservation->approved_at ? $reservation->approved_at->format('M d, Y H:i') : null,
            'rejected_at' => $reservation->rejected_at ? $reservation->rejected_at->format('M d, Y H:i') : null,
            'approved_by_name' => $reservation->approvedBy ? $reservation->approvedBy->name : null,
            'rejected_by_name' => $reservation->rejectedBy ? $reservation->rejectedBy->name : null,
            'has_form_image' => $reservation->hasFormImage(),
            'form_image_url' => $reservation->form_image_url,
        ]);
    }
}