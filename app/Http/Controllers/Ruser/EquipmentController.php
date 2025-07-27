<?php

namespace App\Http\Controllers\Ruser;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentRequest;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    /**
     * Display equipment available for borrowing.
     */    public function index()
    {        $equipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->latest()
            ->paginate(12);
        
        $categories = EquipmentCategory::all();
            
        return view('ruser.equipment.borrow', compact('equipment', 'categories'));
    }

    /**
     * Handle equipment borrow request.
     */
    public function request(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'purpose' => 'required|string|max:1000',
            'requested_from' => 'required|date|after:now',
            'requested_until' => 'required|date|after:requested_from',
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        if (!$equipment->isAvailable()) {
            return back()->with('error', 'This equipment is no longer available.');
        }        $request = EquipmentRequest::create([
            'equipment_id' => $equipment->id,
            'user_id' => Auth::id(),
            'status' => EquipmentRequest::STATUS_PENDING,
            'purpose' => $validated['purpose'],
            'requested_from' => $validated['requested_from'],
            'requested_until' => $validated['requested_until'],
        ]);        return redirect()->route('dashboard')
            ->with('success', 'Your borrow request has been submitted successfully.');    }

    /**
     * Cancel a pending equipment request.
     */public function cancelRequest(EquipmentRequest $equipmentRequest)
    {
        if ($equipmentRequest->user_id !== Auth::id()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to cancel this request.');
        }

        if ($equipmentRequest->status !== EquipmentRequest::STATUS_PENDING) {
            return redirect()->route('dashboard')
                ->with('error', 'Only pending requests can be canceled.');
        }

        $equipmentRequest->delete();
        
        return redirect()->route('dashboard')
            ->with('success', 'Equipment request has been canceled.');
    }

    /**
     * Mark equipment as returned by the user.
     */    public function return(EquipmentRequest $equipmentRequest)
    {
        if ($equipmentRequest->user_id !== Auth::id()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to mark this equipment as returned.');
        }

        if ($equipmentRequest->status !== EquipmentRequest::STATUS_APPROVED || $equipmentRequest->returned_at !== null) {
            return redirect()->route('dashboard')
                ->with('error', 'This equipment cannot be marked as returned.');
        }

        $equipmentRequest->update([
            'return_requested_at' => now(),
        ]);
        
        return redirect()->route('dashboard')
            ->with('success', 'Return request has been submitted. Please return the equipment to the laboratory.');
    }

    /**
     * Show currently borrowed equipment by the user.
     */
    public function borrowed()
    {
        $borrowedRequests = EquipmentRequest::with(['equipment'])
            ->where('user_id', Auth::id())
            ->where('status', EquipmentRequest::STATUS_APPROVED)
            ->whereNull('returned_at')
            ->latest()
            ->paginate(10);

        return view('ruser.equipment.borrowed', compact('borrowedRequests'));
    }

    /**
     * Show equipment borrowing history for the user.
     */
    public function history()
    {
        $historyRequests = EquipmentRequest::with(['equipment'])
            ->where('user_id', Auth::id())
            ->whereIn('status', [EquipmentRequest::STATUS_RETURNED, EquipmentRequest::STATUS_REJECTED])
            ->orWhere(function($query) {
                $query->where('user_id', Auth::id())
                      ->where('status', EquipmentRequest::STATUS_APPROVED)
                      ->whereNotNull('returned_at');
            })
            ->latest()
            ->paginate(15);

        return view('ruser.equipment.history', compact('historyRequests'));
    }
}
