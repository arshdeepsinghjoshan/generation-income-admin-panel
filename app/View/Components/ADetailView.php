<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ADetailView extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $model;
    public $column;
    public $type;
    public $icon;
    public function __construct($model, $column, $type = 'single', $icon = true)
    {
        $this->column = $column;
        $this->model = $model;
        $this->type = $type;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $array = array_filter($this->column, function ($value) {
            // If $value is an array and has 'visible' key with true value, keep it
            if (is_array($value)) {
                if (!isset($value['visible']))
                    return true;
                if (isset($value['visible']) && $value['visible'] === true)
                    return true;
            }
            // If $value is not an array, keep it
            return !is_array($value);
        });
        $this->column = array_values($array);
        return view('components.a-detail-view');
    }
}
