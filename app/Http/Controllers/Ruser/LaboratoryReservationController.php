<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use App\Models\ComputerLaboratory;
use App\Models\LaboratoryReservation;
use App\Services\ReservationConflictService;
use App\Services\LaboratoryReservationService;
use App\Services\UserLaboratoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaboratoryReservationController extends Controller
{
    use ControllerHelpers;

    protected $conflictService;
    protected $reservationService;
    protected $userLaboratoryService;

    public function __construct(
        ReservationConflictService $conflictService, 
        LaboratoryReservationService $reservationService,
        UserLaboratoryService $userLaboratoryService
    ) {
        $this->conflictService = $conflictService;
        $this->reservationService = $reservationService;
        $this->userLaboratoryService = $userLaboratoryService;
    }

    /**
     * Show the laboratory reservation form
     */
    public function create(ComputerLaboratory $laboratory)
    {
        $data = $this->userLaboratoryService->getReservationFormData($laboratory);
        
        return view('ruser.laboratory.reservation.create', $data);
    }

    /**
     * Store a new laboratory reservation
     */
    public function store(Request $request, ComputerLaboratory $laboratory)
    {
        $rules = $this->reservationService->getValidationRules();
        $rules['num_students'] = 'required|integer|min:1|max:' . $laboratory->capacity;
        
        $validatedData = $this->validateRequest($request, $rules);
        
        $result = $this->reservationService->createReservation($laboratory, $validatedData);
        
        if ($result['success']) {
            return redirect()->route('ruser.laboratory.reservations.confirmation', $result['reservation'])
                ->with('success', $result['message']);
        } 
        
        return back()->withInput()->with('error', $result['message']);
    }

    /**
     * Check for conflicts when creating a reservation
     */
    public function checkConflicts(Request $request, ComputerLaboratory $laboratory)
    {
        $validatedData = $request->validate([
            'reservation_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $conflictCheck = $this->conflictService->checkConflicts(
            $laboratory->id,
            $validatedData['reservation_date'],
            $validatedData['start_time'],
            $validatedData['end_time']
        );

        $conflicts = [];
        
        if ($conflictCheck['has_conflict']) {
            $conflictDetails = $conflictCheck['conflict_details'];
            
            switch ($conflictCheck['conflict_type']) {
                case 'single_reservation':
                    $conflicts[] = [
                        'type' => 'Existing Reservation',
                        'description' => 'Reserved by ' . $conflictDetails->user->name,
                        'time' => Carbon::parse($conflictDetails->start_time)->format('g:i A') . ' - ' . 
                                 Carbon::parse($conflictDetails->end_time)->format('g:i A')
                    ];
                    break;
                    
                case 'recurring_reservation':
                    $conflicts[] = [
                        'type' => 'Recurring Reservation',
                        'description' => 'Recurring reservation by ' . $conflictDetails->user->name,
                        'time' => Carbon::parse($conflictDetails->start_time)->format('g:i A') . ' - ' . 
                                 Carbon::parse($conflictDetails->end_time)->format('g:i A')
                    ];
                    break;
                    
                case 'class_schedule':
                    $conflicts[] = [
                        'type' => 'Class Schedule',
                        'description' => $conflictDetails->subject_name . ' (' . $conflictDetails->instructor_name . ')',
                        'time' => Carbon::parse($conflictDetails->start_time)->format('g:i A') . ' - ' . 
                                 Carbon::parse($conflictDetails->end_time)->format('g:i A')
                    ];
                    break;
            }
        }

        return response()->json([
            'has_conflict' => $conflictCheck['has_conflict'],
            'conflicts' => $conflicts
        ]);
    }

    /**
     * Get schedules for a specific date (day of week)
     */
    public function getSchedulesForDate(Request $request, ComputerLaboratory $laboratory)
    {
        $request->validate([
            'date' => 'required|date'
        ]);
        
        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->format('l'); // Full day name (e.g., 'Monday')
        
        $schedules = $this->userLaboratoryService->getSchedulesForDay($laboratory, $dayOfWeek);
        
        return response()->json([
            'schedules' => $schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'day' => $schedule->day,
                    'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($schedule->end_time)->format('H:i'),
                    'subject' => $schedule->subject,
                    'instructor' => $schedule->instructor
                ];
            }),
            'day_name' => $dayOfWeek,
            'has_schedules' => $schedules->isNotEmpty()
        ]);
    }

    /**
     * Display the user's reservations
     */
    public function index(Request $request)
    {
        $data = $this->userLaboratoryService->getUserReservationsData(Auth::id(), $request);
        
        return view('ruser.laboratory.reservation.index', $data);
    }
    
    /**
     * Show a specific reservation
     */
    public function show(LaboratoryReservation $reservation)
    {
        if (!$this->userLaboratoryService->canAccessReservation($reservation, Auth::id())) {
            return redirect()->route('ruser.laboratory.reservations.index')
                ->with('error', 'You do not have permission to view this reservation.');
        }
        
        return view('ruser.laboratory.reservation.show', compact('reservation'));
    }

    /**
     * Show reservation confirmation page
     */
    public function confirmation(LaboratoryReservation $reservation)
    {
        if (!$this->userLaboratoryService->canAccessReservation($reservation, Auth::id())) {
            return redirect()->route('ruser.laboratory.reservations.index')
                ->with('error', 'You do not have permission to view this reservation.');
        }
        
        return view('ruser.laboratory.reservation.confirmation', compact('reservation'));
    }
    
    /**
     * Cancel a pending reservation
     */
    public function cancel(LaboratoryReservation $reservation)
    {
        $result = $this->reservationService->cancelReservation($reservation);
        
        return redirect()->route('ruser.laboratory.reservations.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Show calendar view of reservations
     */
    public function calendar(Request $request)
    {
        $calendarData = $this->reservationService->getCalendarData($request);
        
        return view('ruser.laboratory.reservation.calendar', [
            'events' => $calendarData['events'],
            'laboratories' => $calendarData['laboratories'],
            'selectedLab' => $request->input('laboratory'),
            'view' => $request->input('view', 'dayGridMonth')
        ]);
    }
    
    /**
     * Show the quick reservation form
     */
    public function quickReserve()
    {
        $recentReservations = $this->userLaboratoryService->getRecentReservations(Auth::id(), 5);

        return view('ruser.laboratory.reservation.quick-reserve', compact('recentReservations'));
    }

    /**
     * Process a quick reservation
     */
    public function quickReserveStore(Request $request)
    {
        $validatedData = $this->validateRequest($request, [
            'template' => 'required|exists:laboratory_reservations,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'purpose' => 'required|string|max:1000',
        ]);
        
        $result = $this->userLaboratoryService->createQuickReservation($validatedData, Auth::id());
        
        if ($result['success']) {
            return redirect()->route('ruser.laboratory.reservations.confirmation', $result['reservation'])
                ->with('success', $result['message']);
        }
        
        return back()->withInput()->with('error', $result['message']);
    }
}
