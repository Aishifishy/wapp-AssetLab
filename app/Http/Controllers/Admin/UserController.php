<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request)
    {
        $query = Ruser::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('department', 'LIKE', "%{$search}%")
                  ->orWhere('rfid_tag', 'LIKE', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $request->get('role') !== 'all') {
            $query->where('role', $request->get('role'));
        }

        // Filter by department
        if ($request->filled('department') && $request->get('department') !== 'all') {
            $query->where('department', $request->get('department'));
        }

        $users = $query->withCount(['equipmentRequests'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

        // Get distinct departments and roles for filters
        $departments = Ruser::distinct()->pluck('department')->filter()->sort();
        $roles = ['student', 'faculty', 'staff'];

        return view('admin.users.index', compact('users', 'departments', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:rusers'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:student,faculty,staff'],
            'department' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'rfid_tag' => ['nullable', 'string', 'max:255', 'unique:rusers'],
        ]);

        Ruser::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(Ruser $user)
    {
        $user->load([
            'equipmentRequests' => function($query) {
                $query->with('equipment')->latest()->take(10);
            }
        ]);

        // Get user statistics
        $stats = [
            'total_equipment_requests' => $user->equipmentRequests()->count(),
            'approved_equipment_requests' => $user->equipmentRequests()->where('status', 'approved')->count(),
            'currently_borrowed' => $user->equipmentRequests()
                ->where('status', 'approved')
                ->whereNull('returned_at')
                ->count(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(Ruser $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, Ruser $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:rusers,email,' . $user->id],
            'role' => ['required', 'string', 'in:student,faculty,staff'],
            'department' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'rfid_tag' => ['nullable', 'string', 'max:255', 'unique:rusers,rfid_tag,' . $user->id],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Ruser $user)
    {
        // Check if user has active equipment requests
        $activeRequests = $user->equipmentRequests()
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->count();

        if ($activeRequests > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user with active equipment requests. Please ensure all equipment is returned first.');
        }

        // Check if user has pending requests
        $pendingRequests = $user->equipmentRequests()
            ->whereIn('status', ['pending'])
            ->count();

        if ($pendingRequests > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user with pending requests. Please handle these first.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Show the form for resetting user password.
     */
    public function showResetPasswordForm(Ruser $user)
    {
        return view('admin.users.reset-password', compact('user'));
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, Ruser $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => $validated['password']
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Password reset successfully.');
    }

    /**
     * Find user by RFID tag (for AJAX requests)
     */
    public function findByRfid(Request $request)
    {
        $rfidTag = $request->input('rfid_tag');
        
        if (!$rfidTag) {
            return response()->json(['error' => 'RFID tag is required'], 400);
        }

        $user = Ruser::where('rfid_tag', $rfidTag)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found with this RFID tag'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'department' => $user->department,
            'role' => $user->role,
            'rfid_tag' => $user->rfid_tag
        ]);
    }
}
