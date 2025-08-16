<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use App\Services\EquipmentService;
use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EquipmentController extends Controller
{
    use ControllerHelpers;

    protected $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }
    public function index(Request $request)
    {
        $equipment = $this->equipmentService->getEquipmentIndex($request);
        return view('admin.equipment.index', compact('equipment'));
    }

    public function manage(Request $request)
    {
        $data = $this->equipmentService->getEquipmentManage($request);
        return view('admin.equipment.manage', $data);
    }

    public function create()
    {
        $categories = EquipmentCategory::all();
        return view('admin.equipment.create', compact('categories'));
    }

    public function borrowRequests()
    {
        $data = $this->equipmentService->getBorrowRequests();
        
        return view('admin.equipment.borrow-requests', [
            'requests' => $data['requests'],
            'pendingCount' => $data['statistics']['pending'],
            'activeCount' => $data['statistics']['active'],
            'overdueCount' => $data['statistics']['overdue'],
            'availableEquipment' => $data['availableEquipment'],
            'users' => $data['users']
        ]);
    }

    // History method has been removed

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rfid_tag' => 'nullable|string|unique:equipment,rfid_tag',
            'category_id' => 'required|exists:equipment_categories,id',
            'status' => 'required|in:available,unavailable',
        ]);

        $this->equipmentService->createEquipment($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment added successfully.');
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $this->validateRequest($request, [
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

        $this->equipmentService->updateEquipment($equipment, $validated);

        return redirect()->back()
            ->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        $result = $this->equipmentService->deleteEquipment($equipment);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('admin.equipment.index')
            ->with('success', $result['message']);
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
        $result = $this->equipmentService->approveRequest($request);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function markAsReturned(EquipmentRequest $request, Request $validatedRequest)
    {
        $validatedData = $this->validateRequest($validatedRequest, [
            'condition' => 'required|in:good,damaged,needs_repair',
            'notes' => 'nullable|string|max:1000',
        ]);

        $result = $this->equipmentService->returnEquipment($request, $validatedData);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
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
        $result = $this->equipmentService->findByRfid($rfidTag);

        if (!$result['success']) {
            return response()->json(['error' => $result['message']], $result['success'] ? 200 : 404);
        }

        return response()->json($result['data']);
    }
}