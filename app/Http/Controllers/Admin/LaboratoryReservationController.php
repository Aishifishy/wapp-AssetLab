<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaboratoryReservation;
use App\Models\ComputerLaboratory;
use App\Models\AcademicTerm;
use App\Models\Ruser;
use App\Mail\LaboratoryReservationStatusChanged;
use App\Services\ReservationConflictService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LaboratoryReservationController extends Controller
{
    /**
     * Display all laboratory reservations
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $laboratory = $request->input('laboratory');
        
        // Base query
        $query = LaboratoryReservation::with(['user', 'laboratory'])
            ->orderByDesc('created_at');
        
        // Filter by status if provided
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        // Filter by laboratory if provided
        if ($laboratory) {
            $query->where('laboratory_id', $laboratory);
        }
        
        // Get all reservations paginated
        $reservations = $query->paginate(15);
        
        // Get all laboratories for filter
        $laboratories = ComputerLaboratory::orderBy('name')->get();
        
        // Get counts for each status
        $statusCounts = [
            'pending' => LaboratoryReservation::where('status', LaboratoryReservation::STATUS_PENDING)->count(),
            'approved' => LaboratoryReservation::where('status', LaboratoryReservation::STATUS_APPROVED)
                ->where('reservation_date', '>=', now()->toDateString())
                ->count(),
            'rejected' => LaboratoryReservation::where('status', LaboratoryReservation::STATUS_REJECTED)->count(),
            'cancelled' => LaboratoryReservation::where('status', LaboratoryReservation::STATUS_CANCELLED)->count(),
            'all' => LaboratoryReservation::count(),
        ];
        
        return view('admin.laboratory.reservations.index', compact(
            'reservations',
            'laboratories',
            'status',
            'laboratory',
            'statusCounts'
        ));
    }
    
    /**
     * Display a specific reservation
     */
    public function show(LaboratoryReservation $reservation)
    {
        // Load the necessary relationships
        $reservation->load(['user', 'laboratory']);
        
        return view('admin.laboratory.reservations.show', compact('reservation'));
    }
    
    /**
     * Approve a pending reservation
     */
    public function approve(Request $request, LaboratoryReservation $reservation)
    {
        // Check if the reservation is pending
        if ($reservation->status !== LaboratoryReservation::STATUS_PENDING) {
            return redirect()->route('admin.laboratory.reservations.show', $reservation)
                ->with('error', 'Only pending reservations can be approved.');
        }
          // Check for conflicts using centralized service
        $conflictService = new ReservationConflictService();
        $conflicts = $conflictService->checkConflicts(
            $reservation->laboratory_id, 
            $reservation->reservation_date, 
            $reservation->start_time, 
            $reservation->end_time,
            $reservation->id  // Exclude current reservation
        );
        
        if ($conflicts['has_conflict']) {
            $errorMessage = 'This reservation conflicts with ';
            switch ($conflicts['conflict_type']) {
                case 'single_reservation':
                    $errorMessage .= 'another approved reservation.';
                    break;
                case 'recurring_reservation':
                    $errorMessage .= 'a recurring reservation.';
                    break;
                case 'class_schedule':
                    $errorMessage .= 'a scheduled class.';
                    break;
                default:
                    $errorMessage .= 'another booking.';
            }
            $errorMessage .= ' Please check schedule before approving.';
            
            return redirect()->route('admin.laboratory.reservations.show', $reservation)
                ->with('error', $errorMessage);
        }
          // Update status and save
        $reservation->status = LaboratoryReservation::STATUS_APPROVED;
        $reservation->save();
        
        // Send email notification to user
        $reservation->load(['user', 'laboratory']);
        if ($reservation->user->email) {
            try {
                Mail::to($reservation->user->email)->send(new LaboratoryReservationStatusChanged($reservation));
            } catch (\Exception $e) {
                Log::error('Failed to send reservation approval email', [
                    'error' => $e->getMessage(),
                    'reservation_id' => $reservation->id
                ]);
            }
        }
        
        // Log the approval
        Log::info('Laboratory reservation approved', [
            'reservation_id' => $reservation->id,
            'laboratory_id' => $reservation->laboratory_id,
            'user_id' => $reservation->user_id,
            'admin_id' => auth()->guard('admin')->id()
        ]);
        
        return redirect()->route('admin.laboratory.reservations.show', $reservation)
            ->with('success', 'Reservation has been approved successfully.');
    }
    
    /**
     * Reject a pending reservation
     */
    public function reject(Request $request, LaboratoryReservation $reservation)
    {
        // Validate rejection reason
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        // Check if the reservation is pending
        if ($reservation->status !== LaboratoryReservation::STATUS_PENDING) {
            return redirect()->route('admin.laboratory.reservations.show', $reservation)
                ->with('error', 'Only pending reservations can be rejected.');
        }
          // Update status, add rejection reason, and save
        $reservation->status = LaboratoryReservation::STATUS_REJECTED;
        $reservation->rejection_reason = $validated['rejection_reason'];
        $reservation->save();
        
        // Send email notification to user
        $reservation->load(['user', 'laboratory']);
        if ($reservation->user->email) {
            try {
                Mail::to($reservation->user->email)->send(new LaboratoryReservationStatusChanged($reservation));
            } catch (\Exception $e) {
                Log::error('Failed to send reservation rejection email', [
                    'error' => $e->getMessage(),
                    'reservation_id' => $reservation->id
                ]);
            }
        }
        
        // Log the rejection
        Log::info('Laboratory reservation rejected', [
            'reservation_id' => $reservation->id,
            'laboratory_id' => $reservation->laboratory_id,
            'user_id' => $reservation->user_id,
            'admin_id' => auth()->guard('admin')->id(),
            'reason' => $validated['rejection_reason']
        ]);
        
        return redirect()->route('admin.laboratory.reservations.show', $reservation)
            ->with('success', 'Reservation has been rejected.');
    }
    
    /**
     * Delete a reservation
     */
    public function destroy(LaboratoryReservation $reservation)
    {
        // Log before deletion
        Log::info('Laboratory reservation deleted', [
            'reservation_id' => $reservation->id,
            'laboratory_id' => $reservation->laboratory_id,
            'user_id' => $reservation->user_id,
            'admin_id' => auth()->guard('admin')->id()
        ]);
        
        // Delete the reservation
        $reservation->delete();
        
        return redirect()->route('admin.laboratory.reservations.index')
            ->with('success', 'Reservation has been deleted permanently.');
    }
}
