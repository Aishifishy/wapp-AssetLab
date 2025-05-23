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
     */
    public function index()
    {
        $user = Auth::user();

        // Get equipment request statistics
        $pendingRequests = EquipmentRequest::where('user_id', $user->id)
            ->where('status', EquipmentRequest::STATUS_PENDING)
            ->count();
            
        $currentlyBorrowed = EquipmentRequest::where('user_id', $user->id)
            ->where('status', EquipmentRequest::STATUS_APPROVED)
            ->whereNull('returned_at')
            ->count();
            
        // Get available equipment count
        $availableEquipment = Equipment::where('status', 'available')->count();
        
        // Get available laboratories
        $availableLabs = ComputerLaboratory::where('status', 'available')->count();

        // Get recent activities - showing latest 10 actions
        $recentActivities = EquipmentRequest::where('user_id', $user->id)
            ->with(['equipment'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($request) {
                $activityText = '';
                $statusClass = '';
                
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
                    'purpose' => $request->purpose
                ];
            });

        return view('ruser.dashboard', compact(
            'pendingRequests',
            'currentlyBorrowed',
            'availableEquipment',
            'availableLabs',
            'recentActivities'
        ));
    }
}
