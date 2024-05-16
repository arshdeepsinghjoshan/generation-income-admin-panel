<?php

namespace App\View\Components;

use App\Models\Cart;
use Illuminate\View\Component;

class APincodeMadal extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public $shippingMethods = [];

    public $isPickUP = false;
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $this->shippingMethods = (new Cart())->getShippingMethodOptions();
        if (isset($_COOKIE['shipping_method']) && $_COOKIE['shipping_method'] == Cart::SHIPPING_METHOD_PICKUP)
            $this->getWarehouse();

        return view('components.a-pincode-madal');
    }

    public function getWarehouse()
    {
        $this->isPickUP = true;
    }
}
