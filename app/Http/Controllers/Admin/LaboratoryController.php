<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComputerLaboratory;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    public function index()
    {
        $laboratories = ComputerLaboratory::orderBy('building')
            ->orderBy('room_number')
            ->get();

        return view('admin.laboratory.index', compact('laboratories'));
    }

    public function create()
    {
        return view('admin.laboratory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:computer_laboratories,name',
            'room_number' => 'required|string',
            'building' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'number_of_computers' => 'required|integer|min:1',
            'status' => 'required|in:available,in_use,under_maintenance,reserved',
        ]);

        ComputerLaboratory::create($validated);

        return redirect()->route('admin.laboratory.index')
            ->with('success', 'Laboratory created successfully.');
    }

    public function edit(ComputerLaboratory $laboratory)
    {
        return view('admin.laboratory.edit', compact('laboratory'));
    }

    public function update(Request $request, ComputerLaboratory $laboratory)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:computer_laboratories,name,' . $laboratory->id,
            'room_number' => 'required|string',
            'building' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'number_of_computers' => 'required|integer|min:1',
            'status' => 'required|in:available,in_use,under_maintenance,reserved',
        ]);

        $laboratory->update($validated);

        return redirect()->route('admin.laboratory.index')
            ->with('success', 'Laboratory updated successfully.');
    }

    public function destroy(ComputerLaboratory $laboratory)
    {
        $laboratory->delete();

        return redirect()->route('admin.laboratory.index')
            ->with('success', 'Laboratory deleted successfully.');
    }

    public function updateStatus(Request $request, ComputerLaboratory $laboratory)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,in_use,under_maintenance,reserved',
        ]);

        $laboratory->update(['status' => $validated['status']]);

        return redirect()->route('admin.laboratory.index')
            ->with('success', 'Laboratory status updated successfully.');
    }
} 