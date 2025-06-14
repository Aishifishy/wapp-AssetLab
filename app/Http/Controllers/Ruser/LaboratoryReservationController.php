<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Models\ComputerLaboratory;
use App\Models\LaboratoryReservation;
use App\Models\LaboratorySchedule;
use App\Models\AcademicTerm;
use App\Mail\LaboratoryReservationStatusChanged;
use App\Services\ReservationConflictService;
use App\Services\LaboratoryReservationService;
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
    protected $reservationService;

    public function __construct(ReservationConflictService $conflictService, LaboratoryReservationService $reservationService)
    {
        $this->conflictService = $conflictService;
        $this->reservationService = $reservationService;
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
        // Get base validation rules from service
        $rules = $this->reservationService->getValidationRules();
        
        // Override capacity validation for this specific laboratory
        $rules['num_students'] = 'required|integer|min:1|max:' . $laboratory->capacity;
        
        $validatedData = $request->validate($rules);
        
        $result = $this->reservationService->createReservation($laboratory, $validatedData);
        
        if ($result['success']) {
            return redirect()->route('ruser.laboratory.reservations.index')
                ->with('success', $result['message']);
        } 
        else {
            return back()->withInput()->with('error', $result['message']);
        }
    }

    /**
     * Display the user's reservations
     */
    public function index(Request $request)
    {
        $reservations = $this->reservationService->getUserReservations($request);
        
        // For backward compatibility, we'll still separate them by status
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
        
        return view('ruser.laboratory.reservation.index', compact(
            'upcomingReservations',
            'pendingReservations',
            'reservations'
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
        $result = $this->reservationService->cancelReservation($reservation);
        
        if ($result['success']) {
            return redirect()->route('ruser.laboratory.reservations.index')
                ->with('success', $result['message']);
        } else {
            return redirect()->route('ruser.laboratory.reservations.index')
                ->with('error', $result['message']);
        }
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
