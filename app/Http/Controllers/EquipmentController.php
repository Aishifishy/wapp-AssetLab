<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->latest()
            ->paginate(12);
            
        return view('equipment.borrow', compact('equipment'));
    }

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
        }

        $request = EquipmentRequest::create([
            'equipment_id' => $equipment->id,
            'user_id' => Auth::id(),
            'status' => EquipmentRequest::STATUS_PENDING,
            'purpose' => $validated['purpose'],
            'requested_from' => $validated['requested_from'],
            'requested_until' => $validated['requested_until'],
        ]);

                return redirect()->route('equipment.borrowed')            ->with('success', 'Your borrow request has been submitted successfully.');
    }

    public function borrowed()
    {
        $requests = EquipmentRequest::where('user_id', Auth::id())
            ->with(['equipment'])
            ->latest()
            ->paginate(10);

        return view('equipment.borrowed', compact('requests'));
    }

    public function history()
    {
        $history = EquipmentRequest::where('user_id', Auth::id())
            ->where('status', '!=', EquipmentRequest::STATUS_PENDING)
            ->with(['equipment'])
            ->latest()
            ->paginate(15);

        return view('equipment.history', compact('history'));
    }

    public function cancelRequest(EquipmentRequest $equipmentRequest)
    {
        if ($equipmentRequest->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$equipmentRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $equipmentRequest->delete();

        return back()->with('success', 'Equipment request cancelled successfully.');
    }

    public function return(EquipmentRequest $equipmentRequest)
    {
        if ($equipmentRequest->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$equipmentRequest->isApproved() || $equipmentRequest->returned_at) {
            return back()->with('error', 'Invalid return request.');
        }

        $equipmentRequest->update([
            'status' => EquipmentRequest::STATUS_RETURNED,
            'returned_at' => now(),
        ]);

        $equipmentRequest->equipment->update([
            'status' => Equipment::STATUS_AVAILABLE,
            'current_borrower_id' => null,
        ]);

        return back()->with('success', 'Equipment returned successfully.');
    }
} 