<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ARelationGrid extends Component
{
    /**
     * Create a new component instance.
     */
    public $id;
    public $modelId;
    public $url;
    public $columns;
    public $relation;
    public $buttons;
    public $model;

    public $filterButtonId;

    public $customfilterIds;


    public function __construct($columns, $model,  $id, $relation, $buttons = [], $filterButtonId = null, $customfilterIds = [],$url = 'relation/get-list',)
    {
        $this->columns = $columns;
        $this->id = $id;
        $this->url = url($url);
        $this->modelId = $model->id;
        $this->model = $model;
        $this->buttons = $buttons;
        $this->relation = $relation;
        $this->filterButtonId = $filterButtonId;
        $this->customfilterIds = $customfilterIds;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.a-relation-grid');
    }
}
