<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerHelpers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Base CRUD Controller for Admin controllers
 * Provides common CRUD functionality that can be extended or overridden
 */
abstract class BaseCrudController extends Controller
{
    use ControllerHelpers;

    /**
     * The model class name (must be implemented by child classes)
     */
    protected abstract function getModelClass(): string;

    /**
     * The route prefix for redirects (must be implemented by child classes)
     */
    protected abstract function getRoutePrefix(): string;

    /**
     * The view path prefix (must be implemented by child classes)
     */
    protected abstract function getViewPrefix(): string;

    /**
     * Get validation rules for store operation
     */
    protected abstract function getStoreValidationRules(): array;

    /**
     * Get validation rules for update operation
     */
    protected abstract function getUpdateValidationRules(Model $model): array;

    /**
     * Get the model instance
     */
    protected function getModel(): string
    {
        return $this->getModelClass();
    }

    /**
     * Apply search filters to query (can be overridden)
     */
    protected function applySearchFilters($query, Request $request)
    {
        // Default implementation - can be overridden by child classes
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        return $query;
    }

    /**
     * Get additional data for index view (can be overridden)
     */
    protected function getIndexData(Request $request): array
    {
        return [];
    }

    /**
     * Get additional data for create view (can be overridden)
     */
    protected function getCreateData(): array
    {
        return [];
    }

    /**
     * Get additional data for edit view (can be overridden)
     */
    protected function getEditData(Model $model): array
    {
        return [];
    }

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
    protected function prepareUpdateData(array $validated, Model $model): array
    {
        return $validated;
    }

    /**
     * Handle after store operations (can be overridden)
     */
    protected function afterStore(Model $model, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Handle after update operations (can be overridden)
     */
    protected function afterUpdate(Model $model, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Check if model can be deleted (can be overridden)
     */
    protected function canDelete(Model $model): array
    {
        return ['can_delete' => true, 'message' => ''];
    }

    /**
     * Handle after delete operations (can be overridden)
     */
    protected function afterDelete(Model $model): void
    {
        // Override in child classes if needed
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $modelClass = $this->getModel();
        $query = $modelClass::query();

        // Apply search filters
        $query = $this->applySearchFilters($query, $request);

        // Apply pagination
        $paginationParams = $this->getPaginationParams($request);
        $items = $query->orderBy($paginationParams['sort_by'], $paginationParams['sort_direction'])
                      ->paginate($paginationParams['per_page']);

        // Get additional data
        $additionalData = $this->getIndexData($request);

        $viewData = array_merge([
            'items' => $items,
        ], $additionalData);

        return view($this->getViewPrefix() . '.index', $viewData);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $additionalData = $this->getCreateData();
        return view($this->getViewPrefix() . '.create', $additionalData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request, $this->getStoreValidationRules());
        $data = $this->prepareStoreData($validated);

        $modelClass = $this->getModel();
        $model = $modelClass::create($data);

        $this->afterStore($model, $data);

        $resourceName = class_basename($modelClass);
        return redirect()->route($this->getRoutePrefix() . '.index')
            ->with('success', $resourceName . ' created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($model)
    {
        return view($this->getViewPrefix() . '.show', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     * This method signature allows for route model binding compatibility
     */
    public function edit($model)
    {
        $additionalData = $this->getEditData($model);
        $viewData = array_merge(['model' => $model], $additionalData);
        return view($this->getViewPrefix() . '.edit', $viewData);
    }

    /**
     * Update the specified resource in storage.
     * This method signature allows for route model binding compatibility
     */
    public function update(Request $request, $model)
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
     * Remove the specified resource from storage.
     * This method signature allows for route model binding compatibility
     */
    public function destroy($model)
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
