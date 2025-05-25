<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComputerLaboratory;
use App\Services\ReservationConflictService;
use Illuminate\Http\Request;

class ReservationConflictController extends Controller
{
    protected $conflictService;

    public function __construct(ReservationConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
    }
    
    /**
     * Check if a reservation time has conflicts
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkConflict(Request $request)
    {
        $request->validate([
            'laboratory_id' => 'required|exists:computer_laboratories,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reservation_id' => 'nullable|exists:laboratory_reservations,id',
        ]);

        $conflicts = $this->conflictService->checkConflicts(
            $request->laboratory_id,
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->reservation_id
        );

        return response()->json([
            'success' => true,
            'has_conflict' => $conflicts['has_conflict'],
            'conflict_type' => $conflicts['conflict_type'],
            'message' => $this->getConflictMessage($conflicts)
        ]);
    }
      /**
     * Check recurring reservation conflicts
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRecurringConflicts(Request $request)
    {
        $request->validate([
            'laboratory_id' => 'required|exists:computer_laboratories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'recurrence_pattern' => 'required|in:daily,weekly,monthly',
            'reservation_id' => 'nullable|exists:laboratory_reservations,id',
        ]);

        $conflicts = $this->conflictService->checkRecurringReservationConflicts(
            $request->laboratory_id,
            $request->start_date,
            $request->end_date,
            $request->start_time,
            $request->end_time,
            $request->recurrence_pattern,
            $request->reservation_id
        );
        
        // Get current term info
        $currentTerm = \App\Models\AcademicTerm::where('is_current', true)->first();
        
        // Group conflicts by type
        $conflictsByType = [
            'single_reservation' => [],
            'recurring_reservation' => [],
            'class_schedule' => [],
            'outside_term' => []
        ];
        
        foreach ($conflicts as $conflict) {
            if (isset($conflict['conflict_type']) && isset($conflictsByType[$conflict['conflict_type']])) {
                $conflictsByType[$conflict['conflict_type']][] = $conflict;
            }
        }

        return response()->json([
            'success' => true,
            'has_conflicts' => !empty($conflicts),
            'conflicts' => $conflicts,
            'conflict_count' => count($conflicts),
            'conflicts_by_type' => $conflictsByType,
            'current_term' => $currentTerm ? [
                'id' => $currentTerm->id,
                'name' => $currentTerm->name,
                'start_date' => $currentTerm->start_date,
                'end_date' => $currentTerm->end_date,
                'is_current' => $currentTerm->is_current
            ] : null
        ]);
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
