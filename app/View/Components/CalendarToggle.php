<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CalendarToggle extends Component
{
    public string $calendarContentId;
    public string $tableContentId;
    public string $defaultView;
    public bool $showIcons;

    /**
     * Create a new component instance.
     *
     * @param string $calendarContentId ID of the calendar content element
     * @param string $tableContentId ID of the table content element
     * @param string $defaultView Default view to show ('calendar' or 'table')
     * @param bool $showIcons Whether to show icons in the buttons
     */
    public function __construct(
        string $calendarContentId = 'calendar-content', 
        string $tableContentId = 'table-content',
        string $defaultView = 'calendar',
        bool $showIcons = true
    ) {
        $this->calendarContentId = $calendarContentId;
        $this->tableContentId = $tableContentId;
        $this->defaultView = $defaultView;
        $this->showIcons = $showIcons;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.calendar-toggle');
    }
}
