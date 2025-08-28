<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * CRUD Operations Trait
 * Provides reusable CRUD functionality for Admin controllers
 */
trait CrudOperations
{
    /**
     * Get validation rules for store operation (must be implemented by controller)
     */
    protected abstract function getStoreValidationRules(): array;

    /**
     * Get validation rules for update operation (must be implemented by controller)
     */
    protected abstract function getUpdateValidationRules($model): array;

    /**
     * Get the route prefix for redirects (must be implemented by controller)
     */
    protected abstract function getRoutePrefix(): string;

    /**
     * Get the view path prefix (must be implemented by controller)
     */
    protected abstract function getViewPrefix(): string;

    /**
     * Prepare data before store (can be overridden)
     */
    protected function prepareStoreData(array $validated): array
    {
        return $validated;
    }

    /**
     * Prepare data before update (can be overridden)
     */
    protected function prepareUpdateData(array $validated, $model): array
    {
        return $validated;
    }

    /**
     * Handle after store operations (can be overridden)
     */
    protected function afterStore($model, array $data): void
    {
        // Override in controllers if needed
    }

    /**
     * Handle after update operations (can be overridden)
     */
    protected function afterUpdate($model, array $data): void
    {
        // Override in controllers if needed
    }

    /**
     * Check if model can be deleted (can be overridden)
     */
    protected function canDelete($model): array
    {
        return ['can_delete' => true, 'message' => ''];
    }

    /**
     * Handle after delete operations (can be overridden)
     */
    protected function afterDelete($model): void
    {
        // Override in controllers if needed
    }

    /**
     * Store a newly created resource using CRUD trait.
     */
    protected function handleStore(Request $request, string $modelClass)
    {
        $validated = $this->validateRequest($request, $this->getStoreValidationRules());
        $data = $this->prepareStoreData($validated);

        $model = $modelClass::create($data);

        $this->afterStore($model, $data);

        $resourceName = class_basename($modelClass);
        return redirect()->route($this->getRoutePrefix() . '.index')
            ->with('success', $resourceName . ' created successfully.');
    }

    /**
     * Update the specified resource using CRUD trait.
     */
    protected function handleUpdate(Request $request, $model)
    {
        $validated = $this->validateRequest($request, $this->getUpdateValidationRules($model));
        $data = $this->prepareUpdateData($validated, $model);

        $model->update($data);

        $this->afterUpdate($model, $data);

        $resourceName = class_basename(get_class($model));
        return redirect()->back()
            ->with('success', $resourceName . ' updated successfully.');
    }

    /**
     * Remove the specified resource using CRUD trait.
     */
    protected function handleDestroy($model)
    {
        $deleteCheck = $this->canDelete($model);
        
        if (!$deleteCheck['can_delete']) {
            return redirect()->back()->with('error', $deleteCheck['message']);
        }

        $resourceName = class_basename(get_class($model));
        $model->delete();

        $this->afterDelete($model);

        return redirect()->route($this->getRoutePrefix() . '.index')
            ->with('success', $resourceName . ' deleted successfully.');
    }
}
