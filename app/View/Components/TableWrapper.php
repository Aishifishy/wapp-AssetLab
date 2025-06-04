<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TableWrapper extends Component
{
    public $title;
    public $description;
    public $showSearch;
    public $searchPlaceholder;
    public $responsive;
    public $tableClass;
    public $containerClass;
    public $headerClass;

    /**
     * Create a new component instance.
     *
     * @param string|null $title Table title
     * @param string|null $description Table description
     * @param bool $showSearch Whether to show search input
     * @param string $searchPlaceholder Placeholder for search input
     * @param bool $responsive Whether table should be responsive
     * @param string $tableClass Additional CSS classes for table
     * @param string $containerClass Additional CSS classes for container
     * @param string $headerClass Additional CSS classes for header section
     */
    public function __construct(
        ?string $title = null,
        ?string $description = null,
        bool $showSearch = false,
        string $searchPlaceholder = 'Search...',
        bool $responsive = true,
        string $tableClass = '',
        string $containerClass = '',
        string $headerClass = ''
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->showSearch = $showSearch;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->responsive = $responsive;
        $this->tableClass = $tableClass;
        $this->containerClass = $containerClass;
        $this->headerClass = $headerClass;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.table-wrapper');
    }
}
