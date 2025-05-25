<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\ComputerLaboratory;
use App\Models\LaboratorySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RDashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */    public function index(Request $request)
    {
        $user = Auth::user();
        $activityType = $request->input('activity_type', 'all');

        // Get equipment request statistics
        $pendingRequests = EquipmentRequest::where('user_id', $user->id)
            ->where('status', EquipmentRequest::STATUS_PENDING)
            ->count();
            
        $currentlyBorrowed = EquipmentRequest::where('user_id', $user->id)
            ->where('status', EquipmentRequest::STATUS_APPROVED)
            ->whereNull('returned_at')
            ->count();
            
        // Get upcoming returns (equipment due in the next 7 days)
        $upcomingReturns = EquipmentRequest::where('user_id', $user->id)
            ->where('status', EquipmentRequest::STATUS_APPROVED)
            ->whereNull('returned_at')
            ->where('requested_until', '>=', Carbon::now())
            ->where('requested_until', '<=', Carbon::now()->addDays(7))
            ->count();
            
        // Get available laboratories
        $availableLabs = ComputerLaboratory::where('status', 'available')->count();

        // Get recent activities - showing latest 10 actions
        $equipmentActivitiesQuery = EquipmentRequest::where('user_id', $user->id)
            ->with(['equipment']);
            
        // Currently we only have equipment activities, but this section can be
        // extended in the future to include laboratory activities when implemented
        $recentActivities = collect();
          if ($activityType == 'all' || $activityType == 'equipment') {
            $recentActivities = $equipmentActivitiesQuery
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($request) {
                    $activityText = '';
                    $statusClass = '';
                    $activityType = 'equipment';
                    
                    switch($request->status) {
                        case EquipmentRequest::STATUS_PENDING:
                            $activityText = "Requested {$request->equipment->name}";
                            $statusClass = 'yellow';
                            break;
                        case EquipmentRequest::STATUS_APPROVED:
                            if ($request->returned_at) {
                                $activityText = "Returned {$request->equipment->name}";
                                $statusClass = 'green';
                            } else {
                                $activityText = "Borrowed {$request->equipment->name}";
                                $statusClass = 'blue';
                            }
                            break;
                        case EquipmentRequest::STATUS_REJECTED:
                            $activityText = "Request for {$request->equipment->name} was rejected";
                            $statusClass = 'red';
                            break;
                    }
                    
                    return [
                        'id' => $request->id,
                        'time' => $request->created_at,
                        'description' => $activityText,
                        'status' => $request->status,
                        'status_class' => $statusClass,
                        'equipment_name' => $request->equipment->name,
                        'purpose' => $request->purpose,
                        'activity_type' => $activityType
                    ];
                });
            
            // Sort all activities by time if we have multiple types
            if ($activityType == 'all') {
                $recentActivities = $recentActivities->sortByDesc('time')->take(10);
            }        } elseif ($activityType == 'laboratory') {
            // Get laboratory reservation activities
            $recentActivities = \App\Models\LaboratoryReservation::where('user_id', $user->id)
                ->latest()
                ->with(['laboratory'])
                ->take(10)
                ->get()
                ->map(function ($reservation) {
                    $activityText = '';
                    $statusClass = '';
                    $activityType = 'laboratory';
                    
                    switch($reservation->status) {
                        case \App\Models\LaboratoryReservation::STATUS_PENDING:
                            $activityText = "Requested reservation for {$reservation->laboratory->name}";
                            $statusClass = 'yellow';
                            break;
                        case \App\Models\LaboratoryReservation::STATUS_APPROVED:
                            $activityText = "Laboratory reservation approved for {$reservation->laboratory->name}";
                            $statusClass = 'green';
                            break;
                        case \App\Models\LaboratoryReservation::STATUS_REJECTED:
                            $activityText = "Laboratory reservation rejected for {$reservation->laboratory->name}";
                            $statusClass = 'red';
                            break;
                        case \App\Models\LaboratoryReservation::STATUS_CANCELLED:
                            $activityText = "Cancelled reservation for {$reservation->laboratory->name}";
                            $statusClass = 'gray';
                            break;
                    }
                    
                    return [
                        'id' => $reservation->id,
                        'time' => $reservation->created_at,
                        'description' => $activityText,
                        'status' => $reservation->status,
                        'status_class' => $statusClass,
                        'equipment_name' => $reservation->laboratory->name,
                        'purpose' => $reservation->purpose,
                        'activity_type' => $activityType
                    ];
                });
        }return view('ruser.dashboard', compact(
            'pendingRequests',
            'currentlyBorrowed',
            'upcomingReturns',
            'availableLabs',
            'recentActivities',
            'activityType'
        ));
    }
}
