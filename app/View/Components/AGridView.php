<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AGridView extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $id;
    public $url;
    public $columns;
    public $title;
    public $buttons;
    public $model;

    public $filterButtonId;

    public $customfilterIds;


    public function __construct($url, $columns, $id, $model = '', $title = null, $buttons = [], $filterButtonId = null, $customfilterIds = [])
    {
        $this->url = url($url);
        $this->columns = $columns;
        $this->id = $id;
        $this->model = $model;
        $this->buttons = $buttons;
        $this->title = $title ?? 'User';
        $this->filterButtonId = $filterButtonId;
        $this->customfilterIds = $customfilterIds;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.a-grid-view');
    }
}
