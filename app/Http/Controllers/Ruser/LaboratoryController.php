<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Models\ComputerLaboratory;
use App\Models\LaboratorySchedule;
use App\Models\LaboratoryReservation;
use App\Models\AcademicTerm;
use App\Services\UserLaboratoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    protected $userLaboratoryService;

    public function __construct(UserLaboratoryService $userLaboratoryService)
    {
        $this->userLaboratoryService = $userLaboratoryService;
    }

    /**
     * Display the list of laboratories available for reservation
     */
    public function index()
    {
        $laboratories = $this->userLaboratoryService->getAvailableLaboratories();
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        // Get existing schedules for these laboratories
        $schedules = [];
        $reservations = [];
        
        if ($currentTerm) {
            $schedules = LaboratorySchedule::with(['laboratory', 'academicTerm'])
                ->where('academic_term_id', $currentTerm->id)
                ->get()
                ->groupBy('laboratory_id');
                
            // Get approved reservations for better overview
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->addWeeks(2)->endOfWeek();
            
            $reservations = LaboratoryReservation::with(['user', 'laboratory'])
                ->where('status', LaboratoryReservation::STATUS_APPROVED)
                ->whereBetween('reservation_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get()
                ->groupBy('laboratory_id');
        }

        return view('ruser.laboratory.index', compact('laboratories', 'schedules', 'reservations', 'currentTerm'));
    }

    /**
     * Show the laboratory schedule and reservation form
     */
    public function show(Request $request, ComputerLaboratory $laboratory)
    {
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        // Get the requested week or default to current week
        $weekOffset = (int) $request->query('week', 0); // 0 = current week, -1 = previous, 1 = next, etc.
        
        // Limit week navigation to reasonable bounds (e.g., 6 months in the past and 1 year in the future)
        $weekOffset = max(-26, min(52, $weekOffset));
        
        $currentWeekStart = Carbon::now()->startOfWeek();
        $selectedWeekStart = $currentWeekStart->copy()->addWeeks($weekOffset);
        $selectedWeekEnd = $selectedWeekStart->copy()->endOfWeek();
        
        $schedules = collect([]);
        $reservations = collect([]);
        
        if ($currentTerm) {
            // Get recurring class schedules
            $schedules = LaboratorySchedule::where('laboratory_id', $laboratory->id)
                ->where('academic_term_id', $currentTerm->id)
                ->get();
                
            // Get approved reservations for the selected week only
            $reservations = LaboratoryReservation::with(['user'])
                ->where('laboratory_id', $laboratory->id)
                ->where('status', LaboratoryReservation::STATUS_APPROVED)
                ->whereBetween('reservation_date', [$selectedWeekStart->toDateString(), $selectedWeekEnd->toDateString()])
                ->orderBy('reservation_date')
                ->orderBy('start_time')
                ->get();
        }

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

        return view('ruser.laboratory.show', compact('laboratory', 'schedules', 'reservations', 'currentTerm', 'weekData'));
    }

    /**
     * Make a reservation request for a laboratory
     */
    public function reserve(Request $request, ComputerLaboratory $laboratory)
    {
        // Redirect to the new reservation form
        return redirect()->route('ruser.laboratory.reservations.create', $laboratory);
    }

    /**
     * Get time slots overview for a specific laboratory and date
     */
    public function getTimeSlotsOverview(Request $request, ComputerLaboratory $laboratory)
    {
        $date = $request->get('date');
        
        if (!$date) {
            return response()->json(['error' => 'Date parameter is required'], 400);
        }

        $selectedDate = Carbon::parse($date);
        $dayOfWeek = $selectedDate->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

        // Get the current academic term
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        if (!$currentTerm) {
            return response()->json(['error' => 'No current academic term found'], 400);
        }

        // Get regular schedules for this lab and day
        $regularSchedules = LaboratorySchedule::with(['laboratory', 'academicTerm'])
            ->where('laboratory_id', $laboratory->id)
            ->where('academic_term_id', $currentTerm->id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        // Get active schedule overrides for the selected date and laboratory
        $scheduleOverrides = \App\Models\LaboratoryScheduleOverride::with(['laboratory', 'createdBy', 'requestedBy', 'originalSchedule'])
            ->where('laboratory_id', $laboratory->id)
            ->whereDate('override_date', $selectedDate->format('Y-m-d'))
            ->where('is_active', true)
            ->where(function($query) use ($selectedDate) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', $selectedDate);
            })
            ->get();

        // Get laboratory reservations for the selected date and laboratory
        $labReservations = LaboratoryReservation::with(['user'])
            ->where('laboratory_id', $laboratory->id)
            ->whereDate('reservation_date', $selectedDate->format('Y-m-d'))
            ->where('status', 'approved') // Only approved reservations
            ->get();

        // Create a map of overridden schedule IDs to avoid duplicates
        $overriddenScheduleIds = $scheduleOverrides->pluck('laboratory_schedule_id')->filter()->unique()->toArray();
        
        // Process schedules into unified format
        $schedules = collect();
        
        // Add regular schedules (only if not overridden)
        foreach ($regularSchedules as $schedule) {
            // Skip this regular schedule if it has been overridden
            if (in_array($schedule->id, $overriddenScheduleIds)) {
                continue;
            }
            
            $schedules->push([
                'id' => $schedule->id,
                'type' => 'regular',
                'schedule_type' => $schedule->type, // regular/special
                'subject_code' => $schedule->subject_code,
                'subject_name' => $schedule->subject_name,
                'instructor' => $schedule->instructor_name,
                'section' => $schedule->section,
                'start_time' => $schedule->start_time->format('H:i'),
                'end_time' => $schedule->end_time->format('H:i'),
                'time_range' => $schedule->start_time->format('H:i') . ' - ' . $schedule->end_time->format('H:i'),
                'is_override' => false,
                'is_reservation' => false,
                'notes' => $schedule->notes
            ]);
        }
        
        // Add override schedules
        foreach ($scheduleOverrides as $override) {
            if ($override->override_type === 'cancel') {
                // For cancellations, we don't add anything - cancelled slots become available
                // The original schedule is already excluded above, so the time slot will show as available
                continue;
            } else {
                // For reschedule/replace, add the new schedule details
                $schedules->push([
                    'id' => $override->id,
                    'type' => 'override',
                    'schedule_type' => $override->override_type, // reschedule/replace
                    'subject_code' => $override->new_subject_code ?? ($override->originalSchedule ? $override->originalSchedule->subject_code : 'OVERRIDE'),
                    'subject_name' => $override->new_subject_name ?? ($override->originalSchedule ? $override->originalSchedule->subject_name : $override->reason),
                    'instructor' => $override->new_instructor_name ?? ($override->originalSchedule ? $override->originalSchedule->instructor_name : $override->createdBy->name),
                    'section' => $override->new_section ?? ($override->originalSchedule ? $override->originalSchedule->section : 'Override'),
                    'start_time' => Carbon::parse($override->new_start_time)->format('H:i'),
                    'end_time' => Carbon::parse($override->new_end_time)->format('H:i'),
                    'time_range' => Carbon::parse($override->new_start_time)->format('H:i') . ' - ' . Carbon::parse($override->new_end_time)->format('H:i'),
                    'is_override' => true,
                    'is_reservation' => false,
                    'override_reason' => $override->reason,
                    'override_id' => $override->id,
                    'notes' => $override->reason
                ]);
            }
        }
        
        // Add reservations
        foreach ($labReservations as $reservation) {
            $schedules->push([
                'id' => $reservation->id,
                'type' => 'reservation',
                'schedule_type' => 'reservation',
                'subject_code' => $reservation->course_code ?? 'RESERVATION',
                'subject_name' => $reservation->subject ?? $reservation->purpose,
                'instructor' => $reservation->user->name,
                'section' => $reservation->section ?? 'Reservation',
                'start_time' => Carbon::parse($reservation->start_time)->format('H:i'),
                'end_time' => Carbon::parse($reservation->end_time)->format('H:i'),
                'time_range' => Carbon::parse($reservation->start_time)->format('H:i') . ' - ' . Carbon::parse($reservation->end_time)->format('H:i'),
                'is_override' => false,
                'is_reservation' => true,
                'reservation_id' => $reservation->id,
                'purpose' => $reservation->purpose,
                'num_students' => $reservation->num_students,
                'notes' => $reservation->purpose
            ]);
        }
        
        // Sort schedules by start time
        $schedules = $schedules->sortBy('start_time')->values();
        
        // Generate time slots from 7 AM to 9 PM
        $timeSlots = [];
        for ($hour = 7; $hour <= 21; $hour++) {
            $slotStart = sprintf('%02d:00', $hour);
            $slotEnd = sprintf('%02d:00', $hour + 1);
            
            // Find what occupies this time slot
            $occupyingItem = null;
            $slotType = 'available';
            
            // Check all schedules/reservations for overlap
            $overlappingItems = $schedules->filter(function($item) use ($slotStart, $slotEnd) {
                return ($item['start_time'] < $slotEnd && $item['end_time'] > $slotStart);
            });
            
            if ($overlappingItems->isNotEmpty()) {
                // Priority order: 1. Overrides, 2. Reservations, 3. Regular schedules
                $prioritizedItems = $overlappingItems->sortBy(function($item) {
                    if ($item['type'] === 'override') return 1; // Highest priority
                    if ($item['type'] === 'reservation') return 2;
                    return 3; // Regular schedules
                });
                
                $occupyingItem = $prioritizedItems->first();
                $slotType = $occupyingItem['type'];
            }
            
            $timeSlots[] = [
                'time' => $slotStart . ' - ' . $slotEnd,
                'hour' => $hour,
                'available' => !$occupyingItem,
                'type' => $slotType,
                'item' => $occupyingItem
            ];
        }

        return response()->json([
            'date' => $selectedDate->format('Y-m-d'),
            'day_name' => $selectedDate->format('l'),
            'laboratory' => [
                'id' => $laboratory->id,
                'name' => $laboratory->name,
                'building' => $laboratory->building,
                'room' => $laboratory->room_number,
                'capacity' => $laboratory->capacity,
                'computers' => $laboratory->number_of_computers,
                'status' => $laboratory->status
            ],
            'schedules' => $schedules->toArray(),
            'time_slots' => $timeSlots,
            'has_schedules' => $schedules->count() > 0
        ]);
    }
}
