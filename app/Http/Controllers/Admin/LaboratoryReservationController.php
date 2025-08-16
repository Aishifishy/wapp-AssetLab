<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use App\Models\LaboratoryReservation;
use App\Models\ComputerLaboratory;
use App\Models\AcademicTerm;
use App\Models\Ruser;
use App\Services\LaboratoryReservationService;
use Illuminate\Http\Request;

class LaboratoryReservationController extends Controller
{
    use ControllerHelpers;

    protected $reservationService;

    public function __construct(LaboratoryReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }
    /**
     * Display all laboratory reservations
     */
    public function index(Request $request)
    {
        $data = $this->reservationService->getReservationsIndex($request);
        
        return view('admin.laboratory.reservations.index', $data);
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
        $result = $this->reservationService->approveReservationWithChecks($reservation);

        if (!$result['success']) {
            return redirect()->route('admin.laboratory.reservations.show', $reservation)
                ->with('error', $result['message']);
        }

        return redirect()->route('admin.laboratory.reservations.show', $reservation)
            ->with('success', $result['message']);
    }
    
    /**
     * Reject a pending reservation
     */
    public function reject(Request $request, LaboratoryReservation $reservation)
    {
        $validated = $this->validateRequest($request, [
            'rejection_reason' => 'required|string|max:500',
        ]);

        $result = $this->reservationService->rejectReservationWithReason($reservation, $validated['rejection_reason']);

        if (!$result['success']) {
            return redirect()->route('admin.laboratory.reservations.show', $reservation)
                ->with('error', $result['message']);
        }

        return redirect()->route('admin.laboratory.reservations.show', $reservation)
            ->with('success', $result['message']);
    }
    
    /**
     * Delete a reservation
     */
    public function destroy(LaboratoryReservation $reservation)
    {
        $result = $this->reservationService->deleteReservationWithLogging($reservation);

        return redirect()->route('admin.laboratory.reservations.index')
            ->with('success', $result['message']);
    }
}
