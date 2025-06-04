<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormError extends Component
{
    public $field;
    public $message;

    /**
     * Create a new component instance.
     *
     * @param string $field The field name to check for errors
     * @param string|null $message Custom error message
     */
    public function __construct($field, $message = null)
    {
        $this->field = $field;
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form-error');
    }

    /**
     * Get the error message for the field
     */
    public function getErrorMessage()
    {
        if ($this->message) {
            return $this->message;
        }

        $errors = session('errors') ?? collect();
        return $errors->first($this->field);
    }

    /**
     * Check if there's an error for this field
     */
    public function hasError()
    {
        $errors = session('errors') ?? collect();
        return $errors->has($this->field) || $this->message;
    }
}
