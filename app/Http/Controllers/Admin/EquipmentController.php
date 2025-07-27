<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('currentBorrower');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Search by name, description, or category
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }
        
        $equipment = $query->latest()->paginate(10);
        
        // Keep the filters when paginating
        $equipment->appends([
            'status' => $request->status,
            'search' => $request->search
        ]);
            
        return view('admin.equipment.index', compact('equipment'));
    }

    public function manage(Request $request)
    {
        $query = Equipment::with(['currentBorrower', 'borrowRequests', 'category']);
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Search by name, description, category, or RFID tag
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('rfid_tag', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        $equipment = $query->latest()->get();
        $categories = EquipmentCategory::all();
        return view('admin.equipment.manage', compact('equipment', 'categories'));
    }

    public function create()
    {
        $categories = EquipmentCategory::all();
        return view('admin.equipment.create', compact('categories'));
    }

    public function borrowRequests()
    {
        $requests = EquipmentRequest::with(['user', 'equipment'])
            ->latest()
            ->paginate(15);

        $pendingCount = EquipmentRequest::where('status', 'pending')->count();
        $activeCount = EquipmentRequest::where('status', 'approved')
            ->whereNull('returned_at')
            ->count();
        $overdueCount = EquipmentRequest::where('status', 'approved')
            ->whereNull('returned_at')
            ->where('requested_until', '<', Carbon::now())
            ->count();

        $availableEquipment = Equipment::available()->with('category')->get();
        $users = \App\Models\Ruser::all();

        return view('admin.equipment.borrow-requests', compact(
            'requests',
            'pendingCount',
            'activeCount',
            'overdueCount',
            'availableEquipment',
            'users'
        ));
    }

    // History method has been removed

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rfid_tag' => 'nullable|string|unique:equipment,rfid_tag',
            'category_id' => 'required|exists:equipment_categories,id',
            'status' => 'required|in:available,unavailable',
        ]);

        Equipment::create($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment added successfully.');
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rfid_tag' => 'nullable|string|unique:equipment,rfid_tag,' . $equipment->id,
            'category' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:' . implode(',', [
                Equipment::STATUS_AVAILABLE,
                Equipment::STATUS_BORROWED,
                Equipment::STATUS_UNAVAILABLE,
            ]),
        ]);

        $equipment->update($validated);

        return redirect()->back()
            ->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        if ($equipment->status === Equipment::STATUS_BORROWED) {
            return redirect()->back()
                ->with('error', 'Cannot delete equipment that is currently borrowed.');
        }

        $equipment->delete();

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment deleted successfully.');
    }

    public function updateRfid(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'rfid_tag' => 'required|string|unique:equipment,rfid_tag,' . $equipment->id,
        ]);

        $equipment->update($validated);

        return redirect()->back()
            ->with('success', 'RFID tag updated successfully.');
    }

    public function approveRequest(EquipmentRequest $request)
    {
        if (!$request->isPending()) {
            return back()->with('error', 'This request cannot be approved.');
        }

        if ($request->equipment->status !== Equipment::STATUS_AVAILABLE) {
            return back()->with('error', 'This equipment is not available for borrowing.');
        }

        $request->update(['status' => 'approved']);
        $request->equipment->update(['status' => Equipment::STATUS_BORROWED]);

        return back()->with('success', 'Equipment request approved successfully.');
    }

    public function rejectRequest(EquipmentRequest $request)
    {
        if (!$request->isPending()) {
            return back()->with('error', 'This request cannot be rejected.');
        }

        $request->update(['status' => 'rejected']);

        return back()->with('success', 'Equipment request rejected successfully.');
    }

    public function markAsReturned(EquipmentRequest $request, Request $validatedRequest)
    {
        if (!$request->isApproved() || $request->returned_at) {
            return back()->with('error', 'This equipment cannot be marked as returned.');
        }

        $validatedData = $validatedRequest->validate([
            'condition' => 'required|in:good,damaged,needs_repair',
            'notes' => 'nullable|string|max:1000',
        ]);

        $request->update([
            'returned_at' => Carbon::now(),
            'return_condition' => $validatedData['condition'],
            'return_notes' => $validatedData['notes'],
        ]);

        $request->equipment->update([
            'status' => $validatedData['condition'] === 'good' 
                ? Equipment::STATUS_AVAILABLE 
                : Equipment::STATUS_UNAVAILABLE
        ]);

        return back()->with('success', 'Equipment marked as returned successfully.');
    }

    public function createRequest()
    {
        $equipment = Equipment::available()->get();
        $users = \App\Models\Ruser::all();
        return view('admin.equipment.create-request', compact('equipment', 'users'));
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:rusers,id',
            'equipment_id' => 'required|exists:equipment,id',
            'purpose' => 'required|string|max:1000',
            'requested_from' => 'required|date|after:now',
            'requested_until' => 'required|date|after:requested_from',
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        if (!$equipment->isAvailable()) {
            return back()->with('error', 'This equipment is no longer available.');
        }

        EquipmentRequest::create([
            'user_id' => $validated['user_id'],
            'equipment_id' => $equipment->id,
            'status' => EquipmentRequest::STATUS_PENDING,
            'purpose' => $validated['purpose'],
            'requested_from' => $validated['requested_from'],
            'requested_until' => $validated['requested_until'],
        ]);

        return redirect()->route('admin.equipment.borrow-requests')
            ->with('success', 'Equipment request created successfully.');
    }

    public function destroyRequest(EquipmentRequest $request)
    {
        if (!$request->isPending()) {
            return back()->with('error', 'Only pending requests can be deleted.');
        }

        $request->delete();

        return back()->with('success', 'Equipment request deleted successfully.');
    }

    public function createOnsiteBorrow(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:rusers,id',
            'equipment_id' => 'required|exists:equipment,id',
            'purpose' => 'required|string|max:1000',
            'requested_until' => 'required|date|after:now',
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        if (!$equipment->isAvailable()) {
            return back()->with('error', 'This equipment is no longer available.');
        }

        // Create and automatically approve the request for onsite borrowing
        $borrowRequest = EquipmentRequest::create([
            'user_id' => $validated['user_id'],
            'equipment_id' => $equipment->id,
            'status' => EquipmentRequest::STATUS_APPROVED, // Automatically approved
            'purpose' => $validated['purpose'],
            'requested_from' => now(),
            'requested_until' => $validated['requested_until'],
        ]);

        // Update equipment status
        $equipment->update([
            'status' => Equipment::STATUS_BORROWED,
            'current_borrower_id' => $validated['user_id']
        ]);

        return redirect()->route('admin.equipment.borrow-requests')
            ->with('success', 'Equipment has been borrowed successfully.');
    }

    /**
     * Find equipment by RFID tag (for AJAX requests)
     */
    public function findByRfid(Request $request)
    {
        $rfidTag = $request->input('rfid_tag');
        
        if (!$rfidTag) {
            return response()->json(['error' => 'RFID tag is required'], 400);
        }

        $equipment = Equipment::with('category')
            ->where('rfid_tag', $rfidTag)
            ->where('status', Equipment::STATUS_AVAILABLE)
            ->first();

        if (!$equipment) {
            return response()->json(['error' => 'Available equipment not found with this RFID tag'], 404);
        }

        return response()->json([
            'id' => $equipment->id,
            'name' => $equipment->name,
            'category' => $equipment->category->name ?? 'Uncategorized',
            'description' => $equipment->description,
            'status' => $equipment->status,
            'rfid_tag' => $equipment->rfid_tag
        ]);
    }
}