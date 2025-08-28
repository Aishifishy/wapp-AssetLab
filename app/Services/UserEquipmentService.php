<?php

namespace App\Services;

use App\Models\EquipmentRequest;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return EquipmentRequest::with(['equipment.category'])
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
        
        switch($request->status) {
            case EquipmentRequest::STATUS_PENDING:
                $activityText = "Requested {$request->equipment->name}";
                $statusClass = 'yellow';
                break;
            case EquipmentRequest::STATUS_APPROVED:
                if ($request->returned_at) {
                    $activityText = "Returned {$request->equipment->name}";
                    $statusClass = 'green';
                } else {
                    $activityText = "Borrowed {$request->equipment->name}";
                    $statusClass = 'blue';
                }
                break;
            case EquipmentRequest::STATUS_REJECTED:
                $activityText = "Request for {$request->equipment->name} was rejected";
                $statusClass = 'red';
                break;
        }
        
        return [
            'id' => $request->id,
            'time' => $request->created_at,
            'description' => $activityText,
            'status' => $request->status,
            'status_class' => $statusClass,
            'equipment_name' => $request->equipment->name,
            'category_name' => $request->equipment->category->name ?? 'Uncategorized',
            'purpose' => $request->purpose,
            'activity_type' => 'equipment'
        ];
    }
}
