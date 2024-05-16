<?php

namespace App\View\Components;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class ACartItems extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $model;
    public function __construct()
    {
        $this->model = $this->getCartItems();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.a-cart-items');
    }
    public static function getCartItems()
    {
        $model = [];
        if (Auth::check()) {
            $model = Cart::my('user_id')
                ->orderBy('id', 'desc')
                ->take(3)
                ->get();
        }
        return $model;
    }
}
