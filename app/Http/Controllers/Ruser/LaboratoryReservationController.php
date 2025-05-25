<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Models\ComputerLaboratory;
use App\Models\LaboratoryReservation;
use App\Models\LaboratorySchedule;
use App\Models\AcademicTerm;
use App\Mail\LaboratoryReservationStatusChanged;
use App\Services\ReservationConflictService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LaboratoryReservationController extends Controller
{
    protected $conflictService;

    public function __construct(ReservationConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
    }

    /**
     * Show the laboratory reservation form
     */
    public function create(ComputerLaboratory $laboratory)
    {
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        // Get regular schedules for availability checking
        $schedules = collect([]);
        if ($currentTerm) {
            $schedules = LaboratorySchedule::where('laboratory_id', $laboratory->id)
                ->where('academic_term_id', $currentTerm->id)
                ->get();
        }
        
        // Get existing reservations for the next 14 days
        $startDate = now()->startOfDay();
        $endDate = now()->addDays(14)->endOfDay();
        
        $existingReservations = LaboratoryReservation::where('laboratory_id', $laboratory->id)
            ->whereBetween('reservation_date', [$startDate, $endDate])
            ->where('status', LaboratoryReservation::STATUS_APPROVED)
            ->get();
            
        return view('ruser.laboratory.reservation.create', compact(
            'laboratory', 
            'schedules', 
            'existingReservations', 
            'currentTerm'
        ));
    }
    
    /**
     * Store a new laboratory reservation
     */
    public function store(Request $request, ComputerLaboratory $laboratory)
    {
        $validatedData = $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'purpose' => 'required|string|max:1000',
            'num_students' => 'required|integer|min:1|max:' . $laboratory->capacity,
            'course_code' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:100',
            'section' => 'nullable|string|max:50',
            'is_recurring' => 'sometimes|boolean',
            'recurrence_pattern' => [
                'required_if:is_recurring,1',
                Rule::in(['daily', 'weekly', 'monthly']),
            ],
            'recurrence_end_date' => 'required_if:is_recurring,1|nullable|date|after:reservation_date',
        ]);
        
        // Convert time formats
        $reservationDate = Carbon::parse($validatedData['reservation_date'])->toDateString();
        $startTime = $validatedData['start_time'];
        $endTime = $validatedData['end_time'];
        
        // Check for conflicts with existing reservations
        $conflictingReservation = LaboratoryReservation::where('laboratory_id', $laboratory->id)
            ->where('reservation_date', $reservationDate)
            ->where('status', LaboratoryReservation::STATUS_APPROVED)
            ->where(function($query) use ($startTime, $endTime) {
                // Check if there's any overlap in time
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->first();
            
        if ($conflictingReservation) {
            return back()->withInput()->with('error', 'The selected time conflicts with an existing reservation.');
        }
        
        // Check for conflicts with regular class schedules
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        if ($currentTerm) {
            $dayOfWeek = Carbon::parse($reservationDate)->dayOfWeek;
            
            $conflictingSchedule = LaboratorySchedule::where('laboratory_id', $laboratory->id)
                ->where('academic_term_id', $currentTerm->id)
                ->where('day_of_week', $dayOfWeek)
                ->where(function($query) use ($startTime, $endTime) {
                    // Check if there's any overlap in time
                    $query->where(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                          ->where('end_time', '>', $startTime);
                    })->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>=', $endTime);
                    })->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '>=', $startTime)
                          ->where('end_time', '<=', $endTime);
                    });
                })
                ->first();
                
            if ($conflictingSchedule) {
                return back()->withInput()->with('error', 'The selected time conflicts with a regular class schedule.');
            }
        }
          // Check for recurring reservation conflicts if applicable
        if (!empty($validatedData['is_recurring']) && !empty($validatedData['recurrence_end_date'])) {
            $recurrenceEndDate = Carbon::parse($validatedData['recurrence_end_date'])->toDateString();
            $recurrencePattern = $validatedData['recurrence_pattern'];
            
            // Use the conflict service to check all recurring dates
            $conflictService = app(ReservationConflictService::class);
            $recurringConflicts = $conflictService->checkRecurringReservationConflicts(
                $laboratory->id,
                $reservationDate,
                $recurrenceEndDate,
                $startTime,
                $endTime,
                $recurrencePattern
            );
            
            if (!empty($recurringConflicts)) {
                $conflictDates = array_map(function($conflict) {
                    return Carbon::parse($conflict['date'])->format('M d, Y');
                }, $recurringConflicts);
                
                return back()->withInput()->with('error', 
                    'Your recurring reservation has conflicts on these dates: ' . implode(', ', $conflictDates));
            }
        }

        // Create the reservation
        $reservation = new LaboratoryReservation([
            'user_id' => Auth::id(),
            'laboratory_id' => $laboratory->id,
            'reservation_date' => $reservationDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'purpose' => $validatedData['purpose'],
            'num_students' => $validatedData['num_students'],
            'course_code' => $validatedData['course_code'] ?? null,
            'subject' => $validatedData['subject'] ?? null,
            'section' => $validatedData['section'] ?? null,
            'status' => LaboratoryReservation::STATUS_PENDING,
        ]);
        
        // Handle recurring reservations if requested
        if (!empty($validatedData['is_recurring'])) {
            $reservation->is_recurring = true;
            $reservation->recurrence_pattern = $validatedData['recurrence_pattern'];
            $reservation->recurrence_end_date = $validatedData['recurrence_end_date'];
        }
        
        $reservation->save();
        
        return redirect()->route('ruser.laboratory.reservations.index')
            ->with('success', 'Laboratory reservation request submitted successfully. It will be reviewed by the administrator.');
    }
    
    /**
     * Display the user's reservations
     */
    public function index()
    {
        $upcomingReservations = LaboratoryReservation::where('user_id', Auth::id())
            ->where('status', LaboratoryReservation::STATUS_APPROVED)
            ->where('reservation_date', '>=', now()->toDateString())
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->with('laboratory')
            ->get();
            
        $pendingReservations = LaboratoryReservation::where('user_id', Auth::id())
            ->where('status', LaboratoryReservation::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->with('laboratory')
            ->get();
            
        $pastReservations = LaboratoryReservation::where('user_id', Auth::id())
            ->where(function($query) {
                $query->where('reservation_date', '<', now()->toDateString())
                      ->orWhere('status', LaboratoryReservation::STATUS_REJECTED)
                      ->orWhere('status', LaboratoryReservation::STATUS_CANCELLED);
            })
            ->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->with('laboratory')
            ->paginate(10);
            
        return view('ruser.laboratory.reservation.index', compact(
            'upcomingReservations',
            'pendingReservations',
            'pastReservations'
        ));
    }
    
    /**
     * Show a specific reservation
     */
    public function show(LaboratoryReservation $reservation)
    {
        // Check if the reservation belongs to the user
        if ($reservation->user_id !== Auth::id()) {
            return redirect()->route('ruser.laboratory.reservations.index')
                ->with('error', 'You do not have permission to view this reservation.');
        }
        
        return view('ruser.laboratory.reservation.show', compact('reservation'));
    }
    
    /**
     * Cancel a pending reservation
     */
    public function cancel(LaboratoryReservation $reservation)
    {
        // Check if the reservation belongs to the user
        if ($reservation->user_id !== Auth::id()) {
            return redirect()->route('ruser.laboratory.reservations.index')
                ->with('error', 'You do not have permission to cancel this reservation.');
        }
        
        // Check if the reservation can be cancelled (must be pending or approved)
        if (!in_array($reservation->status, [LaboratoryReservation::STATUS_PENDING, LaboratoryReservation::STATUS_APPROVED])) {
            return redirect()->route('ruser.laboratory.reservations.index')
                ->with('error', 'This reservation cannot be cancelled.');
        }
        
        // If approved and scheduled to start within 24 hours, don't allow cancellation
        if ($reservation->status === LaboratoryReservation::STATUS_APPROVED) {
            $reservationStart = Carbon::parse($reservation->reservation_date . ' ' . $reservation->start_time);
            if (Carbon::now()->diffInHours($reservationStart) < 24) {
                return redirect()->route('ruser.laboratory.reservations.index')
                    ->with('error', 'Reservations cannot be cancelled within 24 hours of the start time.');
            }
        }
          $reservation->status = LaboratoryReservation::STATUS_CANCELLED;
        $reservation->save();
        
        // Reload relationships for email
        $reservation->load('laboratory');
        
        // Send email notification to user
        try {
            Mail::to(Auth::user()->email)->send(new LaboratoryReservationStatusChanged($reservation));
        } catch (\Exception $e) {
            Log::error('Failed to send reservation cancellation email', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id
            ]);
        }
        
        return redirect()->route('ruser.laboratory.reservations.index')
            ->with('success', 'Reservation has been cancelled.');
    }
    
    /**
     * Show calendar view of reservations
     */
    public function calendar(Request $request)
    {
        // Get filter parameters
        $selectedLab = $request->input('laboratory');
        $view = $request->input('view', 'dayGridMonth');
        
        // Get all laboratories
        $laboratories = ComputerLaboratory::orderBy('name')->get();
        
        // Get current term
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        // Initialize events array
        $events = [];
        
        // Get regular schedules
        if ($currentTerm) {
            $scheduleQuery = LaboratorySchedule::with('laboratory')
                ->where('academic_term_id', $currentTerm->id);
                
            if ($selectedLab) {
                $scheduleQuery->where('laboratory_id', $selectedLab);
            }
            
            $schedules = $scheduleQuery->get();
            
            // Get the first and last date of the current month
            $today = Carbon::today();
            $startDate = $today->copy()->startOfMonth();
            $endDate = $today->copy()->addMonths(2)->endOfMonth(); // Show 3 months
            
            // Loop through each schedule
            foreach ($schedules as $schedule) {
                $dayOfWeek = $schedule->day_of_week;
                
                // Get all dates of this day of week within the range
                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    if ($currentDate->dayOfWeek == $dayOfWeek) {
                        $events[] = [
                            'title' => Carbon::parse($schedule->start_time)->format('H:i') . ' - ' . 
                                      Carbon::parse($schedule->end_time)->format('H:i'),
                            'start' => $currentDate->format('Y-m-d') . 'T' . Carbon::parse($schedule->start_time)->format('H:i:s'),
                            'end' => $currentDate->format('Y-m-d') . 'T' . Carbon::parse($schedule->end_time)->format('H:i:s'),
                            'classNames' => ['fc-event-schedule'],
                            'extendedProps' => [
                                'type' => 'schedule',
                                'subject' => $schedule->subject_code . ' - ' . $schedule->subject_name,
                                'time' => Carbon::parse($schedule->start_time)->format('H:i') . ' - ' . 
                                         Carbon::parse($schedule->end_time)->format('H:i'),
                                'instructor' => $schedule->instructor_name,
                                'section' => $schedule->section,
                                'laboratory' => $schedule->laboratory->name . ' (' . $schedule->laboratory->building . ', Room ' . $schedule->laboratory->room_number . ')'
                            ]
                        ];
                    }
                    $currentDate->addDay();
                }
            }
        }
        
        // Get approved reservations
        $reservationsQuery = LaboratoryReservation::with('laboratory')
            ->where('status', LaboratoryReservation::STATUS_APPROVED)
            ->where('reservation_date', '>=', Carbon::today()->subDays(30))
            ->where('reservation_date', '<=', Carbon::today()->addMonths(2));
            
        if ($selectedLab) {
            $reservationsQuery->where('laboratory_id', $selectedLab);
        }
        
        $approvedReservations = $reservationsQuery->get();
        
        foreach ($approvedReservations as $reservation) {
            $events[] = [
                'title' => Carbon::parse($reservation->start_time)->format('H:i') . ' - ' . 
                          Carbon::parse($reservation->end_time)->format('H:i'),
                'start' => $reservation->reservation_date->format('Y-m-d') . 'T' . Carbon::parse($reservation->start_time)->format('H:i:s'),
                'end' => $reservation->reservation_date->format('Y-m-d') . 'T' . Carbon::parse($reservation->end_time)->format('H:i:s'),
                'classNames' => ['fc-event-approved'],
                'extendedProps' => [
                    'type' => 'reservation',
                    'status' => 'approved',
                    'purpose' => Str::limit($reservation->purpose, 100),
                    'laboratory' => $reservation->laboratory->name . ' (' . $reservation->laboratory->building . ', Room ' . $reservation->laboratory->room_number . ')',
                    'url' => route('ruser.laboratory.reservations.show', $reservation)
                ]
            ];
        }
        
        // Get current user's pending reservations
        $pendingReservations = LaboratoryReservation::with('laboratory')
            ->where('user_id', Auth::id())
            ->where('status', LaboratoryReservation::STATUS_PENDING)
            ->where('reservation_date', '>=', Carbon::today())
            ->get();
            
        foreach ($pendingReservations as $reservation) {
            $events[] = [
                'title' => Carbon::parse($reservation->start_time)->format('H:i') . ' - ' . 
                          Carbon::parse($reservation->end_time)->format('H:i') . ' (Pending)',
                'start' => $reservation->reservation_date->format('Y-m-d') . 'T' . Carbon::parse($reservation->start_time)->format('H:i:s'),
                'end' => $reservation->reservation_date->format('Y-m-d') . 'T' . Carbon::parse($reservation->end_time)->format('H:i:s'),
                'classNames' => ['fc-event-pending'],
                'extendedProps' => [
                    'type' => 'reservation',
                    'status' => 'pending',
                    'purpose' => Str::limit($reservation->purpose, 100),
                    'laboratory' => $reservation->laboratory->name . ' (' . $reservation->laboratory->building . ', Room ' . $reservation->laboratory->room_number . ')',
                    'url' => route('ruser.laboratory.reservations.show', $reservation)
                ]
            ];
        }
        
        return view('ruser.laboratory.reservation.calendar', compact(
            'events', 
            'laboratories', 
            'selectedLab', 
            'view'
        ));
    }
    
    /**
     * Show the quick reservation form
     */
    public function quickReserveForm()
    {
        $recentReservations = LaboratoryReservation::where('user_id', Auth::id())
            ->where(function($query) {
                $query->where('status', LaboratoryReservation::STATUS_APPROVED)
                      ->orWhere('status', LaboratoryReservation::STATUS_PENDING);
            })
            ->orderBy('created_at', 'desc')
            ->with('laboratory')
            ->take(5)
            ->get();

        return view('ruser.laboratory.reservation.quick-reserve', compact('recentReservations'));
    }
      /**
     * Process a quick reservation
     */
    public function quickReserveStore(Request $request)
    {
        $validatedData = $request->validate([
            'template' => 'required|exists:laboratory_reservations,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'purpose' => 'required|string|max:1000',
        ]);
        
        // Retrieve the template reservation
        $templateReservation = LaboratoryReservation::findOrFail($validatedData['template']);
        
        // Check that the template belongs to this user
        if ($templateReservation->user_id !== Auth::id()) {
            return back()->with('error', 'You can only use your own reservations as templates.');
        }
        
        // Get the laboratory from the template
        $laboratory = ComputerLaboratory::findOrFail($templateReservation->laboratory_id);
        
        // Convert time formats
        $reservationDate = Carbon::parse($validatedData['reservation_date'])->toDateString();
        $startTime = $validatedData['start_time'];
        $endTime = $validatedData['end_time'];
        
        // Use the ReservationConflictService to check for conflicts
        $conflicts = $this->conflictService->checkConflicts(
            $laboratory->id,
            $reservationDate,
            $startTime,
            $endTime
        );
        
        if ($conflicts['has_conflict']) {
            return back()->withInput()->with('error', $this->getConflictMessage($conflicts));
        }
        
        // Create the reservation using the template and new data
        $reservation = new LaboratoryReservation([
            'user_id' => Auth::id(),
            'laboratory_id' => $laboratory->id,
            'reservation_date' => $reservationDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'purpose' => $validatedData['purpose'],
            'num_students' => $templateReservation->num_students,
            'course_code' => $templateReservation->course_code,
            'subject' => $templateReservation->subject,
            'section' => $templateReservation->section,
            'status' => LaboratoryReservation::STATUS_PENDING,
        ]);
        
        $reservation->save();
        
        return redirect()->route('ruser.laboratory.reservations.index')
            ->with('success', 'Quick reservation request submitted successfully. It will be reviewed by the administrator.');
    }
    
    /**
     * Get a user-friendly conflict message
     */
    private function getConflictMessage($conflicts)
    {
        if (!$conflicts['has_conflict']) {
            return 'The selected time is available.';
        }
        
        switch ($conflicts['conflict_type']) {
            case 'single_reservation':
                return 'The selected time conflicts with an existing reservation.';
                
            case 'recurring_reservation':
                return 'The selected time conflicts with a recurring reservation.';
                
            case 'class_schedule':
                return 'The selected time conflicts with a regular class schedule.';
                
            default:
                return 'The selected time is not available.';
        }
    }
}
