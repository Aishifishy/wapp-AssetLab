<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComputerLaboratory;
use App\Services\ReservationConflictService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReservationConflictController extends Controller
{
    protected $conflictService;

    public function __construct(ReservationConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
    }
    
    /**
     * Check if a reservation time has conflicts (Optimized with validation)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkConflict(Request $request)
    {
        try {
            $validated = $request->validate([
                'laboratory_id' => 'required|integer|exists:computer_laboratories,id',
                'date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'reservation_id' => 'nullable|integer|exists:laboratory_reservations,id',
            ]);

            // Additional time validation
            $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
            $endTime = Carbon::createFromFormat('H:i', $validated['end_time']);
            
            // Check for reasonable time duration (at least 30 minutes, max 8 hours)
            $duration = $endTime->diffInMinutes($startTime);
            if ($duration < 30) {
                return response()->json([
                    'success' => false,
                    'has_conflict' => false,
                    'error' => 'Reservation must be at least 30 minutes long.'
                ], 422);
            }
            
            if ($duration > 480) { // 8 hours
                return response()->json([
                    'success' => false,
                    'has_conflict' => false,
                    'error' => 'Reservation cannot exceed 8 hours.'
                ], 422);
            }

            $conflicts = $this->conflictService->checkConflicts(
                $validated['laboratory_id'],
                $validated['date'],
                $validated['start_time'],
                $validated['end_time'],
                $validated['reservation_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'has_conflict' => $conflicts['has_conflict'],
                'conflict_type' => $conflicts['conflict_type'],
                'message' => $this->getConflictMessage($conflicts)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Conflict check failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while checking for conflicts. Please try again.'
            ], 500);
        }
    }
      /**
     * Check recurring reservation conflicts (Optimized with validation)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRecurringConflicts(Request $request)
    {
        try {
            $validated = $request->validate([
                'laboratory_id' => 'required|integer|exists:computer_laboratories,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'recurrence_pattern' => 'required|in:daily,weekly,monthly',
                'reservation_id' => 'nullable|integer|exists:laboratory_reservations,id',
            ]);

            // Additional validation
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
            $endTime = Carbon::createFromFormat('H:i', $validated['end_time']);
            
            // Check date range is reasonable (max 1 year)
            if ($startDate->diffInDays($endDate) > 365) {
                return response()->json([
                    'success' => false,
                    'error' => 'Recurring reservation cannot span more than 1 year.'
                ], 422);
            }
            
            // Check for reasonable time duration (at least 30 minutes, max 8 hours)
            $duration = $endTime->diffInMinutes($startTime);
            if ($duration < 30) {
                return response()->json([
                    'success' => false,
                    'error' => 'Reservation must be at least 30 minutes long.'
                ], 422);
            }
            
            if ($duration > 480) { // 8 hours
                return response()->json([
                    'success' => false,
                    'error' => 'Reservation cannot exceed 8 hours.'
                ], 422);
            }

            $conflicts = $this->conflictService->checkRecurringReservationConflicts(
                $validated['laboratory_id'],
                $validated['start_date'],
                $validated['end_date'],
                $validated['start_time'],
                $validated['end_time'],
                $validated['recurrence_pattern'],
                $validated['reservation_id'] ?? null
            );
            
            // Get current term info
            $currentTerm = \App\Models\AcademicTerm::where('is_current', true)->first();
            
            // Group conflicts by type for better UI handling
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
                ] : null,
                'performance_metrics' => [
                    'dates_checked' => count($conflicts),
                    'execution_time' => round((microtime(true) - LARAVEL_START) * 1000, 2) . 'ms'
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Recurring conflict check failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while checking for recurring conflicts. Please try again.'
            ], 500);
        }
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
