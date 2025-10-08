<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Services\UserEquipmentService;
use App\Services\UserLaboratoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class RDashboardController extends Controller
{
    protected $equipmentService;
    protected $laboratoryService;

    public function __construct(UserEquipmentService $equipmentService, UserLaboratoryService $laboratoryService)
    {
        $this->equipmentService = $equipmentService;
        $this->laboratoryService = $laboratoryService;
    }

    /**
     * Display the user's dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activityType = $request->input('activity_type', 'all');
        $perPage = (int) $request->input('per_page', 5);
        
        // Validate per_page parameter
        if (!in_array($perPage, [5, 10, 15, 20])) {
            $perPage = 5;
        }

        // Get equipment request statistics
        $equipmentStats = $this->equipmentService->getUserStats($user->id);
        $pendingRequests = $equipmentStats['pending_requests'];
        $currentlyBorrowed = $equipmentStats['currently_borrowed'];
        $upcomingReturns = $equipmentStats['upcoming_returns'];
        
        // Get available laboratories count
        $availableLabs = $this->laboratoryService->getAvailableLabsCount();

        // Get recent activities based on filter with pagination
        $recentActivities = $this->getFilteredActivities($user->id, $activityType, $request->input('page', 1), $perPage);

        return view('ruser.dashboard', compact(
            'pendingRequests',
            'currentlyBorrowed',
            'upcomingReturns',
            'availableLabs',
            'recentActivities',
            'activityType'
        ));
    }

    /**
     * Get filtered activities based on type with pagination
     */
    private function getFilteredActivities($userId, $activityType, $page = 1, $perPage = 5)
    {
        // First, collect all activities without pagination to get total count
        $allActivities = collect();
        
        if ($activityType == 'all' || $activityType == 'equipment') {
            // Get a larger number to ensure we have enough for pagination
            $equipmentActivities = $this->equipmentService->getRecentActivities($userId, 100);
            
            if ($activityType == 'equipment') {
                $allActivities = $equipmentActivities;
            } else {
                $allActivities = $allActivities->merge($equipmentActivities);
            }
        }
        
        if ($activityType == 'all' || $activityType == 'laboratory') {
            // Get a larger number to ensure we have enough for pagination
            $laboratoryActivities = $this->laboratoryService->getRecentActivities($userId, 100);
            
            if ($activityType == 'laboratory') {
                $allActivities = $laboratoryActivities;
            } else {
                $allActivities = $allActivities->merge($laboratoryActivities);
            }
        }
        
        // Sort all activities by time
        $allActivities = $allActivities->sortByDesc('time')->values();
        
        // Create manual pagination
        $total = $allActivities->count();
        $currentPage = max(1, (int) $page);
        $offset = ($currentPage - 1) * $perPage;
        $items = $allActivities->slice($offset, $perPage)->values();
        
        // Create a paginator-like object
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
        
        // Append current query parameters to pagination links
        $paginator->appends(request()->query());
        
        return $paginator;
    }

    /**
     * Get live status updates for user dashboard (for AJAX polling)
     */
    public function getLiveStatus(Request $request)
    {
        try {
            $user = Auth::user();
            $lastUpdate = $request->get('last_update');
            
            // Get current user statistics
            $equipmentStats = $this->equipmentService->getUserStats($user->id);
            
            $stats = [
                'equipment' => [
                    'pending_requests' => $equipmentStats['pending_requests'],
                    'currently_borrowed' => $equipmentStats['currently_borrowed'],
                    'upcoming_returns' => $equipmentStats['upcoming_returns'],
                    'total_requests' => $equipmentStats['total_requests'],
                ],
                'laboratory' => [
                    'available_labs' => $this->laboratoryService->getAvailableLabsCount(),
                    'user_reservations' => \App\Models\LaboratoryReservation::where('user_id', $user->id)
                        ->whereIn('status', ['pending', 'approved'])
                        ->count(),
                    'upcoming_reservations' => \App\Models\LaboratoryReservation::where('user_id', $user->id)
                        ->where('status', 'approved')
                        ->where('reservation_date', '>=', today())
                        ->count(),
                ]
            ];
            
            // Get recent activity if requested
            $recentActivity = null;
            if ($lastUpdate) {
                $recentEquipmentRequests = \App\Models\EquipmentRequest::where('user_id', $user->id)
                    ->where('updated_at', '>', $lastUpdate)
                    ->count();
                    
                $recentLaboratoryReservations = \App\Models\LaboratoryReservation::where('user_id', $user->id)
                    ->where('updated_at', '>', $lastUpdate)
                    ->count();
                
                $recentActivity = [
                    'equipment_requests' => $recentEquipmentRequests,
                    'laboratory_reservations' => $recentLaboratoryReservations,
                    'total' => $recentEquipmentRequests + $recentLaboratoryReservations
                ];
            }
            
            // Check for status changes (approvals, rejections, etc.)
            $notifications = [];
            if ($lastUpdate) {
                // Check for recently approved equipment requests
                $approvedRequests = $user->equipmentRequests()
                    ->where('status', 'approved')
                    ->where('updated_at', '>', $lastUpdate)
                    ->count();
                
                if ($approvedRequests > 0) {
                    $notifications[] = [
                        'type' => 'equipment_approved',
                        'count' => $approvedRequests,
                        'message' => "You have {$approvedRequests} newly approved equipment request(s)!"
                    ];
                }
                
                // Check for recently rejected equipment requests
                $rejectedRequests = $user->equipmentRequests()
                    ->where('status', 'rejected')
                    ->where('updated_at', '>', $lastUpdate)
                    ->count();
                
                if ($rejectedRequests > 0) {
                    $notifications[] = [
                        'type' => 'equipment_rejected',
                        'count' => $rejectedRequests,
                        'message' => "You have {$rejectedRequests} equipment request(s) that need attention."
                    ];
                }
                
                // Check for approved laboratory reservations
                $approvedReservations = $user->laboratoryReservations()
                    ->where('status', 'approved')
                    ->where('updated_at', '>', $lastUpdate)
                    ->count();
                
                if ($approvedReservations > 0) {
                    $notifications[] = [
                        'type' => 'laboratory_approved',
                        'count' => $approvedReservations,
                        'message' => "You have {$approvedReservations} newly approved laboratory reservation(s)!"
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'recent_activity' => $recentActivity,
                'notifications' => $notifications,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in user dashboard live status: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch live updates'
            ], 500);
        }
    }
}
