<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Base service class providing common functionality
 */
abstract class BaseService
{
    /**
     * Apply common filters to a query
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                if (method_exists($this, 'filter' . ucfirst($key))) {
                    $this->{'filter' . ucfirst($key)}($query, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }
        
        return $query;
    }
    
    /**
     * Apply search to specified columns
     */
    protected function applySearch(Builder $query, string $search, array $columns): Builder
    {
        if (empty($search)) {
            return $query;
        }
        
        return $query->where(function ($q) use ($search, $columns) {
            foreach ($columns as $column) {
                if (str_contains($column, '.')) {
                    // Handle relationship columns
                    [$relation, $field] = explode('.', $column, 2);
                    $q->orWhereHas($relation, function ($subQuery) use ($field, $search) {
                        $subQuery->where($field, 'LIKE', "%{$search}%");
                    });
                } else {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            }
        });
    }
    
    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, string $sortBy = 'created_at', string $sortDirection = 'desc'): Builder
    {
        $allowedDirections = ['asc', 'desc'];
        $direction = in_array(strtolower($sortDirection), $allowedDirections) ? $sortDirection : 'desc';
        
        return $query->orderBy($sortBy, $direction);
    }
    
    /**
     * Get paginated results with common parameters
     */
    protected function getPaginatedResults(Builder $query, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }
    
    /**
     * Build query with common operations
     */
    protected function buildQuery(
        Builder $query, 
        array $filters = [], 
        string $search = '', 
        array $searchColumns = [],
        string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): Builder {
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }
        
        if (!empty($search) && !empty($searchColumns)) {
            $query = $this->applySearch($query, $search, $searchColumns);
        }
        
        return $this->applySorting($query, $sortBy, $sortDirection);
    }
    
    /**
     * Extract common filter parameters from request
     */
    protected function extractFilters(Request $request, array $allowedFilters = []): array
    {
        $filters = [];
        
        foreach ($allowedFilters as $filter) {
            if ($request->has($filter)) {
                $filters[$filter] = $request->get($filter);
            }
        }
        
        return $filters;
    }
    
    /**
     * Validate model exists and user has permission
     */
    protected function validateModelAccess(Model $model, $user = null, string $permission = null): bool
    {
        if (!$model->exists) {
            return false;
        }
        
        if ($user && method_exists($model, 'belongsToUser')) {
            return $model->belongsToUser($user);
        }
        
        return true;
    }
    
    /**
     * Log service action
     */
    protected function logAction(string $action, Model $model = null, array $data = []): void
    {
        $logData = [
            'service' => static::class,
            'action' => $action,
            'model' => $model ? get_class($model) . ':' . $model->id : null,
            'data' => $data,
            'timestamp' => now()
        ];
        
        Log::info('Service Action', $logData);
    }

    /**
     * Get filtered data with pagination
     */
    public function getFilteredData(Request $request, array $with = [], array $searchFields = [], array $filters = [], int $perPage = 15)
    {
        $model = $this->getModel();
        $query = $model::query();
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        // Apply search
        if ($request->has('search') && $request->search != '') {
            $query = $this->applySearch($query, $request->search, $searchFields);
        }
        
        // Apply filters
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->where($field, $value);
            }
        }
        
        $result = $query->latest()->paginate($perPage);
        
        // Keep filters when paginating
        $result->appends($request->only(array_merge(['search'], array_keys($filters))));
        
        return $result;
    }

    /**
     * Get the model class - must be implemented by child classes
     */
    abstract protected function getModel();
}
