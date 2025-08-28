<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Common model scopes for filtering and searching
 */
trait Filterable
{
    /**
     * Scope for active records
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope for recent records
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
    
    /**
     * Scope for search functionality
     */
    public function scopeSearch(Builder $query, string $term, array $columns = []): Builder
    {
        if (empty($term)) {
            return $query;
        }
        
        $searchColumns = !empty($columns) ? $columns : $this->getSearchableColumns();
        
        return $query->where(function ($q) use ($term, $searchColumns) {
            foreach ($searchColumns as $column) {
                if (str_contains($column, '.')) {
                    // Handle relationship columns
                    [$relation, $field] = explode('.', $column, 2);
                    $q->orWhereHas($relation, function ($subQuery) use ($field, $term) {
                        $subQuery->where($field, 'LIKE', "%{$term}%");
                    });
                } else {
                    $q->orWhere($column, 'LIKE', "%{$term}%");
                }
            }
        });
    }
    
    /**
     * Scope for filtering by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange(Builder $query, string $startDate, string $endDate, string $column = 'created_at'): Builder
    {
        return $query->whereBetween($column, [$startDate, $endDate]);
    }
    
    /**
     * Scope for user-owned records
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Scope for pending status records
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Scope for approved status records
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
    
    /**
     * Scope for rejected status records
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }
    
    /**
     * Scope for currently borrowed/not returned equipment
     */
    public function scopeCurrentlyBorrowed(Builder $query): Builder
    {
        return $query->where('status', 'approved')->whereNull('returned_at');
    }
    
    /**
     * Scope for returned equipment
     */
    public function scopeReturned(Builder $query): Builder
    {
        return $query->whereNotNull('returned_at');
    }
    
    /**
     * Scope for upcoming returns (within specified days)
     */
    public function scopeUpcomingReturns(Builder $query, int $days = 7): Builder
    {
        return $query->where('status', 'approved')
                    ->whereNull('returned_at')
                    ->where('requested_until', '>=', now())
                    ->where('requested_until', '<=', now()->addDays($days));
    }
    
    /**
     * Get searchable columns for the model
     */
    protected function getSearchableColumns(): array
    {
        return property_exists($this, 'searchable') ? $this->searchable : ['name'];
    }
    
    /**
     * Apply multiple filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $method = 'filter' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->$method($query, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }
        
        return $query;
    }
}
