<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $message;
    public $dismissible;
    public $icon;
    public $title;
    public $persistent;

    /**
     * Create a new component instance.
     *
     * @param string $type The alert type (success, error, warning, info)
     * @param string|null $message The alert message
     * @param bool $dismissible Whether the alert can be dismissed
     * @param string|null $icon Custom icon class
     * @param string|null $title Alert title
     * @param bool $persistent Whether to show alert even without session data
     */
    public function __construct(
        $type = 'info', 
        $message = null, 
        $dismissible = true, 
        $icon = null, 
        $title = null,
        $persistent = false
    ) {
        $this->type = $type;
        $this->message = $message;
        $this->dismissible = $dismissible;
        $this->icon = $icon;
        $this->title = $title;
        $this->persistent = $persistent;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.alert');
    }

    /**
     * Get the CSS classes for the alert type
     */
    public function getAlertClasses()
    {
        return match($this->type) {
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error', 'danger' => 'bg-red-100 border-red-400 text-red-700',
            'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'info' => 'bg-blue-100 border-blue-400 text-blue-700',
            default => 'bg-gray-100 border-gray-400 text-gray-700',
        };
    }

    /**
     * Get the default icon for the alert type
     */
    public function getDefaultIcon()
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match($this->type) {
            'success' => 'fas fa-check-circle',
            'error', 'danger' => 'fas fa-exclamation-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'info' => 'fas fa-info-circle',
            default => 'fas fa-info-circle',
        };
    }

    /**
     * Get the session message for this alert type
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
     * Check if the alert should be shown
     */
    public function shouldShow()
    {
        return $this->persistent || $this->getSessionMessage() || $this->message;
    }
}
