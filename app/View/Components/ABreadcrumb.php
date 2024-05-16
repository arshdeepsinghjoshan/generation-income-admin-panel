<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ABreadcrumb extends Component
{
    /**
     * Create a new component instance.
     */

    public $columns;
    public $model;
    public function __construct($columns, $model = null)
    {
        $this->model = $model;
        $this->columns = $columns;
        
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.a-breadcrumb');
    }
}
