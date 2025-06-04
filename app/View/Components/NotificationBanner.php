<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NotificationBanner extends Component
{
    public $type;
    public $message;
    public $title;
    public $actions;
    public $dismissible;
    public $persistent;

    /**
     * Create a new component instance.
     *
     * @param string $type Banner type (success, error, warning, info)
     * @param string|null $message Banner message
     * @param string|null $title Banner title
     * @param array $actions Action buttons
     * @param bool $dismissible Whether banner can be dismissed
     * @param bool $persistent Whether to show banner even without session data
     */
    public function __construct(
        $type = 'info',
        $message = null,
        $title = null,
        $actions = [],
        $dismissible = true,
        $persistent = false
    ) {
        $this->type = $type;
        $this->message = $message;
        $this->title = $title;
        $this->actions = $actions;
        $this->dismissible = $dismissible;
        $this->persistent = $persistent;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.notification-banner');
    }

    /**
     * Get the CSS classes for the banner type
     */
    public function getBannerClasses()
    {
        return match($this->type) {
            'success' => 'bg-green-50 border-green-200',
            'error', 'danger' => 'bg-red-50 border-red-200',
            'warning' => 'bg-yellow-50 border-yellow-200',
            'info' => 'bg-blue-50 border-blue-200',
            default => 'bg-gray-50 border-gray-200',
        };
    }

    /**
     * Get the text color classes for the banner type
     */
    public function getTextClasses()
    {
        return match($this->type) {
            'success' => 'text-green-800',
            'error', 'danger' => 'text-red-800',
            'warning' => 'text-yellow-800',
            'info' => 'text-blue-800',
            default => 'text-gray-800',
        };
    }

    /**
     * Get the icon color classes for the banner type
     */
    public function getIconClasses()
    {
        return match($this->type) {
            'success' => 'text-green-400',
            'error', 'danger' => 'text-red-400',
            'warning' => 'text-yellow-400',
            'info' => 'text-blue-400',
            default => 'text-gray-400',
        };
    }

    /**
     * Get the default icon for the banner type
     */
    public function getDefaultIcon()
    {
        return match($this->type) {
            'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'error', 'danger' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.084 16.5c-.77.833.192 2.5 1.732 2.5z',
            'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    /**
     * Get the session message for this banner type
     */
    public function getSessionMessage()
    {
        if ($this->message) {
            return $this->message;
        }

        return match($this->type) {
            'success' => session('success'),
            'error', 'danger' => session('error'),
            'warning' => session('warning'),
            'info' => session('info'),
            default => null,
        };
    }

    /**
     * Check if the banner should be shown
     */
    public function shouldShow()
    {
        return $this->persistent || $this->getSessionMessage() || $this->message;
    }
}
