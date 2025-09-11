<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Radmin;
use App\Models\Ruser;
use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\ComputerLaboratory;
use App\Models\LaboratoryReservation;
use App\Models\AcademicTerm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        // Super admin access will be handled by middleware in routes and method-level checks
    }

    /**
     * Check if the current admin has super admin privileges
     */
    private function checkSuperAdminAccess()
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin || !$admin->isSuperAdmin()) {
            abort(403, 'Access denied. Super admin privileges required.');
        }
        return $admin;
    }

    /**
     * Display the user management dashboard.
     */
    public function index(Request $request)
    {
        $this->checkSuperAdminAccess();
        
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50, 100]) ? $perPage : 10;
        
        $admins = Radmin::latest()->paginate($perPage);
        $users = Ruser::latest()->paginate($perPage);
        
        $stats = [
            'total_admins' => Radmin::count(),
            'total_users' => Ruser::count(),
            'super_admins' => Radmin::where('is_super_admin', true)->count(),
            'regular_admins' => Radmin::where('role', 'admin')->count(),
            'faculty_users' => Ruser::where('role', 'faculty')->count(),
            'student_users' => Ruser::where('role', 'student')->count(),
            'staff_users' => Ruser::where('role', 'staff')->count(),
        ];

        return view('admin.super-admin.index', compact('admins', 'users', 'stats'));
    }    /**
     * Show the form for creating a new admin.
     */
    public function createAdmin()
    {
        $this->checkSuperAdminAccess();
        return view('admin.super-admin.create-admin');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function storeAdmin(Request $request)
    {
        $this->checkSuperAdminAccess();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:radmins',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin',
        ]);

        $admin = Radmin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_super_admin' => $request->role === 'super_admin',
        ]);

        return redirect()->route('admin.super-admin.index')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * Display the specified admin.
     */
    public function showAdmin(Radmin $admin)
    {
        $this->checkSuperAdminAccess();
        return view('admin.super-admin.show-admin', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function editAdmin(Radmin $admin)
    {
        $this->checkSuperAdminAccess();
        
        // Prevent editing of the current super admin's own account
        if ($admin->id === auth()->guard('admin')->id() && $admin->isSuperAdmin()) {
            return redirect()->back()->with('error', 'You cannot edit your own super admin account.');
        }

        return view('admin.super-admin.edit-admin', compact('admin'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function updateAdmin(Request $request, Radmin $admin)
    {
        $this->checkSuperAdminAccess();
        
        // Prevent editing of the current super admin's own account
        if ($admin->id === auth()->guard('admin')->id() && $admin->isSuperAdmin()) {
            return redirect()->back()->with('error', 'You cannot edit your own super admin account.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('radmins')->ignore($admin)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin',
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_super_admin' => $request->role === 'super_admin',
        ]);

        if ($request->password) {
            $admin->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.super-admin.index')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroyAdmin(Radmin $admin)
    {
        $this->checkSuperAdminAccess();
        
        // Prevent deletion of the current super admin's own account
        if ($admin->id === auth()->guard('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deletion if it's the last super admin
        if ($admin->isSuperAdmin() && Radmin::where('is_super_admin', true)->count() <= 1) {
            return redirect()->back()->with('error', 'Cannot delete the last super admin account.');
        }

        $admin->delete();

        return redirect()->route('admin.super-admin.index')
            ->with('success', 'Admin deleted successfully.');
    }

    /**
     * Show the form for creating a new user.
     */
    public function createUser()
    {
        $this->checkSuperAdminAccess();
        return view('admin.super-admin.create-user');
    }

    /**
     * Store a newly created user in storage.
     */
    public function storeUser(Request $request)
    {
        $this->checkSuperAdminAccess();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:rusers',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,faculty,staff',
            'department' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'rfid_tag' => 'nullable|string|max:50|unique:rusers',
        ]);

        $user = Ruser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
            'contact_number' => $request->contact_number,
            'rfid_tag' => $request->rfid_tag,
        ]);

        return redirect()->route('admin.super-admin.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function showUser(Ruser $user)
    {
        $this->checkSuperAdminAccess();
        $user->load(['equipmentRequests', 'laboratoryReservations']);
        return view('admin.super-admin.show-user', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function editUser(Ruser $user)
    {
        $this->checkSuperAdminAccess();
        return view('admin.super-admin.edit-user', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function updateUser(Request $request, Ruser $user)
    {
        $this->checkSuperAdminAccess();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('rusers')->ignore($user)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:student,faculty,staff',
            'department' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'rfid_tag' => ['nullable', 'string', 'max:50', Rule::unique('rusers')->ignore($user)],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department' => $request->department,
            'contact_number' => $request->contact_number,
            'rfid_tag' => $request->rfid_tag,
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.super-admin.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroyUser(Ruser $user)
    {
        $this->checkSuperAdminAccess();
        
        // Check if user has pending requests or reservations
        $pendingRequests = $user->equipmentRequests()->where('status', 'pending')->count();
        $pendingReservations = $user->laboratoryReservations()->where('status', 'pending')->count();

        if ($pendingRequests > 0 || $pendingReservations > 0) {
            return redirect()->back()->with('error', 'Cannot delete user with pending requests or reservations.');
        }

        $user->delete();

        return redirect()->route('admin.super-admin.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle admin status (for debugging purposes - super admin only).
     */
    public function toggleAdminStatus(Radmin $admin)
    {
        $this->checkSuperAdminAccess();
        
        if ($admin->id === auth()->guard('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot modify your own status.');
        }

        if ($admin->isSuperAdmin() && Radmin::where('is_super_admin', true)->count() <= 1) {
            return redirect()->back()->with('error', 'Cannot remove super admin status from the last super admin.');
        }

        $admin->update([
            'is_super_admin' => !$admin->is_super_admin,
            'role' => $admin->is_super_admin ? 'admin' : 'super_admin'
        ]);

        return redirect()->back()->with('success', 'Admin status updated successfully.');
    }

    /**
     * Display system reports and statistics.
     */
    public function systemReports(Request $request)
    {
        $this->checkSuperAdminAccess();

        // Get filter parameters
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $period = $request->get('period', 'month'); // month, term, year, custom
        $academicTermId = $request->get('academic_term_id');

        // Date range based on period
        switch ($period) {
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            case 'term':
                if ($academicTermId) {
                    $term = \App\Models\AcademicTerm::find($academicTermId);
                    $startDate = $term ? $term->start_date : now()->startOfMonth();
                    $endDate = $term ? $term->end_date : now()->endOfMonth();
                } else {
                    $currentTerm = \App\Models\AcademicTerm::where('is_current', true)->first();
                    $startDate = $currentTerm ? $currentTerm->start_date : now()->startOfMonth();
                    $endDate = $currentTerm ? $currentTerm->end_date : now()->endOfMonth();
                }
                break;
            case 'custom':
                $startDate = \Carbon\Carbon::parse($dateFrom);
                $endDate = \Carbon\Carbon::parse($dateTo);
                break;
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
        }

        // User activity statistics
        $userStats = [
            'total_users' => Ruser::count(),
            'active_users_period' => Ruser::whereBetween('updated_at', [$startDate, $endDate])->count(),
            'new_users_period' => Ruser::whereBetween('created_at', [$startDate, $endDate])->count(),
            'faculty_count' => Ruser::where('role', 'faculty')->count(),
            'student_count' => Ruser::where('role', 'student')->count(),
            'staff_count' => Ruser::where('role', 'staff')->count(),
            'faculty_new_period' => Ruser::where('role', 'faculty')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'student_new_period' => Ruser::where('role', 'student')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'staff_new_period' => Ruser::where('role', 'staff')->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Admin activity statistics
        $adminStats = [
            'total_admins' => Radmin::count(),
            'super_admins' => Radmin::where('is_super_admin', true)->count(),
            'regular_admins' => Radmin::where('role', 'admin')->count(),
            'new_admins_period' => Radmin::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Equipment statistics
        $equipmentStats = [
            'total_equipment' => Equipment::count(),
            'available_equipment' => Equipment::where('status', 'available')->count(),
            'borrowed_equipment' => Equipment::where('status', 'borrowed')->count(),
            'maintenance_equipment' => Equipment::where('status', 'maintenance')->count(),
            'total_requests' => EquipmentRequest::count(),
            'pending_requests' => EquipmentRequest::where('status', 'pending')->count(),
            'requests_period' => EquipmentRequest::whereBetween('created_at', [$startDate, $endDate])->count(),
            'approved_requests_period' => EquipmentRequest::where('status', 'approved')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'rejected_requests_period' => EquipmentRequest::where('status', 'rejected')->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Laboratory statistics
        $labStats = [
            'total_laboratories' => ComputerLaboratory::count(),
            'available_laboratories' => ComputerLaboratory::where('status', 'available')->count(),
            'total_reservations' => LaboratoryReservation::count(),
            'pending_reservations' => LaboratoryReservation::where('status', 'pending')->count(),
            'reservations_period' => LaboratoryReservation::whereBetween('created_at', [$startDate, $endDate])->count(),
            'approved_reservations_period' => LaboratoryReservation::where('status', 'approved')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_reservations_period' => LaboratoryReservation::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Monthly trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $monthlyTrends[] = [
                'month' => $monthStart->format('M Y'),
                'new_users' => Ruser::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'equipment_requests' => EquipmentRequest::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'lab_reservations' => LaboratoryReservation::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        }

        // Get academic terms for filter dropdown
        $academicTerms = \App\Models\AcademicTerm::with('academicYear')->orderBy('start_date', 'desc')->get();

        // Popular equipment (most requested)
        $popularEquipment = Equipment::withCount(['borrowRequests' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->orderBy('borrow_requests_count', 'desc')
        ->limit(5)
        ->get();

        // Popular laboratories (most reserved)
        $popularLabs = ComputerLaboratory::withCount(['reservations' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->orderBy('reservations_count', 'desc')
        ->limit(5)
        ->get();

        return view('admin.super-admin.reports', compact(
            'userStats', 
            'adminStats', 
            'equipmentStats', 
            'labStats',
            'monthlyTrends',
            'academicTerms',
            'popularEquipment',
            'popularLabs',
            'period',
            'dateFrom',
            'dateTo',
            'academicTermId',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export user data to CSV.
     */
    public function exportUsers()
    {
        $this->checkSuperAdminAccess();

        $users = Ruser::with(['equipmentRequests', 'laboratoryReservations'])->get();
        
        $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Role', 'Department', 'Contact Number', 
                'RFID Tag', 'Equipment Requests', 'Lab Reservations', 'Created At'
            ]);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->department,
                    $user->contact_number,
                    $user->rfid_tag,
                    $user->equipmentRequests->count(),
                    $user->laboratoryReservations->count(),
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk delete users.
     */
    public function bulkDeleteUsers(Request $request)
    {
        $this->checkSuperAdminAccess();

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:rusers,id',
        ]);

        $users = Ruser::whereIn('id', $request->user_ids)->get();
        $deletedCount = 0;
        $errors = [];

        foreach ($users as $user) {
            // Check if user has pending requests or reservations
            $pendingRequests = $user->equipmentRequests()->where('status', 'pending')->count();
            $pendingReservations = $user->laboratoryReservations()->where('status', 'pending')->count();

            if ($pendingRequests > 0 || $pendingReservations > 0) {
                $errors[] = "Cannot delete {$user->name} - has pending requests or reservations";
                continue;
            }

            $user->delete();
            $deletedCount++;
        }

        $message = "Successfully deleted {$deletedCount} users.";
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(', ', $errors);
        }

        return redirect()->back()->with('success', $message);
    }
}
