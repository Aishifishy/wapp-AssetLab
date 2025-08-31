<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\EquipmentCategory;
use App\Services\UserEquipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EquipmentController extends Controller
{
    use ControllerHelpers;

    protected $equipmentService;

    public function __construct(UserEquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    /**
     * Display equipment available for borrowing.
     */
    public function index(Request $request)
    {
        $categoryId = $request->get('category');
        
        if ($categoryId) {
            return $this->showCategory($categoryId);
        }
        
        // Show categories overview
        $categories = EquipmentCategory::withCount(['equipment' => function ($query) {
            $query->where('status', Equipment::STATUS_AVAILABLE);
        }])
        ->orderBy('name')
        ->get()
        ->where('equipment_count', '>', 0);
        
        return view('ruser.equipment.categories', compact('categories'));
    }

    /**
     * Show equipment for a specific category
     */
    public function showCategory($categoryId)
    {
        $selectedCategory = EquipmentCategory::findOrFail($categoryId);
        $equipment = $this->equipmentService->getAvailableEquipmentByCategory($categoryId);
        
        return view('ruser.equipment.borrow', compact('equipment', 'selectedCategory'));
    }

    /**
     * Handle equipment borrow request.
     */
    public function request(Request $request)
    {
        $validated = $this->validateRequest($request, [
            'equipment_id' => 'required|exists:equipment,id',
            'purpose' => 'required|string|max:1000',
            'requested_from' => 'required|date|after:now',
            'requested_until' => 'required|date|after:requested_from',
            'booking_type' => 'sometimes|in:immediate,advance',
        ]);

        $result = $this->equipmentService->createRequest($validated, Auth::id());

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('dashboard')
            ->with('success', $result['message']);
    }

    /**
     * Check equipment availability for booking.
     */
    public function checkAvailability(Request $request)
    {
        $validated = $this->validateRequest($request, [
            'equipment_id' => 'required|exists:equipment,id',
            'requested_from' => 'required|date|after:now',
            'requested_until' => 'required|date|after:requested_from',
        ]);

        $result = $this->equipmentService->checkAvailabilityForTimeSlot(
            $validated['equipment_id'],
            $validated['requested_from'],
            $validated['requested_until'],
            Auth::id()
        );

        return response()->json($result);
    }

    /**
     * Cancel a pending equipment request.
     */
    public function cancelRequest(EquipmentRequest $equipmentRequest)
    {
        Log::info('Cancel request called', [
            'request_id' => $equipmentRequest->id,
            'user_id' => Auth::id(),
            'is_ajax' => request()->ajax(),
            'expects_json' => request()->expectsJson(),
            'x_requested_with' => request()->header('X-Requested-With'),
            'content_type' => request()->header('Content-Type'),
            'accept' => request()->header('Accept')
        ]);

        try {
            $result = $this->equipmentService->cancelRequest($equipmentRequest, Auth::id());

            Log::info('Cancel request result', $result);

            // Always return JSON for AJAX requests
            if (request()->expectsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                Log::info('Returning JSON response');
                return response()->json([
                    'success' => $result['success'],
                    'message' => $result['message']
                ], $result['success'] ? 200 : 400);
            }

            Log::info('Returning redirect response');
            return redirect()->route('dashboard')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        } catch (\Exception $e) {
            Log::error('Cancel request error: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while cancelling the request.'
                ], 500);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'An error occurred while cancelling the request.');
        }
    }

    /**
     * Mark equipment as returned by the user.
     */
    public function return(EquipmentRequest $equipmentRequest)
    {
        $result = $this->equipmentService->requestReturn($equipmentRequest, Auth::id());

        return redirect()->route('dashboard')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Show currently borrowed equipment by the user.
     */
    public function borrowed()
    {
        $borrowedRequests = $this->equipmentService->getCurrentlyBorrowed(Auth::id());

        return view('ruser.equipment.borrowed', compact('borrowedRequests'));
    }

    /**
     * Show pending equipment requests by the user.
     */
    /**
     * Show equipment borrowing history for the user.
     */
    public function history()
    {
        $historyRequests = $this->equipmentService->getHistory(Auth::id());

        return view('ruser.equipment.history', compact('historyRequests'));
    }
}
