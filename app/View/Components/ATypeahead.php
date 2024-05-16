<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ATypeahead extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $model;
    public $column;
    public function __construct($column,$model = null )
    {
        $this->column = $column;
        $this->model = $model;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.a-typeahead');
    }
}
