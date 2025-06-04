<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TableHeader extends Component
{
    public $columns;
    public $sortable;
    public $currentSort;
    public $currentDirection;
    public $headerClass;
    public $actions;
    public $actionColumnTitle;

    /**
     * Create a new component instance.
     *
     * @param array $columns Array of column definitions
     * @param bool $sortable Whether columns should be sortable
     * @param string|null $currentSort Current sort column
     * @param string $currentDirection Current sort direction
     * @param string $headerClass Additional CSS classes for header
     * @param bool $actions Whether to include actions column
     * @param string $actionColumnTitle Title for actions column
     */
    public function __construct(
        array $columns = [],
        bool $sortable = false,
        ?string $currentSort = null,
        string $currentDirection = 'asc',
        string $headerClass = '',
        bool $actions = false,
        string $actionColumnTitle = 'Actions'
    ) {
        $this->columns = $columns;
        $this->sortable = $sortable;
        $this->currentSort = $currentSort;
        $this->currentDirection = $currentDirection;
        $this->headerClass = $headerClass;
        $this->actions = $actions;
        $this->actionColumnTitle = $actionColumnTitle;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.table-header');
    }

    /**
     * Get sort URL for a column
     *
     * @param string $column
     * @return string
     */
    public function getSortUrl($column)
    {
        $direction = 'asc';
        if ($this->currentSort === $column && $this->currentDirection === 'asc') {
            $direction = 'desc';
        }

        return request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $direction
        ]);
    }

    /**
     * Check if column is currently sorted
     *
     * @param string $column
     * @return bool
     */
    public function isCurrentSort($column)
    {
        return $this->currentSort === $column;
    }

    /**
     * Get sort icon for column
     *
     * @param string $column
     * @return string
     */
    public function getSortIcon($column)
    {
        if (!$this->isCurrentSort($column)) {
            return 'fas fa-sort text-gray-400';
        }

        return $this->currentDirection === 'asc' 
            ? 'fas fa-sort-up text-blue-500' 
            : 'fas fa-sort-down text-blue-500';
    }
}
