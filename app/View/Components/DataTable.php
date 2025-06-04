<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DataTable extends Component
{
    public $headers;
    public $data;
    public $actions;
    public $showSearch;
    public $showPagination;
    public $tableId;
    public $responsive;
    public $sortable;
    public $emptyMessage;
    public $tableClass;
    public $headerClass;
    public $bodyClass;
    public $rowClass;
    public $cellClass;
    public $actionColumnTitle;

    /**
     * Create a new component instance.
     *
     * @param array $headers Array of header definitions
     * @param array $data Array of data rows
     * @param array $actions Array of action definitions
     * @param bool $showSearch Whether to show search functionality
     * @param bool $showPagination Whether to show pagination
     * @param string $tableId Unique table ID
     * @param bool $responsive Whether table should be responsive
     * @param bool $sortable Whether columns should be sortable
     * @param string $emptyMessage Message to show when no data
     * @param string $tableClass Additional CSS classes for table
     * @param string $headerClass Additional CSS classes for header
     * @param string $bodyClass Additional CSS classes for body
     * @param string $rowClass Additional CSS classes for rows
     * @param string $cellClass Additional CSS classes for cells
     * @param string $actionColumnTitle Title for actions column
     */
    public function __construct(
        array $headers = [],
        array $data = [],
        array $actions = [],
        bool $showSearch = false,
        bool $showPagination = false,
        string $tableId = 'data-table',
        bool $responsive = true,
        bool $sortable = false,
        string $emptyMessage = 'No data available',
        string $tableClass = '',
        string $headerClass = '',
        string $bodyClass = '',
        string $rowClass = '',
        string $cellClass = '',
        string $actionColumnTitle = 'Actions'
    ) {
        $this->headers = $headers;
        $this->data = $data;
        $this->actions = $actions;
        $this->showSearch = $showSearch;
        $this->showPagination = $showPagination;
        $this->tableId = $tableId;
        $this->responsive = $responsive;
        $this->sortable = $sortable;
        $this->emptyMessage = $emptyMessage;
        $this->tableClass = $tableClass;
        $this->headerClass = $headerClass;
        $this->bodyClass = $bodyClass;
        $this->rowClass = $rowClass;
        $this->cellClass = $cellClass;
        $this->actionColumnTitle = $actionColumnTitle;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.data-table');
    }

    /**
     * Get the value for a specific column from a data row
     *
     * @param mixed $row
     * @param array $column
     * @return mixed
     */
    public function getColumnValue($row, $column)
    {
        $key = $column['key'] ?? '';
        
        if (is_array($row)) {
            return $row[$key] ?? '';
        }
        
        if (is_object($row)) {
            // Handle nested properties with dot notation
            if (strpos($key, '.') !== false) {
                $keys = explode('.', $key);
                $value = $row;
                foreach ($keys as $nestedKey) {
                    if (is_object($value) && property_exists($value, $nestedKey)) {
                        $value = $value->{$nestedKey};
                    } elseif (is_array($value) && array_key_exists($nestedKey, $value)) {
                        $value = $value[$nestedKey];
                    } else {
                        return '';
                    }
                }
                return $value;
            }
            
            return property_exists($row, $key) ? $row->{$key} : '';
        }
        
        return '';
    }

    /**
     * Format the column value based on column type
     *
     * @param mixed $value
     * @param array $column
     * @param mixed $row
     * @return string
     */
    public function formatColumnValue($value, $column, $row = null)
    {
        $type = $column['type'] ?? 'text';
        
        switch ($type) {
            case 'status':
                return $this->formatStatus($value, $column);
            case 'date':
                return $this->formatDate($value, $column);
            case 'datetime':
                return $this->formatDateTime($value, $column);
            case 'currency':
                return $this->formatCurrency($value, $column);
            case 'number':
                return $this->formatNumber($value, $column);
            case 'badge':
                return $this->formatBadge($value, $column);
            case 'link':
                return $this->formatLink($value, $column, $row);
            case 'image':
                return $this->formatImage($value, $column);
            case 'boolean':
                return $this->formatBoolean($value, $column);
            case 'html':
                return $value; // Raw HTML
            default:
                return htmlspecialchars($value ?? '');
        }
    }

    /**
     * Format status values with appropriate styling
     */
    private function formatStatus($value, $column)
    {
        $statusConfig = $column['status_config'] ?? [];
        $defaultClass = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
        
        $class = $statusConfig[$value] ?? $defaultClass;
        
        return '<span class="' . $class . '">' . ucfirst($value) . '</span>';
    }

    /**
     * Format date values
     */
    private function formatDate($value, $column)
    {
        if (!$value) return '';
        
        $format = $column['format'] ?? 'Y-m-d';
        
        if ($value instanceof \Carbon\Carbon) {
            return $value->format($format);
        }
        
        try {
            return \Carbon\Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Format datetime values
     */
    private function formatDateTime($value, $column)
    {
        if (!$value) return '';
        
        $format = $column['format'] ?? 'Y-m-d H:i:s';
        
        if ($value instanceof \Carbon\Carbon) {
            return $value->format($format);
        }
        
        try {
            return \Carbon\Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Format currency values
     */
    private function formatCurrency($value, $column)
    {
        $symbol = $column['symbol'] ?? '$';
        $decimals = $column['decimals'] ?? 2;
        
        return $symbol . number_format($value, $decimals);
    }

    /**
     * Format number values
     */
    private function formatNumber($value, $column)
    {
        $decimals = $column['decimals'] ?? 0;
        
        return number_format($value, $decimals);
    }

    /**
     * Format badge values
     */
    private function formatBadge($value, $column)
    {
        $badgeClass = $column['badge_class'] ?? 'bg-blue-100 text-blue-800';
        
        return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $badgeClass . '">' . $value . '</span>';
    }

    /**
     * Format link values
     */
    private function formatLink($value, $column, $row)
    {
        $url = $column['url'] ?? '#';
        $target = $column['target'] ?? '_self';
        $class = $column['link_class'] ?? 'text-blue-600 hover:text-blue-900';
        
        // Replace placeholders in URL with row data
        if ($row && strpos($url, '{') !== false) {
            preg_match_all('/\{(\w+)\}/', $url, $matches);
            foreach ($matches[1] as $placeholder) {
                $replacement = is_object($row) ? ($row->{$placeholder} ?? '') : ($row[$placeholder] ?? '');
                $url = str_replace('{' . $placeholder . '}', $replacement, $url);
            }
        }
        
        return '<a href="' . $url . '" target="' . $target . '" class="' . $class . '">' . $value . '</a>';
    }

    /**
     * Format image values
     */
    private function formatImage($value, $column)
    {
        if (!$value) return '';
        
        $alt = $column['alt'] ?? '';
        $class = $column['image_class'] ?? 'h-10 w-10 rounded-full';
        
        return '<img src="' . $value . '" alt="' . $alt . '" class="' . $class . '">';
    }

    /**
     * Format boolean values
     */
    private function formatBoolean($value, $column)
    {
        $trueText = $column['true_text'] ?? 'Yes';
        $falseText = $column['false_text'] ?? 'No';
        $trueClass = $column['true_class'] ?? 'text-green-600';
        $falseClass = $column['false_class'] ?? 'text-red-600';
        
        if ($value) {
            return '<span class="' . $trueClass . '">' . $trueText . '</span>';
        } else {
            return '<span class="' . $falseClass . '">' . $falseText . '</span>';
        }
    }
}
