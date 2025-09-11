<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use App\Http\Controllers\Traits\CrudOperations;
use App\Models\Ruser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    use ControllerHelpers, CrudOperations;

    protected function getRoutePrefix(): string
    {
        return 'admin.users';
    }

    protected function getViewPrefix(): string
    {
        return 'admin.users';
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:rusers'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:student,faculty,staff'],
            'department' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'rfid_tag' => ['nullable', 'string', 'max:255', 'unique:rusers'],
        ];
    }

    protected function getUpdateValidationRules($model): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:rusers,email,' . $model->id],
            'role' => ['required', 'string', 'in:student,faculty,staff'],
            'department' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'rfid_tag' => ['nullable', 'string', 'max:255', 'unique:rusers,rfid_tag,' . $model->id],
        ];
    }

    protected function canDelete($model): array
    {
        // Check if user has active equipment requests
        $activeRequests = $model->equipmentRequests()
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->count();

        if ($activeRequests > 0) {
            return [
                'can_delete' => false,
                'message' => 'Cannot delete user with active equipment requests. Please ensure all equipment is returned first.'
            ];
        }

        // Check if user has pending requests
        $pendingRequests = $model->equipmentRequests()
            ->whereIn('status', ['pending'])
            ->count();

        if ($pendingRequests > 0) {
            return [
                'can_delete' => false,
                'message' => 'Cannot delete user with pending requests. Please handle these first.'
            ];
        }

        return ['can_delete' => true, 'message' => ''];
    }

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

        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50, 100]) ? $perPage : 10;

        $users = $query->withCount(['equipmentRequests'])
                      ->orderBy('created_at', 'desc')
                      ->paginate($perPage);

        // Get distinct departments and roles for filters
        $departments = Ruser::distinct()->pluck('department')->filter()->sort();
        $roles = ['student', 'faculty', 'staff'];

        return view($this->getViewPrefix() . '.index', compact('users', 'departments', 'roles'));
    }

    public function create()
    {
        return view($this->getViewPrefix() . '.create');
    }

    public function store(Request $request)
    {
        return $this->handleStore($request, Ruser::class);
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

        return view($this->getViewPrefix() . '.show', compact('user', 'stats'));
    }

    public function edit(Ruser $user)
    {
        return view($this->getViewPrefix() . '.edit', compact('user'));
    }

    public function update(Request $request, Ruser $user)
    {
        return $this->handleUpdate($request, $user);
    }

    public function destroy(Ruser $user)
    {
        return $this->handleDestroy($user);
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
        $validated = $this->validateRequest($request, [
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