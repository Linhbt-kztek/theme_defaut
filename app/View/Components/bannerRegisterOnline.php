<?php

namespace App\View\Components;

use Illuminate\View\Component;

class bannerRegisterOnline extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        protected $banner
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.banner-register-online',[
            'banner' => $this->banner
        ]);
    }
}
