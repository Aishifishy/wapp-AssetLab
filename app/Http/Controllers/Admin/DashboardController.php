<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Ruser;
use App\Services\EquipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

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

        // Get auto-rejection statistics
        $autoRejectionStats = $this->equipmentService->getAutoRejectionStats();

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
            $equipmentActivities = \App\Models\EquipmentRequest::with(['user', 'equipment', 'approvedBy', 'rejectedBy', 'checkedOutBy'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($request) {
                    $statusClass = '';
                    $description = '';
                    $adminInfo = '';
                    
                    switch($request->status) {
                        case \App\Models\EquipmentRequest::STATUS_PENDING:
                            $statusClass = 'yellow';
                            $description = "Equipment borrow request submitted";
                            break;
                        case \App\Models\EquipmentRequest::STATUS_APPROVED:
                            if ($request->returned_at) {
                                $statusClass = 'green';
                                $description = "Equipment returned successfully";
                            } elseif ($request->checked_out_at) {
                                $statusClass = 'blue';
                                $description = "Equipment checked out to user";
                                if ($request->checkedOutBy) {
                                    $adminInfo = "Checked out by: " . $request->checkedOutBy->name;
                                }
                            } else {
                                $statusClass = 'blue';
                                $description = "Equipment borrow request approved";
                                if ($request->approvedBy) {
                                    $adminInfo = "Approved by: " . $request->approvedBy->name;
                                }
                            }
                            break;
                        case \App\Models\EquipmentRequest::STATUS_REJECTED:
                            $statusClass = 'red';
                            $description = "Equipment borrow request rejected";
                            if ($request->rejectedBy) {
                                $adminInfo = "Rejected by: " . $request->rejectedBy->name;
                            }
                            break;
                        case \App\Models\EquipmentRequest::STATUS_RETURNED:
                            $statusClass = 'green';
                            $description = "Equipment marked as returned";
                            break;
                        case \App\Models\EquipmentRequest::STATUS_CHECKED_OUT:
                            $statusClass = 'blue';
                            $description = "Equipment checked out";
                            if ($request->checkedOutBy) {
                                $adminInfo = "Checked out by: " . $request->checkedOutBy->name;
                            }
                            break;
                        default:
                            $statusClass = 'gray';
                            $description = "Equipment request status updated";
                            break;
                    }
                    
                    return (object)[
                        'id' => $request->id,
                        'created_at' => $request->created_at,
                        'description' => $description,
                        'notes' => $request->purpose,
                        'user_name' => $request->user ? $request->user->name : 'Unknown User',
                        'item_name' => $request->equipment ? $request->equipment->name : 'Unknown Equipment',
                        'status' => $request->status,
                        'status_class' => $statusClass,
                        'activity_type' => 'equipment',
                        'admin_info' => $adminInfo,
                        'view_url' => route('admin.equipment.borrow-requests')
                    ];
                });
            
            $recentActivities = $equipmentActivities;
        }
        
        // Get laboratory activities
        if ($activityType == 'all' || $activityType == 'laboratory') {
            $labActivities = \App\Models\LaboratoryReservation::with(['user', 'laboratory', 'approvedBy', 'rejectedBy'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($reservation) {
                    $statusClass = '';
                    $description = '';
                    $adminInfo = '';
                    
                    switch($reservation->status) {
                        case \App\Models\LaboratoryReservation::STATUS_PENDING:
                            $statusClass = 'yellow';
                            $description = "Laboratory reservation request submitted";
                            break;
                        case \App\Models\LaboratoryReservation::STATUS_APPROVED:
                            $statusClass = 'green';
                            $description = "Laboratory reservation approved";
                            if ($reservation->approvedBy) {
                                $adminInfo = "Approved by: " . $reservation->approvedBy->name;
                            }
                            break;
                        case \App\Models\LaboratoryReservation::STATUS_REJECTED:
                            $statusClass = 'red';
                            $description = "Laboratory reservation request rejected";
                            if ($reservation->rejectedBy) {
                                $adminInfo = "Rejected by: " . $reservation->rejectedBy->name;
                            }
                            break;
                        case \App\Models\LaboratoryReservation::STATUS_CANCELLED:
                            $statusClass = 'gray';
                            $description = "Laboratory reservation cancelled";
                            break;
                        default:
                            $statusClass = 'gray';
                            $description = "Laboratory reservation updated";
                            break;
                    }
                    
                    return (object)[
                        'id' => $reservation->id,
                        'created_at' => $reservation->created_at,
                        'description' => $description,
                        'notes' => $reservation->purpose,
                        'user_name' => $reservation->user ? $reservation->user->name : 'Unknown User',
                        'item_name' => $reservation->laboratory ? $reservation->laboratory->name : 'Unknown Laboratory',
                        'status' => $reservation->status,
                        'status_class' => $statusClass,
                        'activity_type' => 'laboratory',
                        'admin_info' => $adminInfo,
                        'view_url' => route('admin.laboratory.reservations')
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
            'autoRejectionStats',
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