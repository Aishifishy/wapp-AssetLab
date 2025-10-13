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
use Illuminate\Support\Facades\Mail;
use App\Mail\EquipmentRequestSubmitted;
use App\Mail\EquipmentRequestReceived;
use App\Models\Radmin;

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
        
        // Show categories overview - include both available and borrowed equipment
        $categories = EquipmentCategory::withCount([
            'equipment' => function ($query) {
                $query->whereIn('status', [Equipment::STATUS_AVAILABLE, 'borrowed']);
            },
            'equipment as available_count' => function ($query) {
                $query->where('status', Equipment::STATUS_AVAILABLE);
            },
            'equipment as borrowed_count' => function ($query) {
                $query->where('status', 'borrowed');
            }
        ])
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
        try {
            $validated = $this->validateRequest($request, [
                'equipment_id' => 'required|exists:equipment,id',
                'purpose' => 'required|string|max:1000',
                'requested_from' => 'required|date|after_or_equal:' . now()->subMinutes(5)->format('Y-m-d H:i:s'),
                'requested_until' => 'required|date|after:requested_from',
                'booking_type' => 'sometimes|in:immediate,advance',
            ]);

            $result = $this->equipmentService->createRequest($validated, Auth::id());

            if (!$result['success']) {
                return back()->with('error', $result['message']);
            }

            // Send email notifications
            $equipmentRequest = $result['request']; // Assuming the service returns the created request
            
            // Send confirmation email to user
            Mail::to(Auth::user()->email)->send(new EquipmentRequestSubmitted($equipmentRequest));
            
            // Send notification email to admins
            $adminEmails = Radmin::pluck('email')->toArray();
            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->send(new EquipmentRequestReceived($equipmentRequest));
            }

            return redirect()->route('ruser.dashboard')
                ->with('success', $result['message']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Equipment request creation failed', [
                'user_id' => Auth::id(),
                'equipment_id' => $request->equipment_id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'An error occurred while processing your request. Please try again.');
        }
    }



    /**
     * Check equipment availability for booking.
     */
    public function checkAvailability(Request $request)
    {
        try {
            // Log the incoming request data for debugging
            Log::info('Equipment availability check request', [
                'user_id' => Auth::id(),
                'equipment_id' => $request->equipment_id,
                'requested_from' => $request->requested_from,
                'requested_until' => $request->requested_until,
                'current_time' => now()->toDateTimeString()
            ]);

            $validated = $this->validateRequest($request, [
                'equipment_id' => 'required|exists:equipment,id',
                'requested_from' => 'required|date|after_or_equal:now',
                'requested_until' => 'required|date|after:requested_from',
            ]);

            $result = $this->equipmentService->checkAvailabilityForTimeSlot(
                $validated['equipment_id'],
                $validated['requested_from'],
                $validated['requested_until'],
                Auth::id()
            );

            return response()->json($result);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'available' => false,
                'message' => 'Validation error: ' . implode(' ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Equipment availability check failed', [
                'user_id' => Auth::id(),
                'equipment_id' => $request->equipment_id,
                'requested_from' => $request->requested_from,
                'requested_until' => $request->requested_until,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'available' => false,
                'message' => 'An error occurred while checking availability. Please try again.'
            ], 500);
        }
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
            return redirect()->route('ruser.dashboard')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        } catch (\Exception $e) {
            Log::error('Cancel request error: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while cancelling the request.'
                ], 500);
            }
            
            return redirect()->route('ruser.dashboard')
                ->with('error', 'An error occurred while cancelling the request.');
        }
    }

    /**
     * Mark equipment as returned by the user.
     */
    public function return(EquipmentRequest $equipmentRequest)
    {
        $result = $this->equipmentService->requestReturn($equipmentRequest, Auth::id());

        return redirect()->route('ruser.dashboard')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }



    /**
     * Show currently borrowed equipment by the user.
     */
    public function borrowed(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $allowedPerPage = [5, 10, 15, 25, 50, 100];
        
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $borrowedRequests = $this->equipmentService->getCurrentlyBorrowed(Auth::id(), $perPage);

        return view('ruser.equipment.borrowed', compact('borrowedRequests', 'perPage'));
    }

    /**
     * Show equipment borrowing history for the user.
     */
    public function history(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $allowedPerPage = [5, 10, 15, 25, 50, 100];
        
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }

        $historyRequests = $this->equipmentService->getHistory(Auth::id(), $perPage);

        return view('ruser.equipment.history', compact('historyRequests', 'perPage'));
    }

    /**
     * Get live status updates for equipment items (for AJAX polling)
     */
    public function getLiveStatus(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            $lastUpdate = $request->get('last_update');
            
            $query = Equipment::select('id', 'name', 'status', 'updated_at');
            
            // Filter by category if provided
            if ($categoryId) {
                $query->where('equipment_category_id', $categoryId);
            }
            
            // Only get items updated after the last check (for efficiency)
            if ($lastUpdate) {
                $query->where('updated_at', '>', $lastUpdate);
            }
            
            $equipmentUpdates = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'status' => $item->status ?? 'available',
                    'updated_at' => $item->updated_at->toISOString(),
                ];
            });

            // Get pending/recent equipment requests count for real-time notifications
            $recentRequests = Auth::check() ? 
                Auth::user()->equipmentRequests()
                    ->where('created_at', '>', now()->subMinutes(5))
                    ->whereIn('status', ['pending', 'approved'])
                    ->count() : 0;

            return response()->json([
                'success' => true,
                'equipment' => $equipmentUpdates,
                'recent_requests' => $recentRequests,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Live status update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch live updates'
            ], 500);
        }
    }
}
