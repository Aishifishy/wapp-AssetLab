<?php

namespace App\Services;

use App\Models\LaboratoryReservation;
use App\Models\ComputerLaboratory;
use App\Models\LaboratorySchedule;
use App\Models\AcademicTerm;
use App\Services\ReservationConflictService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * User Laboratory Reservation service
 */
class UserLaboratoryService extends BaseService
{
    protected function getModel()
    {
        return LaboratoryReservation::class;
    }

    /**
     * Get user's laboratory reservation statistics
     */
    public function getUserStats($userId)
    {
        return [
            'pending_reservations' => LaboratoryReservation::forUser($userId)->pending()->count(),
            'approved_reservations' => LaboratoryReservation::forUser($userId)->approved()->count(),
            'total_reservations' => LaboratoryReservation::forUser($userId)->count(),
        ];
    }

    /**
     * Get available laboratories
     */
    public function getAvailableLaboratories()
    {
        return ComputerLaboratory::where('status', 'available')
            ->orderBy('building')
            ->orderBy('room_number')
            ->get();
    }

    /**
     * Get available laboratories count
     */
    public function getAvailableLabsCount()
    {
        return ComputerLaboratory::where('status', 'available')->count();
    }

    /**
     * Get user's recent laboratory activities for dashboard
     */
    public function getRecentActivities($userId, $limit = 15)
    {
        return LaboratoryReservation::with(['laboratory', 'approvedBy', 'rejectedBy'])
            ->forUser($userId)
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($reservation) {
                return $this->formatActivity($reservation);
            });
    }

    /**
     * Get data for reservation form
     */
    public function getReservationFormData(ComputerLaboratory $laboratory)
    {
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        $schedules = collect([]);
        if ($currentTerm) {
            $schedules = LaboratorySchedule::where('laboratory_id', $laboratory->id)
                ->where('academic_term_id', $currentTerm->id)
                ->get();
        }
        
        $startDate = now()->startOfDay();
        $endDate = now()->addDays(14)->endOfDay();
        
        $existingReservations = LaboratoryReservation::where('laboratory_id', $laboratory->id)
            ->whereBetween('reservation_date', [$startDate, $endDate])
            ->approved()
            ->get();
            
        return compact('laboratory', 'schedules', 'existingReservations', 'currentTerm');
    }

    /**
     * Get schedules for a specific day of the week
     */
    public function getSchedulesForDay(ComputerLaboratory $laboratory, $dayOfWeek)
    {
        $currentTerm = AcademicTerm::where('is_current', true)->first();
        
        if (!$currentTerm) {
            return collect([]);
        }
        
        // Map day names to match the database format
        $dayMapping = [
            'sunday' => 'Sunday',
            'monday' => 'Monday', 
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday'
        ];
        
        $dayName = $dayMapping[strtolower($dayOfWeek)] ?? $dayOfWeek;
        
        return LaboratorySchedule::where('laboratory_id', $laboratory->id)
            ->where('academic_term_id', $currentTerm->id)
            ->where('day', $dayName)
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get user reservations data for index view
     */
    public function getUserReservationsData($userId, $request)
    {
        $perPage = $request->get('per_page', 15);
        $allowedPerPage = [5, 10, 15, 25, 50, 100];
        
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }

        // Get paginated reservations using service
        $reservations = $this->getModel()::forUser($userId)
            ->with('laboratory')
            ->latest()
            ->paginate($perPage);
        
        // Get categorized reservations for backward compatibility
        $upcomingReservations = LaboratoryReservation::forUser($userId)
            ->approved()
            ->where('reservation_date', '>=', now()->toDateString())
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->with('laboratory')
            ->get();
            
        $pendingReservations = LaboratoryReservation::forUser($userId)
            ->pending()
            ->orderBy('created_at', 'desc')
            ->with('laboratory')
            ->get();
            
        // Get past/cancelled/rejected reservations with pagination
        $pastReservations = LaboratoryReservation::forUser($userId)
            ->whereIn('status', [LaboratoryReservation::STATUS_REJECTED, LaboratoryReservation::STATUS_CANCELLED])
            ->orWhere(function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->approved()
                      ->where('reservation_date', '<', now()->toDateString());
            })
            ->with('laboratory')
            ->latest()
            ->paginate($perPage, ['*'], 'past_page');
        
        return compact('upcomingReservations', 'pendingReservations', 'reservations', 'pastReservations', 'perPage');
    }

    /**
     * Check if user can access reservation
     */
    public function canAccessReservation(LaboratoryReservation $reservation, $userId)
    {
        return $reservation->user_id === $userId;
    }



    /**
     * Get conflict message
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

    /**
     * Format activity data for display
     */
    private function formatActivity($reservation)
    {
        $activityText = '';
        $statusClass = '';
        
        switch($reservation->status) {
            case LaboratoryReservation::STATUS_PENDING:
                $activityText = "<strong>Laboratory Reservation Submitted</strong> for {$reservation->laboratory->name}";
                if ($reservation->reservation_date) {
                    $activityText .= '<br><small class="text-gray-600">Requested date: ' . 
                                   $reservation->reservation_date->format('M j, Y') . '</small>';
                }
                if ($reservation->formatted_start_time && $reservation->formatted_end_time) {
                    $activityText .= '<br><small class="text-gray-600">Requested time: ' . 
                                   $reservation->formatted_start_time . ' - ' . $reservation->formatted_end_time . '</small>';
                }
                $statusClass = 'yellow';
                break;
                
            case LaboratoryReservation::STATUS_APPROVED:
                $activityText = "<strong>Laboratory Reservation Approved</strong> for {$reservation->laboratory->name}";
                if ($reservation->reservation_date) {
                    $activityText .= '<br><small class="text-green-600">Reserved date: ' . 
                                   $reservation->reservation_date->format('M j, Y') . '</small>';
                }
                if ($reservation->formatted_start_time && $reservation->formatted_end_time) {
                    $activityText .= '<br><small class="text-green-600">Reserved time: ' . 
                                   $reservation->formatted_start_time . ' - ' . $reservation->formatted_end_time . '</small>';
                }
                if ($reservation->approvedBy) {
                    $activityText .= '<br><small class="text-green-600">Approved by: ' . 
                                   $reservation->approvedBy->name . ' on ' . 
                                   $reservation->approved_at->format('M j, Y g:i A') . '</small>';
                }
                $statusClass = 'green';
                break;
                
            case LaboratoryReservation::STATUS_REJECTED:
                $activityText = "<strong>Laboratory Reservation Declined</strong> for {$reservation->laboratory->name}";
                if ($reservation->reservation_date) {
                    $activityText .= '<br><small class="text-gray-600">Requested date: ' . 
                                   $reservation->reservation_date->format('M j, Y') . '</small>';
                }
                if ($reservation->formatted_start_time && $reservation->formatted_end_time) {
                    $activityText .= '<br><small class="text-gray-600">Requested time: ' . 
                                   $reservation->formatted_start_time . ' - ' . $reservation->formatted_end_time . '</small>';
                }
                if ($reservation->rejectedBy) {
                    $activityText .= '<br><small class="text-red-600">Reviewed by: ' . 
                                   $reservation->rejectedBy->name . ' on ' . 
                                   $reservation->rejected_at->format('M j, Y g:i A') . '</small>';
                }
                if ($reservation->rejection_reason) {
                    $activityText .= '<br><small class="text-red-600">Reason: ' . 
                                   Str::limit($reservation->rejection_reason, 60) . '</small>';
                }
                $statusClass = 'red';
                break;
                
            case LaboratoryReservation::STATUS_CANCELLED:
                $activityText = "<strong>Laboratory Reservation Cancelled</strong> for {$reservation->laboratory->name}";
                if ($reservation->reservation_date) {
                    $activityText .= '<br><small class="text-gray-600">Originally scheduled for: ' . 
                                   $reservation->reservation_date->format('M j, Y') . '</small>';
                }
                $statusClass = 'gray';
                break;
                
            default:
                $activityText = "Status update for {$reservation->laboratory->name} reservation";
                $statusClass = 'gray';
                break;
        }
        
        return [
            'id' => $reservation->id,
            'time' => $reservation->updated_at ?? $reservation->created_at, // Use updated_at for status changes
            'description' => $activityText,
            'status' => $reservation->status,
            'status_class' => $statusClass,
            'equipment_name' => $reservation->laboratory->name,
            'category_name' => 'Laboratory',
            'purpose' => $reservation->purpose,
            'activity_type' => 'reservation', // Use 'reservation' type for proper status badge colors
            'reservation_date' => $reservation->reservation_date,
            'start_time' => $reservation->formatted_start_time,
            'end_time' => $reservation->formatted_end_time
        ];
    }
}
