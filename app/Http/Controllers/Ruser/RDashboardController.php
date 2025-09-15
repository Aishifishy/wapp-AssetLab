<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Services\UserEquipmentService;
use App\Services\UserLaboratoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
