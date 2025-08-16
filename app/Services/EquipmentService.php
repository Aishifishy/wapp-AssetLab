<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Equipment service for business logic
 */
class EquipmentService extends BaseService
{
    protected function getModel()
    {
        return Equipment::class;
    }

    /**
     * Get equipment with advanced filtering
     */
    public function getEquipmentIndex(Request $request)
    {
        $filters = [
            'status' => $request->input('status')
        ];

        $searchFields = ['name', 'description', 'location', 'category.name'];
        
        return $this->getFilteredData(
            $request, 
            ['currentBorrower', 'category'], 
            $searchFields, 
            $filters, 
            10
        );
    }

    /**
     * Get equipment for management with additional data
     */
    public function getEquipmentManage(Request $request)
    {
        $filters = [
            'status' => $request->input('status')
        ];

        $searchFields = ['name', 'description', 'rfid_tag', 'category.name'];
        
        $equipment = $this->getFilteredData(
            $request, 
            ['currentBorrower', 'borrowRequests', 'category'], 
            $searchFields, 
            $filters, 
            0 // Get all records
        );

        $categories = EquipmentCategory::all();
        
        return [
            'equipment' => $equipment,
            'categories' => $categories
        ];
    }

    /**
     * Get borrow requests with statistics
     */
    public function getBorrowRequests()
    {
        $requests = EquipmentRequest::with(['user', 'equipment'])
            ->latest()
            ->paginate(15);

        $statistics = [
            'pending' => EquipmentRequest::where('status', 'pending')->count(),
            'active' => EquipmentRequest::where('status', 'approved')
                ->whereNull('returned_at')
                ->count(),
            'overdue' => EquipmentRequest::where('status', 'approved')
                ->whereNull('returned_at')
                ->where('requested_until', '<', Carbon::now())
                ->count()
        ];

        $availableEquipment = Equipment::available()->with('category')->get();
        $users = \App\Models\Ruser::all();

        return [
            'requests' => $requests,
            'statistics' => $statistics,
            'availableEquipment' => $availableEquipment,
            'users' => $users
        ];
    }

    /**
     * Create equipment with validation
     */
    public function createEquipment(array $data)
    {
        $this->logAction('create_equipment', null, $data);
        
        return Equipment::create($data);
    }

    /**
     * Update equipment with validation
     */
    public function updateEquipment(Equipment $equipment, array $data)
    {
        $this->logAction('update_equipment', $equipment, $data);
        
        $equipment->update($data);
        
        return $equipment;
    }

    /**
     * Delete equipment with checks
     */
    public function deleteEquipment(Equipment $equipment)
    {
        if ($equipment->status === Equipment::STATUS_BORROWED) {
            return [
                'success' => false,
                'message' => 'Cannot delete equipment that is currently borrowed.'
            ];
        }

        $this->logAction('delete_equipment', $equipment);
        
        $equipment->delete();

        return [
            'success' => true,
            'message' => 'Equipment deleted successfully.'
        ];
    }

    /**
     * Approve equipment request
     */
    public function approveRequest(EquipmentRequest $request)
    {
        if (!$request->isPending()) {
            return [
                'success' => false,
                'message' => 'This request cannot be approved.'
            ];
        }

        if ($request->equipment->status !== Equipment::STATUS_AVAILABLE) {
            return [
                'success' => false,
                'message' => 'This equipment is not available for borrowing.'
            ];
        }

        $request->update(['status' => 'approved']);
        $request->equipment->update(['status' => Equipment::STATUS_BORROWED]);

        $this->logAction('approve_request', $request);

        return [
            'success' => true,
            'message' => 'Equipment request approved successfully.'
        ];
    }

    /**
     * Return equipment
     */
    public function returnEquipment(EquipmentRequest $request, array $returnData)
    {
        if (!$request->isApproved() || $request->returned_at) {
            return [
                'success' => false,
                'message' => 'This equipment cannot be marked as returned.'
            ];
        }

        $request->update([
            'returned_at' => Carbon::now(),
            'return_condition' => $returnData['condition'],
            'return_notes' => $returnData['notes'] ?? null,
        ]);

        $request->equipment->update([
            'status' => $returnData['condition'] === 'good' 
                ? Equipment::STATUS_AVAILABLE 
                : Equipment::STATUS_UNAVAILABLE
        ]);

        $this->logAction('return_equipment', $request, $returnData);

        return [
            'success' => true,
            'message' => 'Equipment marked as returned successfully.'
        ];
    }

    /**
     * Find equipment by RFID
     */
    public function findByRfid(string $rfidTag)
    {
        if (empty($rfidTag)) {
            return [
                'success' => false,
                'message' => 'RFID tag is required'
            ];
        }

        $equipment = Equipment::with('category')
            ->where('rfid_tag', $rfidTag)
            ->where('status', Equipment::STATUS_AVAILABLE)
            ->first();

        if (!$equipment) {
            return [
                'success' => false,
                'message' => 'Available equipment not found with this RFID tag'
            ];
        }

        return [
            'success' => true,
            'data' => [
                'id' => $equipment->id,
                'name' => $equipment->name,
                'category' => $equipment->category->name ?? 'Uncategorized',
                'description' => $equipment->description,
                'status' => $equipment->status,
                'rfid_tag' => $equipment->rfid_tag
            ]
        ];
    }
}
