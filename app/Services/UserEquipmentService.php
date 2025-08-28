<?php

namespace App\Services;

use App\Models\EquipmentRequest;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Equipment Request service for user-related equipment operations
 */
class UserEquipmentService extends BaseService
{
    protected function getModel()
    {
        return EquipmentRequest::class;
    }

    /**
     * Get user's equipment request statistics
     */
    public function getUserStats($userId)
    {
        return [
            'pending_requests' => EquipmentRequest::forUser($userId)->pending()->count(),
            'currently_borrowed' => EquipmentRequest::forUser($userId)->currentlyBorrowed()->count(),
            'upcoming_returns' => EquipmentRequest::forUser($userId)->upcomingReturns(7)->count(),
            'total_requests' => EquipmentRequest::forUser($userId)->count(),
            'approved_requests' => EquipmentRequest::forUser($userId)->approved()->count(),
        ];
    }

    /**
     * Get user's currently borrowed equipment
     */
    public function getCurrentlyBorrowed($userId, $perPage = 10)
    {
        return EquipmentRequest::with(['equipment'])
            ->forUser($userId)
            ->currentlyBorrowed()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get user's equipment history
     */
    public function getHistory($userId, $perPage = 15)
    {
        return EquipmentRequest::with(['equipment'])
            ->forUser($userId)
            ->whereIn('status', [EquipmentRequest::STATUS_RETURNED, EquipmentRequest::STATUS_REJECTED])
            ->orWhere(function($query) use ($userId) {
                $query->forUser($userId)
                      ->approved()
                      ->returned();
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get user's recent equipment activities for dashboard
     */
    public function getRecentActivities($userId, $limit = 15)
    {
        return EquipmentRequest::with(['equipment.category', 'approvedBy', 'rejectedBy', 'checkedOutBy'])
            ->forUser($userId)
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($request) {
                return $this->formatActivity($request);
            });
    }

    /**
     * Get equipment available for borrowing by category
     */
    public function getAvailableEquipmentByCategory($categoryId = null)
    {
        $query = Equipment::where('status', Equipment::STATUS_AVAILABLE)
                          ->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->latest()->paginate(12);
    }

    /**
     * Create a new equipment request
     */
    public function createRequest(array $data, $userId)
    {
        $equipment = Equipment::findOrFail($data['equipment_id']);

        if (!$equipment->isAvailable()) {
            return [
                'success' => false,
                'message' => 'This equipment is no longer available.'
            ];
        }

        $request = EquipmentRequest::create([
            'equipment_id' => $equipment->id,
            'user_id' => $userId,
            'status' => EquipmentRequest::STATUS_PENDING,
            'purpose' => $data['purpose'],
            'requested_from' => $data['requested_from'],
            'requested_until' => $data['requested_until'],
        ]);

        return [
            'success' => true,
            'message' => 'Your borrow request has been submitted successfully.',
            'request' => $request
        ];
    }

    /**
     * Cancel a pending request
     */
    public function cancelRequest(EquipmentRequest $request, $userId)
    {
        if ($request->user_id !== $userId) {
            return [
                'success' => false,
                'message' => 'You are not authorized to cancel this request.'
            ];
        }

        if ($request->status !== EquipmentRequest::STATUS_PENDING) {
            return [
                'success' => false,
                'message' => 'Only pending requests can be canceled.'
            ];
        }

        $request->delete();
        
        return [
            'success' => true,
            'message' => 'Equipment request has been canceled.'
        ];
    }

    /**
     * Request return of borrowed equipment
     */
    public function requestReturn(EquipmentRequest $request, $userId)
    {
        if ($request->user_id !== $userId) {
            return [
                'success' => false,
                'message' => 'You are not authorized to mark this equipment as returned.'
            ];
        }

        if ($request->status !== EquipmentRequest::STATUS_APPROVED || $request->returned_at !== null) {
            return [
                'success' => false,
                'message' => 'This equipment cannot be marked as returned.'
            ];
        }

        $request->update([
            'return_requested_at' => now(),
        ]);
        
        return [
            'success' => true,
            'message' => 'Return request has been submitted. Please return the equipment to the laboratory.'
        ];
    }

    /**
     * Format activity data for display
     */
    private function formatActivity($request)
    {
        $activityText = '';
        $statusClass = '';
        $detailedStatus = $request->status;
        
        // Determine detailed status for display
        if ($request->returned_at) {
            $detailedStatus = 'returned';
        } elseif ($request->isCheckedOut() && !$request->returned_at) {
            $detailedStatus = 'checked_out';
        }
        
        switch($request->status) {
            case EquipmentRequest::STATUS_PENDING:
                $activityText = "<strong>Requested</strong> {$request->equipment->name}";
                $dateRange = Carbon::parse($request->requested_from)->format('M j') . 
                           ' - ' . Carbon::parse($request->requested_until)->format('M j, Y');
                $activityText .= '<br><small class="text-gray-600">Duration: ' . $dateRange . '</small>';
                $statusClass = 'yellow';
                break;
                
            case EquipmentRequest::STATUS_APPROVED:
                if ($request->returned_at) {
                    // Equipment has been returned
                    $activityText = "<strong>Returned</strong> {$request->equipment->name}";
                    $activityText .= '<br><small class="text-green-600">Returned on: ' . 
                                   $request->returned_at->format('M j, Y g:i A') . '</small>';
                    if ($request->return_condition) {
                        $conditionColor = $request->return_condition === 'good' ? 'text-green-600' : 
                                        ($request->return_condition === 'damaged' ? 'text-red-600' : 'text-yellow-600');
                        $activityText .= '<br><small class="' . $conditionColor . '">Condition: ' . 
                                       ucfirst($request->return_condition) . '</small>';
                    }
                    $detailedStatus = 'returned';
                    $statusClass = 'green';
                } elseif ($request->isCheckedOut()) {
                    // Equipment is currently checked out (borrowed)
                    $activityText = "<strong>Borrowed</strong> {$request->equipment->name}";
                    $activityText .= '<br><small class="text-blue-600">Checked out: ' . 
                                   $request->checked_out_at->format('M j, Y g:i A') . '</small>';
                    if ($request->checkedOutBy) {
                        $activityText .= '<br><small class="text-blue-600">By: ' . $request->checkedOutBy->name . '</small>';
                    }
                    $returnDate = Carbon::parse($request->requested_until);
                    $isOverdue = $returnDate->isPast();
                    $returnText = $isOverdue ? 'text-red-600' : 'text-gray-600';
                    $overdueLabel = $isOverdue ? ' (OVERDUE)' : '';
                    $activityText .= '<br><small class="' . $returnText . '">Due: ' . 
                                   $returnDate->format('M j, Y g:i A') . $overdueLabel . '</small>';
                    $detailedStatus = 'checked_out';
                    $statusClass = 'blue';
                } else {
                    // Equipment is approved but not yet checked out
                    $activityText = "<strong>Approved</strong> for {$request->equipment->name}";
                    $dateRange = Carbon::parse($request->requested_from)->format('M j') . 
                               ' - ' . Carbon::parse($request->requested_until)->format('M j, Y');
                    $activityText .= '<br><small class="text-green-600">Duration: ' . $dateRange . '</small>';
                    if ($request->approvedBy) {
                        $activityText .= '<br><small class="text-green-600">Approved by: ' . 
                                       $request->approvedBy->name . ' on ' . 
                                       $request->approved_at->format('M j, Y g:i A') . '</small>';
                    }
                    $activityText .= '<br><small class="text-blue-600">Ready for pickup</small>';
                    $statusClass = 'green';
                }
                break;
                
            case EquipmentRequest::STATUS_REJECTED:
                $activityText = "<strong>Request rejected</strong> for {$request->equipment->name}";
                $dateRange = Carbon::parse($request->requested_from)->format('M j') . 
                           ' - ' . Carbon::parse($request->requested_until)->format('M j, Y');
                $activityText .= '<br><small class="text-gray-600">Requested duration: ' . $dateRange . '</small>';
                if ($request->rejectedBy) {
                    $activityText .= '<br><small class="text-red-600">Rejected by: ' . 
                                   $request->rejectedBy->name . ' on ' . 
                                   $request->rejected_at->format('M j, Y g:i A') . '</small>';
                }
                if ($request->rejection_reason) {
                    $activityText .= '<br><small class="text-red-600">Reason: ' . 
                                   Str::limit($request->rejection_reason, 60) . '</small>';
                }
                $statusClass = 'red';
                break;
                
            default:
                $activityText = "Unknown status for {$request->equipment->name}";
                $statusClass = 'gray';
                break;
        }
        
        return [
            'id' => $request->id,
            'time' => $request->updated_at ?? $request->created_at, // Use updated_at for status changes
            'description' => $activityText,
            'status' => $detailedStatus, // Use detailed status for better badge display
            'status_class' => $statusClass,
            'equipment_name' => $request->equipment->name,
            'category_name' => $request->equipment->category->name ?? 'Uncategorized',
            'purpose' => $request->purpose,
            'activity_type' => 'request', // Use 'request' type for proper status badge colors
            'original_status' => $request->status, // Keep original status for reference
            'is_overdue' => $request->status === EquipmentRequest::STATUS_APPROVED && 
                          $request->isCheckedOut() && 
                          !$request->returned_at && 
                          Carbon::parse($request->requested_until)->isPast()
        ];
    }
}
