<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class EquipmentCategoryController extends Controller
{
    public function index()
    {
        $categories = EquipmentCategory::withCount('equipment')->latest()->get();
        return view('admin.equipment.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.equipment.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:equipment_categories',
            'description' => 'nullable|string',
        ]);

        EquipmentCategory::create($validated);

        return redirect()->route('admin.equipment.categories.index')
            ->with('success', 'Equipment type created successfully.');
    }

    public function edit(EquipmentCategory $category)
    {
        return view('admin.equipment.categories.edit', compact('category'));
    }

    public function update(Request $request, EquipmentCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:equipment_categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('admin.equipment.categories.index')
            ->with('success', 'Equipment type updated successfully.');
    }

    public function destroy(EquipmentCategory $category)
    {
        if ($category->equipment()->exists()) {
            return back()->with('error', 'Cannot delete equipment type that has equipment assigned to it.');
        }

        $category->delete();

        return back()->with('success', 'Equipment type deleted successfully.');
    }
}
