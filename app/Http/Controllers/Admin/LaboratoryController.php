<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use App\Http\Controllers\Traits\CrudOperations;
use App\Models\ComputerLaboratory;
use App\Models\LaboratoryReservation;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    use ControllerHelpers, CrudOperations;

    protected function getRoutePrefix(): string
    {
        return 'admin.laboratory';
    }

    protected function getViewPrefix(): string
    {
        return 'admin.laboratory';
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => 'required|string|unique:computer_laboratories,name',
            'room_number' => 'required|string',
            'building' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'number_of_computers' => 'required|integer|min:1',
            'status' => 'required|in:available,in_use,under_maintenance,reserved',
        ];
    }

    protected function getUpdateValidationRules($model): array
    {
        return [
            'name' => 'required|string|unique:computer_laboratories,name,' . $model->id,
            'room_number' => 'required|string',
            'building' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'number_of_computers' => 'required|integer|min:1',
            'status' => 'required|in:available,in_use,under_maintenance,reserved',
        ];
    }

    public function index()
    {
        $laboratories = ComputerLaboratory::orderBy('building')
            ->orderBy('room_number')
            ->get();

        return view($this->getViewPrefix() . '.index', compact('laboratories'));
    }

    public function create()
    {
        return view($this->getViewPrefix() . '.create');
    }

    public function store(Request $request)
    {
        return $this->handleStore($request, ComputerLaboratory::class);
    }

    public function edit(ComputerLaboratory $laboratory)
    {
        return view($this->getViewPrefix() . '.edit', compact('laboratory'));
    }

    public function update(Request $request, ComputerLaboratory $laboratory)
    {
        return $this->handleUpdate($request, $laboratory);
    }

    public function destroy(ComputerLaboratory $laboratory)
    {
        return $this->handleDestroy($laboratory);
    }

    public function updateStatus(Request $request, ComputerLaboratory $laboratory)
    {
        $validated = $this->validateRequest($request, [
            'status' => 'required|in:available,in_use,under_maintenance,reserved',
        ]);

        $laboratory->update(['status' => $validated['status']]);

        return redirect()->route('admin.laboratory.index')
            ->with('success', 'Laboratory status updated successfully.');
    }

    /**
     * Show laboratory reservation requests for admin approval
     */
    public function reservations()
    {
        $pendingRequests = LaboratoryReservation::with(['user', 'laboratory', 'approvedBy', 'rejectedBy'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        $recentRequests = LaboratoryReservation::with(['user', 'laboratory', 'approvedBy', 'rejectedBy'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        $pendingCount = $pendingRequests->count();
        $approvedTodayCount = LaboratoryReservation::approved()
            ->whereDate('updated_at', today())
            ->count();
        $rejectedTodayCount = LaboratoryReservation::rejected()
            ->whereDate('updated_at', today())
            ->count();

        return view('admin.laboratory.reservations', compact(
            'pendingRequests', 
            'recentRequests', 
            'pendingCount', 
            'approvedTodayCount', 
            'rejectedTodayCount'
        ));
    }

    /**
     * Approve a laboratory reservation request
     */
    public function approveRequest(Request $request, LaboratoryReservation $reservation)
    {
        if ($reservation->status !== LaboratoryReservation::STATUS_PENDING) {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $reservation->update([
            'status' => LaboratoryReservation::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth('admin')->id()
        ]);

        return redirect()->back()->with('success', 'Reservation request approved successfully.');
    }

    /**
     * Reject a laboratory reservation request
     */
    public function rejectRequest(Request $request, LaboratoryReservation $reservation)
    {
        $validated = $this->validateRequest($request, [
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($reservation->status !== LaboratoryReservation::STATUS_PENDING) {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $reservation->update([
            'status' => LaboratoryReservation::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now(),
            'rejected_by' => auth('admin')->id()
        ]);

        return redirect()->back()->with('success', 'Reservation request rejected.');
    }
}