<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Ruser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Log dashboard access
        $admin = Auth::guard('admin')->user();
        Log::info('Admin accessing dashboard', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email
        ]);
        
        // Get activity type from request (default is 'all')
        $activityType = $request->input('activity_type', 'all');

        // Get equipment statistics
        $totalEquipment = Equipment::count();
        $borrowedEquipment = Equipment::where('status', 'borrowed')->count();
        $pendingRequests = \App\Models\EquipmentRequest::where('status', \App\Models\EquipmentRequest::STATUS_PENDING)->count();

        // Get laboratory statistics
        $todayBookings = \App\Models\LaboratoryReservation::whereDate('reservation_date', today())
            ->where('status', \App\Models\LaboratoryReservation::STATUS_APPROVED)
            ->count();
        $pendingReservations = \App\Models\LaboratoryReservation::where('status', \App\Models\LaboratoryReservation::STATUS_PENDING)
            ->count();
        $activeClasses = \App\Models\LaboratorySchedule::whereHas('academicTerm', function($query) {
                $query->where('is_current', true);
            })
            ->count();

        // Get user statistics
        $totalUsers = Ruser::count();
        $activeUsers = Ruser::whereHas('equipmentRequests', function($query) {
            $query->whereDate('created_at', '>=', now()->subDays(30));
        })->count();
        $newUsers = Ruser::where('created_at', '>=', now()->subWeek())->count();

        // Get recent activities based on activity type
        $recentActivities = collect([]);
        
        // Get equipment related activities
        if ($activityType == 'all' || $activityType == 'equipment') {
            $equipmentActivities = \App\Models\EquipmentRequest::with(['user', 'equipment'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($request) {
                    $statusClass = '';
                    
                    switch($request->status) {
                        case \App\Models\EquipmentRequest::STATUS_PENDING:
                            $statusClass = 'yellow';
                            break;
                        case \App\Models\EquipmentRequest::STATUS_APPROVED:
                            if ($request->returned_at) {
                                $statusClass = 'green';
                            } else {
                                $statusClass = 'blue';
                            }
                            break;
                        case \App\Models\EquipmentRequest::STATUS_REJECTED:
                            $statusClass = 'red';
                            break;
                    }
                    
                    return (object)[
                        'id' => $request->id,
                        'created_at' => $request->created_at,
                        'description' => "Equipment " . ($request->status === \App\Models\EquipmentRequest::STATUS_PENDING ? "Request" : 
                                        ($request->status === \App\Models\EquipmentRequest::STATUS_APPROVED && !$request->returned_at ? "Borrowed" : 
                                        ($request->status === \App\Models\EquipmentRequest::STATUS_APPROVED && $request->returned_at ? "Returned" : "Rejected"))),
                        'notes' => $request->purpose,
                        'user_name' => $request->user ? $request->user->name : 'Unknown User',
                        'item_name' => $request->equipment ? $request->equipment->name : 'Unknown Equipment',
                        'status' => $request->status,
                        'status_class' => $statusClass,
                        'activity_type' => 'equipment'
                    ];
                });
            
            $recentActivities = $equipmentActivities;
        }
        
        // Get laboratory activities
        if ($activityType == 'all' || $activityType == 'laboratory') {
            $labActivities = \App\Models\LaboratoryReservation::with(['user', 'laboratory'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($reservation) {
                    $statusClass = '';
                    
                    switch($reservation->status) {
                        case \App\Models\LaboratoryReservation::STATUS_PENDING:
                            $statusClass = 'yellow';
                            break;
                        case \App\Models\LaboratoryReservation::STATUS_APPROVED:
                            $statusClass = 'green';
                            break;
                        case \App\Models\LaboratoryReservation::STATUS_REJECTED:
                            $statusClass = 'red';
                            break;
                        case \App\Models\LaboratoryReservation::STATUS_CANCELLED:
                            $statusClass = 'gray';
                            break;
                    }
                    
                    return (object)[
                        'id' => $reservation->id,
                        'created_at' => $reservation->created_at,
                        'description' => "Laboratory " . 
                                        ($reservation->status === \App\Models\LaboratoryReservation::STATUS_PENDING ? "Reservation Request" : 
                                        ($reservation->status === \App\Models\LaboratoryReservation::STATUS_APPROVED ? "Reservation Approved" : 
                                        ($reservation->status === \App\Models\LaboratoryReservation::STATUS_REJECTED ? "Reservation Rejected" :
                                        "Reservation Cancelled"))),
                        'notes' => $reservation->purpose,
                        'user_name' => $reservation->user ? $reservation->user->name : 'Unknown User',
                        'item_name' => $reservation->laboratory ? $reservation->laboratory->name : 'Unknown Laboratory',
                        'status' => $reservation->status,
                        'status_class' => $statusClass,
                        'activity_type' => 'laboratory'
                    ];
                });
            
            if ($activityType == 'all') {
                $recentActivities = $recentActivities->concat($labActivities);
            } else {
                $recentActivities = $labActivities;
            }
        }
        
        // Sort activities if we have multiple types and limit to 10
        if ($activityType == 'all') {
            $recentActivities = $recentActivities->sortByDesc('created_at')->take(10)->values();
        }

        return view('admin.dashboard', compact(
            'activityType',
            'totalEquipment',
            'borrowedEquipment',
            'pendingRequests',
            'todayBookings',
            'pendingReservations',
            'activeClasses',
            'totalUsers',
            'activeUsers',
            'newUsers',
            'recentActivities'
        ));
    }
} 