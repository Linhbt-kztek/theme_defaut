<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SidebarProfileComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public function __construct(
        protected $customer
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.sidebar-profile-component', ['customer' => $this->customer ?? null]);
    }
}
