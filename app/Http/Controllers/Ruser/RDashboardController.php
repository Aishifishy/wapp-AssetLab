<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Services\UserEquipmentService;
use App\Services\UserLaboratoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Get equipment request statistics
        $equipmentStats = $this->equipmentService->getUserStats($user->id);
        $pendingRequests = $equipmentStats['pending_requests'];
        $currentlyBorrowed = $equipmentStats['currently_borrowed'];
        $upcomingReturns = $equipmentStats['upcoming_returns'];
        
        // Get available laboratories count
        $availableLabs = $this->laboratoryService->getAvailableLabsCount();

        // Get recent activities based on filter
        $recentActivities = $this->getFilteredActivities($user->id, $activityType);

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
     * Get filtered activities based on type
     */
    private function getFilteredActivities($userId, $activityType)
    {
        $recentActivities = collect();
        
        if ($activityType == 'all' || $activityType == 'equipment') {
            $equipmentActivities = $this->equipmentService->getRecentActivities(
                $userId, 
                $activityType == 'equipment' ? 10 : 15
            );
            
            if ($activityType == 'equipment') {
                $recentActivities = $equipmentActivities;
            } else {
                $recentActivities = $recentActivities->merge($equipmentActivities);
            }
        }
        
        if ($activityType == 'all' || $activityType == 'laboratory') {
            $laboratoryActivities = $this->laboratoryService->getRecentActivities(
                $userId, 
                $activityType == 'laboratory' ? 10 : 15
            );
            
            if ($activityType == 'laboratory') {
                $recentActivities = $laboratoryActivities;
            } else {
                $recentActivities = $recentActivities->merge($laboratoryActivities);
            }
        }
        
        // Sort all activities by time and limit to 10 for 'all' view
        if ($activityType == 'all') {
            $recentActivities = $recentActivities->sortByDesc('time')->take(10)->values();
        }

        return $recentActivities;
    }
}
