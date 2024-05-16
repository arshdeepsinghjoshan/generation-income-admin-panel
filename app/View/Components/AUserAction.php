<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AUserAction extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $states;
    public $model;
    public $attribute;
    public $title;
    public $column;
    public function __construct($model, $attribute, $states, $title = null)
    {
        $this->model = $model;
        $this->attribute = $attribute;
        $this->states = $states;
        $this->title = $title ?? 'User';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.a-user-action');
    }
}
