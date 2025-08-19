<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatusBadge extends Component
{
    public string $status;
    public string $type;
    public array $statusConfig;

    /**
     * Create a new component instance.
     *
     * @param string $status The status value to display
     * @param string $type The type of status badge (equipment|laboratory|reservation|request)
     */
    public function __construct(string $status, string $type = 'equipment')
    {
        $this->status = $status;
        $this->type = $type;
        $this->statusConfig = $this->getStatusConfig();
    }

    /**
     * Get status configuration based on type
     */
    private function getStatusConfig(): array
    {
        $configs = [
            'equipment' => [
                'available' => ['bg-green-100 text-green-800', 'Available'],
                'borrowed' => ['bg-yellow-100 text-yellow-800', 'Borrowed'],
                'unavailable' => ['bg-red-100 text-red-800', 'Unavailable'],
                'maintenance' => ['bg-red-100 text-red-800', 'Under Maintenance'],
            ],
            'laboratory' => [
                'available' => ['bg-green-100 text-green-800', 'Available'],
                'in_use' => ['bg-yellow-100 text-yellow-800', 'In Use'],
                'under_maintenance' => ['bg-red-100 text-red-800', 'Under Maintenance'],
                'reserved' => ['bg-blue-100 text-blue-800', 'Reserved'],
            ],
            'reservation' => [
                'pending' => ['bg-yellow-100 text-yellow-800', 'Pending'],
                'approved' => ['bg-green-100 text-green-800', 'Approved'],
                'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
                'cancelled' => ['bg-gray-100 text-gray-800', 'Cancelled'],
            ],
            'request' => [
                'pending' => ['bg-yellow-100 text-yellow-800', 'Pending'],
                'approved' => ['bg-green-100 text-green-800', 'Approved'],
                'checked_out' => ['bg-blue-100 text-blue-800', 'Borrowed'],
                'returned' => ['bg-purple-100 text-purple-800', 'Returned'],
                'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
                'cancelled' => ['bg-gray-100 text-gray-800', 'Cancelled'],
            ],
        ];

        return $configs[$this->type] ?? $configs['equipment'];
    }

    /**
     * Get the badge CSS classes for the current status
     */
    public function getBadgeClasses(): string
    {
        $config = $this->statusConfig[$this->status] ?? ['bg-gray-100 text-gray-800', ucfirst($this->status)];
        return $config[0];
    }

    /**
     * Get the formatted display text for the current status
     */
    public function getDisplayText(): string
    {
        $config = $this->statusConfig[$this->status] ?? ['bg-gray-100 text-gray-800', ucfirst(str_replace('_', ' ', $this->status))];
        return $config[1];
    }

    /**
     * Check if the current status indicates an overdue state
     */
    public function isOverdue(): bool
    {
        return in_array($this->status, ['overdue', 'late']);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.status-badge');
    }
}
