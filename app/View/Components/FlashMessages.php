<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FlashMessages extends Component
{
    public $types;

    /**
     * Create a new component instance.
     *
     * @param array $types Types of flash messages to check for
     */
    public function __construct($types = ['success', 'error', 'warning', 'info'])
    {
        $this->types = $types;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.flash-messages');
    }

    /**
     * Get all active flash messages
     */
    public function getFlashMessages()
    {
        $messages = [];
        
        foreach ($this->types as $type) {
            $message = session($type);
            if ($message) {
                $messages[] = [
                    'type' => $type,
                    'message' => $message
                ];
            }
        }
        
        return $messages;
    }
}
