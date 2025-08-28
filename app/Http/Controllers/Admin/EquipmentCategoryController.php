<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use App\Http\Controllers\Traits\CrudOperations;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class EquipmentCategoryController extends Controller
{
    use ControllerHelpers, CrudOperations;

    protected function getRoutePrefix(): string
    {
        return 'admin.equipment.categories';
    }

    protected function getViewPrefix(): string
    {
        return 'admin.equipment.categories';
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:equipment_categories',
            'description' => 'nullable|string',
        ];
    }

    protected function getUpdateValidationRules($model): array
    {
        return [
            'name' => 'required|string|max:255|unique:equipment_categories,name,' . $model->id,
            'description' => 'nullable|string',
        ];
    }

    protected function canDelete($model): array
    {
        if ($model->equipment()->exists()) {
            return [
                'can_delete' => false,
                'message' => 'Cannot delete equipment type that has equipment assigned to it.'
            ];
        }

        return ['can_delete' => true, 'message' => ''];
    }

    public function index()
    {
        $categories = EquipmentCategory::withCount('equipment')->latest()->get();
        return view($this->getViewPrefix() . '.index', compact('categories'));
    }

    public function create()
    {
        return view($this->getViewPrefix() . '.create');
    }

    public function store(Request $request)
    {
        return $this->handleStore($request, EquipmentCategory::class);
    }

    public function edit(EquipmentCategory $category)
    {
        return view($this->getViewPrefix() . '.edit', compact('category'));
    }

    public function update(Request $request, EquipmentCategory $category)
    {
        return $this->handleUpdate($request, $category);
    }

    public function destroy(EquipmentCategory $category)
    {
        return $this->handleDestroy($category);
    }
}
