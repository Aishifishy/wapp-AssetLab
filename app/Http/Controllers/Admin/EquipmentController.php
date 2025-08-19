<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use App\Services\EquipmentService;
use App\Services\BarcodeService;
use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EquipmentController extends Controller
{
    use ControllerHelpers;

    protected $equipmentService;
    protected $barcodeService;

    public function __construct(EquipmentService $equipmentService, BarcodeService $barcodeService)
    {
        $this->equipmentService = $equipmentService;
        $this->barcodeService = $barcodeService;
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
            'barcode' => 'nullable|string|unique:equipment,barcode',
            'rfid_tag' => 'nullable|string|unique:equipment,rfid_tag', // Legacy support
            'category_id' => 'required|exists:equipment_categories,id',
            'status' => 'required|in:available,unavailable',
        ]);

        // Auto-generate barcode if not provided
        if (empty($validated['barcode'])) {
            $validated['barcode'] = Equipment::generateBarcode();
        }

        $this->equipmentService->createEquipment($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment added successfully.');
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $this->validateRequest($request, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|unique:equipment,barcode,' . $equipment->id,
            'rfid_tag' => 'nullable|string|unique:equipment,rfid_tag,' . $equipment->id, // Legacy support
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

    public function updateIdentificationCode(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'barcode' => 'nullable|string|unique:equipment,barcode,' . $equipment->id,
            'rfid_tag' => 'nullable|string|unique:equipment,rfid_tag,' . $equipment->id,
        ]);

        $equipment->update($validated);

        return redirect()->back()
            ->with('success', 'Identification code updated successfully.');
    }

    // Legacy method for backward compatibility
    public function updateRfid(Request $request, Equipment $equipment)
    {
        return $this->updateIdentificationCode($request, $equipment);
    }

    /**
     * Find equipment by barcode or RFID (legacy)
     */
    public function findByCode(Request $request)
    {
        $code = $request->input('code');
        $type = $request->input('type'); // Optional: specify search type
        
        if ($type) {
            switch ($type) {
                case 'barcode':
                    $result = $this->equipmentService->findByBarcode($code);
                    break;
                case 'rfid':
                    $result = $this->equipmentService->findByRfid($code);
                    break;
                default:
                    $result = $this->equipmentService->findByIdentificationCode($code);
            }
        } else {
            // Universal search across barcode and RFID
            $result = $this->equipmentService->findByIdentificationCode($code);
        }

        if (!$result['success']) {
            return response()->json(['error' => $result['message']], 404);
        }

        return response()->json($result['data']);
    }

    public function approveRequest(EquipmentRequest $request)
    {
        $result = $this->equipmentService->approveRequest($request);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function checkOutEquipment(EquipmentRequest $request)
    {
        $result = $this->equipmentService->checkOutEquipment($request);

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
        $userId = $validated['user_id'];

        // First, check if there's an existing approved request for this user and equipment
        $existingRequest = EquipmentRequest::where('user_id', $userId)
            ->where('equipment_id', $equipment->id)
            ->where('status', EquipmentRequest::STATUS_APPROVED)
            ->whereNull('checked_out_at')
            ->whereNull('returned_at')
            ->first();

        if ($existingRequest) {
            // Check out the existing approved request
            $result = $this->equipmentService->checkOutEquipment($existingRequest);
            
            if (!$result['success']) {
                return back()->with('error', $result['message']);
            }

            return redirect()->route('admin.equipment.borrow-requests')
                ->with('success', 'Approved equipment request has been checked out successfully.');
        }

        // No existing approved request found, create new onsite borrow
        if (!$equipment->isAvailable()) {
            return back()->with('error', 'This equipment is no longer available.');
        }

        // Create and automatically approve the request for onsite borrowing
        $borrowRequest = EquipmentRequest::create([
            'user_id' => $userId,
            'equipment_id' => $equipment->id,
            'status' => EquipmentRequest::STATUS_APPROVED,
            'purpose' => $validated['purpose'],
            'requested_from' => now(),
            'requested_until' => $validated['requested_until'],
        ]);

        // Immediately check out the equipment
        $result = $this->equipmentService->checkOutEquipment($borrowRequest);
        
        if (!$result['success']) {
            // If checkout fails, delete the created request
            $borrowRequest->delete();
            return back()->with('error', $result['message']);
        }

        return redirect()->route('admin.equipment.borrow-requests')
            ->with('success', 'Equipment has been borrowed and checked out successfully.');
    }

    /**
     * Check if there's an approved request for the given user and equipment
     */
    public function checkApprovedRequest(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:rusers,id',
            'equipment_id' => 'required|exists:equipment,id',
        ]);

        $approvedRequest = EquipmentRequest::where('user_id', $validated['user_id'])
            ->where('equipment_id', $validated['equipment_id'])
            ->where('status', EquipmentRequest::STATUS_APPROVED)
            ->whereNull('checked_out_at')
            ->whereNull('returned_at')
            ->first();

        if ($approvedRequest) {
            return response()->json([
                'hasApprovedRequest' => true,
                'requestDate' => $approvedRequest->created_at->format('M d, Y'),
                'purpose' => Str::limit($approvedRequest->purpose, 50),
            ]);
        }

        return response()->json([
            'hasApprovedRequest' => false,
        ]);
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

    /**
     * Export single equipment barcode as PDF
     */
    public function exportSingleBarcode(Equipment $equipment, Request $request)
    {
        $labelSize = $request->input('label_size', 'standard');
        
        try {
            Log::info('Generating barcode for equipment', [
                'id' => $equipment->id,
                'name' => $equipment->name,
                'barcode' => $equipment->getIdentificationCode(),
                'label_size' => $labelSize
            ]);
            
            $pdf = $this->barcodeService->generateSingleBarcodePDF($equipment, $labelSize);
            
            $filename = 'barcode-' . $equipment->name . '-' . $equipment->getIdentificationCode() . '.pdf';
            $filename = preg_replace('/[^A-Za-z0-9\-_.]/', '', $filename); // Sanitize filename
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Barcode generation error', [
                'equipment_id' => $equipment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error generating barcode: ' . $e->getMessage());
        }
    }

    /**
     * Export selected equipment barcodes as PDF
     */
    public function exportSelectedBarcodes(Request $request)
    {
        $validated = $request->validate([
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipment,id',
            'label_size' => 'nullable|in:small,standard,medium,large'
        ]);

        $labelSize = $validated['label_size'] ?? 'standard';
        
        try {
            $pdf = $this->barcodeService->generateSelectedEquipmentBarcodesPDF(
                $validated['equipment_ids'], 
                $labelSize
            );
            
            $filename = 'equipment-barcodes-' . count($validated['equipment_ids']) . '-items-' . date('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating barcodes: ' . $e->getMessage());
        }
    }

    /**
     * Export all equipment barcodes as PDF
     */
    public function exportAllBarcodes(Request $request)
    {
        $labelSize = $request->input('label_size', 'standard');
        
        try {
            $pdf = $this->barcodeService->generateAllEquipmentBarcodesPDF($labelSize);
            
            $filename = 'all-equipment-barcodes-' . date('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating barcodes: ' . $e->getMessage());
        }
    }

    /**
     * Show barcode export options page
     */
    public function barcodeExport()
    {
        $equipment = Equipment::whereNotNull('barcode')
            ->with('category')
            ->orderBy('name')
            ->get();
            
        $labelSizes = BarcodeService::getLabelSizes();
        
        return view('admin.equipment.barcode.export', compact('equipment', 'labelSizes'));
    }
}