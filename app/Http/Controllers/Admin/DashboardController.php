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
    public function index()
    {
        // Log dashboard access
        $admin = Auth::guard('admin')->user();
        Log::info('Admin accessing dashboard', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email
        ]);

        // Get equipment statistics
        $totalEquipment = 0; // Equipment::count();
        $borrowedEquipment = 0; // Equipment::where('status', 'borrowed')->count();
        $pendingRequests = 0; // EquipmentRequest::where('status', 'pending')->count();

        // Get laboratory statistics
        $todayBookings = 0;
        $pendingReservations = 0;
        $activeClasses = 0;

        // Get user statistics
        $totalUsers = Ruser::count();
        $activeUsers = 0; // Implement active users logic
        $newUsers = Ruser::where('created_at', '>=', now()->subWeek())->count();

        // Get recent activities
        $recentActivities = collect([]); // Implement activity logging

        return view('admin.dashboard', compact(
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