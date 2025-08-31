<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

        $searchFields = ['name', 'description', 'barcode', 'rfid_tag', 'category.name'];
        
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
        // Run background auto-repair for all equipment before loading page
        $this->autoRepairAllEquipment();
        
        $requests = EquipmentRequest::with(['user', 'equipment', 'approvedBy', 'rejectedBy', 'checkedOutBy'])
            ->latest()
            ->paginate(15);

        $statistics = [
            'pending' => EquipmentRequest::where('status', 'pending')->count(),
            'active' => EquipmentRequest::checkedOut()
                ->whereNull('returned_at')
                ->count(),
            'overdue' => EquipmentRequest::checkedOut()
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
     * Check if auto-rejection of conflicting requests is enabled
     */
    private function isAutoRejectionEnabled()
    {
        // You can make this configurable via settings table or config file
        return config('equipment.auto_reject_conflicts', true);
    }

    /**
     * Approve equipment request and auto-reject conflicting requests
     */
    public function approveRequest(EquipmentRequest $request)
    {
        if (!$request->isPending()) {
            return [
                'success' => false,
                'message' => 'This request cannot be approved.'
            ];
        }

        // Auto-repair equipment status before checking availability
        $this->autoRepairEquipmentStatus($request->equipment);
        
        // Refresh equipment data after potential auto-repair
        $request->equipment->refresh();

        if ($request->equipment->status !== Equipment::STATUS_AVAILABLE) {
            return [
                'success' => false,
                'message' => 'This equipment is not available for borrowing.'
            ];
        }

        DB::beginTransaction();
        
        try {
            // Approve the current request
            $request->update([
                'status' => EquipmentRequest::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth('admin')->id()
            ]);

            // Auto-reject conflicting requests if enabled
            if ($this->isAutoRejectionEnabled()) {
                $conflictingRequests = $this->getConflictingRequests($request);
                
                if ($conflictingRequests->count() > 0) {
                    $rejectedCount = $this->autoRejectConflictingRequests($conflictingRequests, $request);
                    
                    $this->logAction('approve_request_with_auto_reject', $request, [
                        'rejected_conflicting_requests' => $rejectedCount
                    ]);
                    
                    $message = 'Equipment request approved successfully. Equipment can now be checked out.';
                    if ($rejectedCount > 0) {
                        $message .= " Additionally, {$rejectedCount} conflicting " . 
                                  ($rejectedCount === 1 ? 'request was' : 'requests were') . 
                                  " automatically rejected due to time slot conflicts.";
                    }
                    
                    DB::commit();
                    
                    return [
                        'success' => true,
                        'message' => $message,
                        'rejected_count' => $rejectedCount
                    ];
                }
            }
            
            $this->logAction('approve_request', $request);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Equipment request approved successfully. Equipment can now be checked out.'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving request with auto-rejection: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'An error occurred while processing the approval. Please try again.'
            ];
        }
    }

    /**
     * Get conflicting equipment requests for the same equipment and overlapping time periods
     */
    private function getConflictingRequests(EquipmentRequest $approvedRequest)
    {
        return EquipmentRequest::where('equipment_id', $approvedRequest->equipment_id)
            ->where('id', '!=', $approvedRequest->id)
            ->where('status', EquipmentRequest::STATUS_PENDING)
            ->where(function($query) use ($approvedRequest) {
                $query->where(function($subQuery) use ($approvedRequest) {
                    // Overlapping time periods logic
                    $subQuery->where('requested_from', '<', $approvedRequest->requested_until)
                             ->where('requested_until', '>', $approvedRequest->requested_from);
                });
            })
            ->with(['user', 'equipment'])
            ->get();
    }

    /**
     * Auto-reject conflicting equipment requests
     */
    private function autoRejectConflictingRequests($conflictingRequests, EquipmentRequest $approvedRequest)
    {
        $rejectedCount = 0;
        $adminId = auth('admin')->id();
        $rejectionReason = str_replace(
            '{approved_request_id}', 
            $approvedRequest->id, 
            config('equipment.auto_rejection_reasons.time_conflict')
        );
        
        foreach ($conflictingRequests as $conflictRequest) {
            try {
                $conflictRequest->update([
                    'status' => EquipmentRequest::STATUS_REJECTED,
                    'rejected_at' => now(),
                    'rejected_by' => $adminId,
                    'rejection_reason' => $rejectionReason
                ]);
                
                $this->logAction('auto_reject_conflict', $conflictRequest, [
                    'approved_request_id' => $approvedRequest->id,
                    'conflict_reason' => 'overlapping_time_slot',
                    'conflict_from' => $conflictRequest->requested_from,
                    'conflict_until' => $conflictRequest->requested_until,
                    'approved_from' => $approvedRequest->requested_from,
                    'approved_until' => $approvedRequest->requested_until
                ]);
                
                $rejectedCount++;
                
                // Notify user about auto-rejection if enabled
                if (config('equipment.conflict_detection.notify_auto_rejection', true)) {
                    $this->notifyUserOfAutoRejection($conflictRequest, $approvedRequest);
                }
                
            } catch (\Exception $e) {
                Log::error("Failed to auto-reject conflicting request {$conflictRequest->id}: " . $e->getMessage());
            }
        }
        
        return $rejectedCount;
    }

    /**
     * Notify user about auto-rejection due to conflict
     */
    private function notifyUserOfAutoRejection(EquipmentRequest $rejectedRequest, EquipmentRequest $approvedRequest)
    {
        // Log the auto-rejection for transparency
        Log::info("Auto-rejected equipment request", [
            'rejected_request_id' => $rejectedRequest->id,
            'user_id' => $rejectedRequest->user_id,
            'user_name' => $rejectedRequest->user->name ?? 'Unknown',
            'equipment_id' => $rejectedRequest->equipment_id,
            'equipment_name' => $rejectedRequest->equipment->name ?? 'Unknown',
            'approved_request_id' => $approvedRequest->id,
            'reason' => 'Time slot conflict',
            'rejected_time_slot' => $rejectedRequest->requested_from . ' to ' . $rejectedRequest->requested_until,
            'approved_time_slot' => $approvedRequest->requested_from . ' to ' . $approvedRequest->requested_until
        ]);
        
        // TODO: Implement email notification to user if needed
        // This could include information about alternative time slots
    }

    /**
     * Get auto-rejection statistics for reporting
     */
    public function getAutoRejectionStats($dateFrom = null, $dateTo = null)
    {
        $query = EquipmentRequest::where('status', EquipmentRequest::STATUS_REJECTED)
            ->where('rejection_reason', 'LIKE', '%Automatically rejected due to time conflict%');

        if ($dateFrom) {
            $query->where('rejected_at', '>=', Carbon::parse($dateFrom));
        }

        if ($dateTo) {
            $query->where('rejected_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        $autoRejectedRequests = $query->with(['user', 'equipment', 'rejectedBy'])
            ->orderBy('rejected_at', 'desc')
            ->get();

        return [
            'total_auto_rejected' => $autoRejectedRequests->count(),
            'auto_rejected_today' => $autoRejectedRequests->filter(function($request) {
                return $request->rejected_at->isToday();
            })->count(),
            'auto_rejected_this_week' => $autoRejectedRequests->filter(function($request) {
                return $request->rejected_at->isCurrentWeek();
            })->count(),
            'auto_rejected_this_month' => $autoRejectedRequests->filter(function($request) {
                return $request->rejected_at->isCurrentMonth();
            })->count(),
            'requests' => $autoRejectedRequests
        ];
    }

    /**
     * Preview conflicts before approving a request (for admin review)
     */
    public function previewConflicts(EquipmentRequest $request)
    {
        if (!$request->isPending()) {
            return [
                'has_conflicts' => false,
                'message' => 'Request is not pending.'
            ];
        }

        $conflictingRequests = $this->getConflictingRequests($request);

        return [
            'has_conflicts' => $conflictingRequests->count() > 0,
            'conflict_count' => $conflictingRequests->count(),
            'conflicting_requests' => $conflictingRequests->map(function($conflict) {
                return [
                    'id' => $conflict->id,
                    'user_name' => $conflict->user->name,
                    'user_email' => $conflict->user->email,
                    'requested_from' => $conflict->requested_from,
                    'requested_until' => $conflict->requested_until,
                    'purpose' => $conflict->purpose,
                    'created_at' => $conflict->created_at
                ];
            }),
            'auto_rejection_enabled' => $this->isAutoRejectionEnabled()
        ];
    }

    /**
     * Check out equipment to borrower
     */
    public function checkOutEquipment(EquipmentRequest $request)
    {
        if (!$request->isApproved() || $request->isCheckedOut()) {
            return [
                'success' => false,
                'message' => 'This equipment cannot be checked out.'
            ];
        }

        $equipment = $request->equipment;
        
        // Auto-repair: Check if equipment status is inconsistent and fix it
        $this->autoRepairEquipmentStatus($equipment);
        
        // Refresh equipment data after potential auto-repair
        $equipment->refresh();
        
        // Check for time slot conflicts with other approved/checked-out requests
        $conflictingRequest = EquipmentRequest::where('equipment_id', $equipment->id)
            ->where('status', 'approved')
            ->where('id', '!=', $request->id)
            ->where(function($query) use ($request) {
                // Check if the requested time overlaps with existing requests
                $query->where(function($q) use ($request) {
                    // New request starts during existing request
                    $q->where('requested_from', '<=', $request->requested_from)
                      ->where('requested_until', '>', $request->requested_from);
                })->orWhere(function($q) use ($request) {
                    // New request ends during existing request  
                    $q->where('requested_from', '<', $request->requested_until)
                      ->where('requested_until', '>=', $request->requested_until);
                })->orWhere(function($q) use ($request) {
                    // New request completely encompasses existing request
                    $q->where('requested_from', '>=', $request->requested_from)
                      ->where('requested_until', '<=', $request->requested_until);
                });
            })
            ->whereNull('returned_at')
            ->first();

        if ($conflictingRequest) {
            return [
                'success' => false,
                'message' => 'This equipment has a time slot conflict with another approved request.'
            ];
        }
        
        // Check if equipment is currently checked out by someone else
        $currentActiveCheckout = EquipmentRequest::where('equipment_id', $equipment->id)
            ->where('status', 'approved')
            ->whereNotNull('checked_out_at')
            ->whereNull('returned_at')
            ->where('id', '!=', $request->id) // Exclude current request
            ->first();

        if ($currentActiveCheckout) {
            return [
                'success' => false,
                'message' => 'This equipment is currently checked out by another user.'
            ];
        }

        // For equipment marked as borrowed, check if it's for this specific request's user and time slot
        if ($equipment->status === Equipment::STATUS_BORROWED) {
            // Allow checkout if equipment is marked as borrowed for this user and no conflicting checkouts
            if ($equipment->current_borrower_id !== $request->user_id) {
                return [
                    'success' => false,
                    'message' => 'This equipment is currently borrowed by another user.'
                ];
            }
        }

        // Equipment should be available or already assigned to this user
        $isAvailableForCheckout = ($equipment->status === Equipment::STATUS_AVAILABLE) ||
            ($equipment->status === Equipment::STATUS_BORROWED && $equipment->current_borrower_id === $request->user_id);

        if (!$isAvailableForCheckout) {
            return [
                'success' => false,
                'message' => 'This equipment is not available for checkout. Status: ' . $equipment->status
            ];
        }

        $request->update([
            'status' => EquipmentRequest::STATUS_CHECKED_OUT,
            'checked_out_at' => Carbon::now(),
            'checked_out_by' => auth('admin')->id(),
        ]);

        $request->equipment->update([
            'status' => Equipment::STATUS_BORROWED,
            'current_borrower_id' => $request->user_id,
        ]);

        $this->logAction('checkout_equipment', $request);

        return [
            'success' => true,
            'message' => 'Equipment checked out successfully.'
        ];
    }

    /**
     * Auto-repair status for a specific equipment
     */
    private function autoRepairEquipmentStatus(Equipment $equipment)
    {
        try {
            // Check if equipment is marked as borrowed but has no active checkout
            if ($equipment->status === 'borrowed') {
                $hasActiveCheckout = EquipmentRequest::where('equipment_id', $equipment->id)
                    ->where('status', 'checked_out')
                    ->whereNull('returned_at')
                    ->exists();

                if (!$hasActiveCheckout) {
                    $equipment->update(['status' => 'available']);
                    Log::info('Auto-repair: Fixed orphaned borrowed status', [
                        'equipment_id' => $equipment->id,
                        'name' => $equipment->name
                    ]);
                    return true;
                }
            }

            // Check if equipment is marked as available but has active checkout
            if ($equipment->status === 'available') {
                $hasActiveCheckout = EquipmentRequest::where('equipment_id', $equipment->id)
                    ->where('status', 'checked_out')
                    ->whereNull('returned_at')
                    ->exists();

                if ($hasActiveCheckout) {
                    $equipment->update(['status' => 'borrowed']);
                    Log::info('Auto-repair: Fixed orphaned available status', [
                        'equipment_id' => $equipment->id,
                        'name' => $equipment->name
                    ]);
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Auto-repair failed for equipment', [
                'equipment_id' => $equipment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Auto-repair all equipment status inconsistencies
     */
    private function autoRepairAllEquipment()
    {
        try {
            $repaired = 0;
            
            // Find equipment marked as borrowed but with no active checkout
            $orphanedBorrowed = Equipment::where('status', 'borrowed')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('equipment_requests')
                        ->whereColumn('equipment_requests.equipment_id', 'equipment.id')
                        ->where('equipment_requests.status', 'checked_out')
                        ->whereNull('equipment_requests.returned_at');
                })
                ->get();

            foreach ($orphanedBorrowed as $equipment) {
                $equipment->update(['status' => 'available']);
                $repaired++;
                Log::info('Auto-repair: Fixed orphaned borrowed status', [
                    'equipment_id' => $equipment->id,
                    'name' => $equipment->name
                ]);
            }

            // Find equipment marked as available but with active checkouts
            $orphanedAvailable = Equipment::where('status', 'available')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('equipment_requests')
                        ->whereColumn('equipment_requests.equipment_id', 'equipment.id')
                        ->where('equipment_requests.status', 'checked_out')
                        ->whereNull('equipment_requests.returned_at');
                })
                ->get();

            foreach ($orphanedAvailable as $equipment) {
                $equipment->update(['status' => 'borrowed']);
                $repaired++;
                Log::info('Auto-repair: Fixed orphaned available status', [
                    'equipment_id' => $equipment->id,
                    'name' => $equipment->name
                ]);
            }

            if ($repaired > 0) {
                Log::info("Auto-repair completed: Fixed {$repaired} equipment status inconsistencies");
            }
            
        } catch (\Exception $e) {
            Log::error('Auto-repair failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Return equipment
     */
    public function returnEquipment(EquipmentRequest $request, array $returnData)
    {
        // Allow return if request is approved OR checked out, not already returned, and has been checked out
        $canReturn = ($request->isApproved() || $request->status === EquipmentRequest::STATUS_CHECKED_OUT) 
                    && !$request->returned_at 
                    && $request->isCheckedOut();
                    
        if (!$canReturn) {
            return [
                'success' => false,
                'message' => 'This equipment cannot be marked as returned. Status: ' . $request->status
            ];
        }

        // Auto-repair equipment status before processing return
        $this->autoRepairEquipmentStatus($request->equipment);

        $request->update([
            'status' => EquipmentRequest::STATUS_RETURNED,
            'returned_at' => Carbon::now(),
            'return_condition' => $returnData['condition'],
            'return_notes' => $returnData['notes'] ?? null,
        ]);

        $request->equipment->update([
            'status' => $returnData['condition'] === 'good' 
                ? Equipment::STATUS_AVAILABLE 
                : Equipment::STATUS_UNAVAILABLE,
            'current_borrower_id' => null,
        ]);

        $this->logAction('return_equipment', $request, $returnData);

        return [
            'success' => true,
            'message' => 'Equipment marked as returned successfully.'
        ];
    }

    /**
     * Find equipment by RFID (legacy support)
     */
    public function findByRfid(string $rfidTag)
    {
        return $this->findByIdentificationCode($rfidTag, 'rfid_tag');
    }

    /**
     * Find equipment by barcode
     */
    public function findByBarcode(string $barcode)
    {
        return $this->findByIdentificationCode($barcode, 'barcode');
    }

    /**
     * Universal method to find equipment by barcode or RFID (legacy)
     */
    public function findByIdentificationCode(string $code, string $field = null)
    {
        if (empty($code)) {
            return [
                'success' => false,
                'message' => 'Identification code is required'
            ];
        }

        $query = Equipment::with('category')
            ->where('status', Equipment::STATUS_AVAILABLE);

        if ($field) {
            // Search specific field
            $query->where($field, $code);
        } else {
            // Search barcode first, then RFID as fallback
            $query->where(function($q) use ($code) {
                $q->where('barcode', $code)
                  ->orWhere('rfid_tag', $code);
            });
        }

        $equipment = $query->first();

        if (!$equipment) {
            return [
                'success' => false,
                'message' => 'Available equipment not found with this identification code'
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
                'identification_code' => $equipment->getIdentificationCode(),
                'identification_label' => $equipment->getIdentificationLabel(),
                // Legacy support
                'rfid_tag' => $equipment->rfid_tag,
                'barcode' => $equipment->barcode
            ]
        ];
    }
}
