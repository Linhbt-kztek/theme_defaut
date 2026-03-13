<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardTabListComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        protected $active
    ) {

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard-tab-list-component', [
            'active' => $this->active
        ]);
    }
}
