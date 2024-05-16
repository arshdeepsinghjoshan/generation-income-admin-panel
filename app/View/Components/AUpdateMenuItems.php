<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AUpdateMenuItems extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $model;
    public $action;

    public $text;
    public $textColor;

    public function __construct($model, $action, $text = null, $textColor = 'black')
    {
        $this->model = $model;
        $this->text = $text;
        $this->textColor = $textColor;
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.a-update-menu-items');
    }
}
